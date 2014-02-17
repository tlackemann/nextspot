<?php

class LikesController extends ControllerBase
{
    public function indexAction()
    {
        $uniqId = $this->request->get('id');

        $place = Places::findFirst("uniq_id = '{$uniqId}'");
        $auth = $this->session->get('auth');

        if ($place->id && isset($auth['id']))
        {
        	$userId = $auth['id'];

        	$like = new Likes();
        	$like->user_id = $userId;
        	$like->place_id = $place->id;
        	$like->created_at = new Phalcon\Db\RawValue('now()');
        	$like->positive = true;
        	if ($like->save() != false)
        	{
        		$response = array(
        			'success' => true,
        			'message' => 'Great! Next time we\'ll recommend something else'
        		);
        	}
        	else
        	{
        		$response = array(
        			'success' => false,
        			'message' => 'Something went wrong while liking this place, please try again'
        		);
        	}
	        
        	$this->_sendJson($response);
        }
    }

    public function dislikeAction()
    {
        $uniqId = $this->request->get('id');

        $place = Places::findFirst("uniq_id = '{$uniqId}'");
        $auth = $this->session->get('auth');

        if ($place->id && isset($auth['id']))
        {
        	$userId = $auth['id'];

        	$like = new Likes();
        	$like->user_id = $userId;
        	$like->place_id = $place->id;
        	$like->created_at = new Phalcon\Db\RawValue('now()');
        	$like->positive = false;
        	if ($like->save() != false)
        	{
        		$response = array(
        			'success' => true,
        			'message' => 'Hang tight! We\'re searching for something else!'
        		);
        	}
        	else
        	{
        		$response = array(
        			'success' => false,
        			'message' => 'Something went wrong while finding another place, please try again'
        		);
        	}
	        
        	$this->_sendJson($response);
        }
    }

    protected function _sendJson($response)
    {
    	$this->view->disable();
    	echo json_encode($response);
    }

}
