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
        $user = $this->session->get('auth');
        $userId = $user['id'];

        // Get a list of likes from this user
        $likes = Likes::find("user_id = '{$userId}'");

        $likedPlaces = array();
        $dislikedPlaces = array();
        foreach($likes as $like)
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
        $uniqIds = array();
        $places = Places::find();
        foreach($places as $place)
        {
            $uniqIds[$place->uniq_id] = $place->id;
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
            if (!isset($uniqIds[$venue->id]))
            {
                $place = new Places();
                $place->name = $venue->name;
                $place->uniq_id = $venue->id;
                $place->address = $venue->location->address;
                $place->city = $venue->location->city;
                $place->state = $venue->location->state;
                $place->zip = $venue->location->postalCode;
                $place->lat = $lat;
                $place->lng = $lng;
                $place->created_at = new Phalcon\Db\RawValue('now()');
                $place->save();
            }
            else
            {
                $place = Places::findFirst("uniq_id = '{$venue->id}'");
            }

            if (!isset($likedPlaces[$place->id]) && !isset($dislikedPlaces[$place->id]))
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
                $place = $venue;
                $this->view->place = $place;
                $this->view->term = $term;
                $this->view->address = $address;
                break;
            }
        }

        #var_dump($place);exit;
        
    }
}
