<?php

class FacebookController extends ControllerBase
{
    /**
     * Redirect the user to the Facebook OAuth page
     */
    public function indexAction()
    {
        $this->response->redirect($this->facebook->getLoginUrl(array(
            'redirect_uri'  => $this->config->general->uri . $this->url->get('facebook/callback')
        )), true);
    }

    /**
     * Stores the code received by Facebook and redirects to the /users section
     * Redirects the user to the homepage on failure
     */
    public function callbackAction()
    {
        // Get the user id
        $uid = $this->facebook->getUser();

        if ($uid)
        {
            $this->_processSocialSignin($uid, 'fb_uid');               
        }
        else
        {
            $this->flash->error('Something went wrong when retrieving a Facebook User ID');
            return $this->forward('index/index');
        }

    }
}
