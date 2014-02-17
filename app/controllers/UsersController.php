<?php

class UsersController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setTemplateAfter('main');
        Phalcon\Tag::setTitle('Dashboard');
        parent::initialize();
    }

    public function indexAction()
    {
        $user = $this->session->get('auth');
        $userId = $user['id'];
    	$this->view->user = Users::findFirst($userId);
    	$this->view->tokens = $this->view->user->tokens;
    	
        // Get the stuff the users liked
        $likes = Likes::find(array(
            'conditions' => "user_id = '{$this->view->user->id}' AND positive = true ",
            'order' => 'created_at DESC'
        ));

        if ($likes->count())
        {  
            $this->view->likes = $likes;
            $placeIds = array();
            foreach($likes as $like)
            {
                $placeIds[] = $like->place_id;
            }
            $placeIdsString = implode(',', $placeIds);
            $places = Places::find(array(
                'conditions' => "id IN ({$placeIdsString})",
                'order' => 'id DESC'
            ));
            $this->view->places = $places;
        }
    }
}
