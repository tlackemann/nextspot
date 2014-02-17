<?php

class FoursquareController extends ControllerBase
{
	public function indexAction()
	{
		$this->foursquare->initiateLogin();
	}

	public function callbackAction()
	{
		if ($this->_setCode('foursquare'))
        {
			$token = $this->foursquare->authenticateUser($this->request->get('code'));
			$this->foursquareFactory->setToken($token);
			$gateway = $this->foursquareFactory->getUsersGateway();
			$user = $gateway->getUser();

			// Get the user id
            $uid = $user->id;

            if ($uid)
            {
                $this->_processSocialSignin($uid, 'fs_uid');               
            }
            else
            {
                $this->flash->error('Something went wrong when retrieving a Foursquare User ID');
                return $this->forward('index/index');
            }

        }
        else
        {
            $this->flash->error('Something went wrong when retrieving a Foursquare access token');
            return $this->forward('index/index');
        }
	}
}