<?php

class TwitterController extends ControllerBase
{
    public function indexAction()
    {
        $credentials = $this->twitter->getRequestToken($this->config->general->uri . $this->url->get('twitter/callback'));
        $uri = $this->twitter->getAuthorizeURL($credentials);
        $this->response->redirect($uri, true);
    }
    
    /**
     * Stores the code received by Twitter and redirects to the /users section
     * Redirects the user to the homepage on failure
     */
    public function callbackAction()
    {
        // Get the access token
        $this->twitter = new TwitterOAuth($this->config->twitter->apiKey, $this->config->twitter->apiSecret, $this->request->get('oauth_token'),
$this->request->get('oauth_verifier'));
        $token_credentials = $this->twitter->getAccessToken($this->request->get('oauth_verifier'));
        $this->twitter = new TwitterOAuth($this->config->twitter->apiKey, $this->config->twitter->apiSecret, $token_credentials['oauth_token'],
$token_credentials['oauth_token_secret']);
        $account = $this->twitter->get('account/verify_credentials');
        
        // Get the user id
        $uid = $account->id;

        if ($uid)
        {
            $this->_processSocialSignin($uid, 'tw_uid', $token_credentials['oauth_token'], $token_credentials['oauth_token_secret']);               
        }
        else
        {
            $this->flash->error('Something went wrong when retrieving a Facebook User ID');
            return $this->forward('index/index');
        }

    }
}
