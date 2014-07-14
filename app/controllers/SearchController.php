<?php

class SearchController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setTemplateAfter('main');
        Phalcon\Tag::setTitle('Search');
        parent::initialize();
    }

    public function indexAction()
    {

        $_placeModel = Places::findFirst("id = 1");
        // foreach($_placeModel->placecategories as $cat)
        // {
        //     foreach($cat->categories as $c)
        //     {
        //         #var_dump($c->name);
        //     }
        // }
        
        // Get a list of categories
        $_categoriesCollection = Categories::find();
        $_categories = array();
        foreach($_categoriesCollection as $_category)
        {
            $_categories[$_category->code] = $_category;
        }

        // Get the user
        $_user = $this->session->get('auth');
        $_userId = $_user['id'];

        // Get a list of likes from this user
        $_likes = Likes::find("user_id = '{$_userId}'");

        $likedPlaces = array();
        $dislikedPlaces = array();
        foreach($_likes as $like)
        {
            if ($like->positive)
            {
                if (!isset($likedPlaces[$like->place_id])) $likedPlaces[$like->place_id] = 0;
                $likedPlaces[$like->place_id]++;
            }
            else
            {
                if (!isset($dislikedPlaces[$like->place_id])) $dislikedPlaces[$like->place_id] = 0;
                $dislikedPlaces[$like->place_id]++;
            }
        }

        // sort the liked places, if the place recommended is high on the list, we'll ignore it and recommend the second
        if (!empty($likedPlaces)) asort($likedPlaces);

    	// Hit the foursquare server to get places related to our search
        $term = $this->request->get('place');
        $address = $this->request->get('address');
        $lat = $this->request->get('lat');
    	$lng = $this->request->get('lng');

        $gateway = $this->foursquareFactory->getVenuesGateway();
        $groups = $gateway->explore(array(
            'll' => $lat.','.$lng,
            #'near' => $term,
            'query' => $term,
            'radius' => 1000
        ));

        $associatedCategories = array(
            'bar' => array(
                'bar'
            ),
            'club' => array(
                'club',
                'stripclub'
            ),
            'restaurant' => array(
                'restaurant'
            ),
            'pub' => array(
                'pub'
            )
        );
        $_uniqIds = array();
        $_placesCollection = Places::find();
        foreach($_placesCollection as $_place)
        {
            $_uniqIds[$_place->uniq_id] = $_place->id;
        }

        // We need to store new bars into our database and figure out what's the best place
        // Step 1: Sort by "checkins" or "hereNow"
        // Step 2: Filter by "likes" and "rating"
        // Step 3: Choose the closest fit

        $group = array_shift($groups);

        // Each factor will have a certain "weight"
        // The place with the highest "weight" will be returned the user
        $recommendationPoints = array();

        foreach($group->items as $venueItem)
        {
            $venue = $venueItem->venue;
            $points = 0;

            // For now, add these places to the database and we'll do stuff with them later
            if (!isset($_uniqIds[$venue->id]))
            {
                $_placeModel = new Places();
                $_placeModel->name = $venue->name;
                $_placeModel->uniq_id = $venue->id;
                $_placeModel->address = ($venue->location) ? $venue->location->address : null;
                $_placeModel->city = ($venue->location) ? $venue->location->city : null;
                $_placeModel->state = ($venue->location) ? $venue->location->state : null;
                $_placeModel->zip = ($venue->location) ? $venue->location->postalCode : null;
                $_placeModel->lat = $lat;
                $_placeModel->lng = $lng;
                $_placeModel->created_at = new Phalcon\Db\RawValue('now()');
                $_placeModel->save();

                // Create the categories
                if ($venue->categories)
                {   
                    // Get the current categories of the place
                    $_placeCategoriesCollection = $_placeModel->placecategories;
                    $_placeCategories = array();
                    foreach($_placeCategoriesCollection as $_placeCategory)
                    {
                        $_placeCategories[$_placeCategory->category_id] = $_placeCategory;
                    }

                    foreach($venue->categories as $_category)
                    {
                        // Add the new category to our table
                        if (!isset($_categories[strtolower($_category->shortName)]))
                        {
                            $_categoryModel = new Categories();
                            $_categoryModel->parent_id = $_categories[strtolower($term)]->id; // main categories will always exist
                            $_categoryModel->code = strtolower($_category->shortName);
                            $_categoryModel->name = $_category->name;
                            $_categoryModel->created_at = new Phalcon\Db\RawValue('now()');
                            $_categoryModel->save();

                            // add the newly created model to the existing array
                            $_categories[strtolower($_category->shortName)] = $_categoryModel;
                        }

                        // Add any categories not currently assigned to the place
                        if (!isset($_placeCategories[$_categories[strtolower($_category->shortName)]->id]))
                        {
                            $_categoryModel = $_categories[strtolower($_category->shortName)];
                            $_placeCategoryModel = new PlaceCategories();
                            $_placeCategoryModel->category_id = $_categoryModel->id;
                            $_placeCategoryModel->place_id = $_placeModel->id;
                            $_placeCategoryModel->created_at = new Phalcon\Db\RawValue('now()');
                            $_placeCategoryModel->save();
                        }

                        
                    }

                    

                }
            }
            else
            {
                $_placeModel = Places::findFirst("uniq_id = '{$venue->id}'");
            }

            if (!isset($likedPlaces[$_placeModel->id]) && !isset($dislikedPlaces[$_placeModel->id]))
            {
                // If the category matches, weigh more
                if (@$venue->categories[0]->name)
                {
                    $category = str_replace(' ', '', strtolower($venue->categories[0]->name));

                    if (in_array($category, $associatedCategories[$term]))
                    {
                        $points = $points + 5000;
                    }
                }
                $points = (@$venue->hours->isOpen) ? $points + 1000 : $points; // if we're open, weigh more
                $points = $points + @$venue->stats->checkinsCount;
                $points = $points + @$venue->links->count;
                $points = $points - @$venue->location->distance; // the closer we are, the more this is valued
                $points = $points + ((@$venue->rating) ? @$venue->rating * 10 : 0);
                $points = $points + ((@$venue->hereNow->count) ? @$venue->hereNow->count * 1000 : 0);

                $recommendationPoints[$venue->id] = $points;
                // hereNow: $venue->venue->hereNow->count
                // isOpen: $venue->venue->hours->isOpen
                // distance: $venue->venue->location->distance
                // rating: $venue->venue->rating
                // likes: $venue->venue->likes->count
                // lifetime checkins: $venue->venue->stats->checkinsCount
            }
        }

        // Sort the recommendations
        $highestNumber = 0;
        $recommendationUniqId = null;
        foreach($recommendationPoints as $uniqId => $points)
        {
            if ($points > $highestNumber)
            {
                $highestNumber = $points;
                $recommendationUniqId = $uniqId;
            }
        }

        foreach($group->items as $venueItem)
        {
            $venue = $venueItem->venue;

            if ($venue->id == $recommendationUniqId)
            {
                $_placeModel = $venue;
                $this->view->place = $_placeModel;
                $this->view->term = $term;
                $this->view->address = $address;
                break;
            }
        }

        #var_dump($_placeModel);exit;
        
    }
}
