<?php

class ControllerBase extends Phalcon\Mvc\Controller
{
    protected $config;
    protected $facebook;
    protected $foursquare;
    protected $foursquareFactory;
    protected $twitter;

    protected function initialize()
    {
        Phalcon\Tag::prependTitle('NextSpot | ');

        // Load the configuration
        $this->config = new \Phalcon\Config\Adapter\Ini(__DIR__ . '/../config/config.ini');

        // Load all the tokens for the user, if applicable
        $tokens = null;
        if ($this->session->get('auth'))
        {
            $user = $this->session->get('auth');
            $userId = $user['id'];
            $tokenQuery = Tokens::find("user_id = '{$userId}'");
            foreach($tokenQuery as $token)
            {
                $tokens[$token->network] = $token->token;
            }
        }
        // Setup Facebook
        $config = array(
            'appId' => $this->config->facebook->appId,
            'secret' => $this->config->facebook->appSecret,
            'fileUpload' => false, // optional
            'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
        );
        $this->facebook = new Facebook($config);

        // Setup Foursquare
        $client = new \TheTwelve\Foursquare\HttpClient\CurlHttpClient(__DIR__ . '/../../vendor/haxx-se/curl/cacert.pem');
        $redirector = new \TheTwelve\Foursquare\Redirector\HeaderRedirector();

        $this->foursquareFactory = new \TheTwelve\Foursquare\ApiGatewayFactory($client, $redirector);

        // Required for most requests
        $this->foursquareFactory->setClientCredentials($this->config->foursquare->clientId, $this->config->foursquare->clientSecret);

        // Optional (only use these if you know what you're doing)
        $this->foursquareFactory->setEndpointUri('https://api.foursquare.com');
        $this->foursquareFactory->useVersion(2);
        if (isset($tokens['foursquare']))
        {
            $this->foursquareFactory->setToken($tokens['foursquare']);
        }
        $this->foursquare = $this->foursquareFactory->getAuthenticationGateway(
            'https://foursquare.com/oauth2/authorize',
            'https://foursquare.com/oauth2/access_token',
            $this->config->general->uri . $this->url->get('foursquare/callback')
        );

        // Setup Twitter
        $config = array(
            'consumer_key' => $this->config->twitter->apiKey,
            'consumer_secret' => $this->config->twitter->apiSecret,
            'oauth_token' => $this->config->twitter->accessToken,
            'oauth_token_secret' => $this->config->twitter->accessTokenSecret,
            'output_format' => 'object'
        );
        $this->twitter = new TwitterOAuth($config['consumer_key'], $config['consumer_secret']);
    }

    protected function forward($uri){
    	$uriParts = explode('/', $uri);
    	return $this->dispatcher->forward(
    		array(
    			'controller' => $uriParts[0], 
    			'action' => $uriParts[1]
    		)
    	);
    }

    protected function _processSocialSignin($uid, $attr, $twitterOauthToken = null, $twitterOauthTokenSecret = null)
    {
        // Check if the user is logged in
        $loggedIn = $this->session->get('auth');

        if ($loggedIn)
        {
            $user = Users::findFirst($loggedIn['id']);
        }
        else
        {
            $user = Users::findFirst("{$attr}='{$uid}'");
        }

        // If the user exists, sign them in
        if ($user != false) {
            $user->$attr = $uid;
            
            if ($user->id)
            {
                $user->updated_at = new Phalcon\Db\RawValue('now()');
            }
            else
            {
                $user->created_at = new Phalcon\Db\RawValue('now()');
            }

            if ($user->save() == false) {
                foreach ($user->getMessages() as $message) {
                    $this->flash->error((string) $message);
                }
            } else {

                $this->_authenticateUser($user, $attr, $twitterOauthToken, $twitterOauthTokenSecret);

                return $this->response->redirect();
            }

            // Save the session
            $this->session->set('auth', array(
                'id' => $user->id
            ));

            return $this->response->redirect();
        }
        // Register a new user
        else
        {
            $user = new Users();
            $user->$attr = $uid;
            if (!$loggedIn) $user->created_at = new Phalcon\Db\RawValue('now()');
            if ($user->save() == false) {
                foreach ($user->getMessages() as $message) {
                    $this->flash->error((string) $message);
                }
            } else {

                $this->_authenticateUser($user, $attr, $twitterOauthToken, $twitterOauthTokenSecret);

                $this->flash->success('Thanks for signing up!');
                return $this->response->redirect();
            }
        } 
    }

    protected function _authenticateUser($user, $network, $twitterOauthToken = null, $twitterOauthTokenSecret = null)
    {
        // Register the session
        $this->session->set('auth', array(
            'id' => $user->id
        ));

        // Set the new code
        $this->_setCode($network, $twitterOauthToken, $twitterOauthTokenSecret);
    }

    protected function _setCode($attr, $twitterOauthToken = null, $twitterOauthTokenSecret = null)
    {
        // Grab the code
        $code = $this->request->get('code');
        if ($code || ($twitterOauthToken && $twitterOauthTokenSecret))
        {
            $tokens = ($this->session->get('tokens')) ? $this->session->get('tokens') : array();
            $network = null;
            switch($attr)
            {
                case 'fb_uid' :
                    $network = 'facebook';
                    break;
                // case 'tw_uid' :
                //     $network = 'twitter';
                //     break;
                case 'ig_uid' :
                    $network = 'instagram';
                    break;
                case 'gp_uid' :
                    $network = 'gplus';
                    break;
                case 'fs_uid' :
                    $network = 'foursquare';
                    break;
            }

            if ($network)
            {
                $user = $this->session->get('auth');
                $userId = $user['id'];
                // Get a list of the users currently saved sessions
                $tokens = Tokens::find("user_id = '{$userId}' AND network = '{$network}'");
                if ($tokens)
                {
                    foreach($tokens as $token)
                    {
                        if ($token->network == $network)
                        {
                            $token->token = $code;
                            $token->updated_at = new Phalcon\Db\RawValue('now()');
                            $token->save();
                            return true;
                        }
                    }
                }

                // If we didn't already return, we need to create a new token
                $token = new Tokens();
                $token->user_id = $userId;
                $token->network = $network;
                $token->token = $code;
                $token->created_at = new Phalcon\Db\RawValue('now()');
                $token->save();
                
            }
            elseif ($twitterOauthToken && $twitterOauthTokenSecret)
            {
                $networks = array(
                    'twitter_oauth' => $twitterOauthToken,
                    'twitter_oauth_secret' => $twitterOauthTokenSecret
                );

                $user = $this->session->get('auth');
                $userId = $user['id'];
                
                $i = 0;
                foreach($networks as $network => $code)
                {
                    // Get a list of the users currently saved sessions
                    $tokens = Tokens::find("user_id = '{$userId}' AND network = '{$network}'");

                    foreach($tokens as $token)
                    {
                        if ($token->network == $network)
                        {
                            $token->token = $code;
                            $token->updated_at = new Phalcon\Db\RawValue('now()');
                            $token->save();
                            if ($i+1 == count($networks))
                                return true;
                            else
                                continue;
                        }
                    }   
                    ++$i;                 
                }
                // If we didn't return, we need to create a new one
                foreach($networks as $network => $code)
                {
                    $token = new Tokens();
                    $token->user_id = $userId;
                    $token->network = $network;
                    $token->token = $code;
                    $token->created_at = new Phalcon\Db\RawValue('now()');
                    $token->save();
                }
            }

            return true;
        }

        return false;
    }
}
