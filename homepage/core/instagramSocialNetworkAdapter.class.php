<?php

class instagramSocialNetworkAdapter extends SocialNetworkAdapter
{
    protected $appId;
    protected $secret;
    protected $token;
    protected $userId;
    protected $session;
    protected $api;
    protected $appOwnerId;
    protected $appToken;

    public function setCredentials(array $credentials)
    {
        $this->appId = $credentials['appId'];
        $this->secret = $credentials['appKey'];
    }

    public function authenticate($code)
    {

    }

    public function useAccessToken($token)
    {
        $this->getApi()->setDefaultAccessToken($token);
        $this->token = $token;
    }

    /**
     * Returns the URL of Facebook login page.
     * If an authorisation is required, then user can be redirected to this page,
     * authorised in FB, and then redirected back to $returnUrl with signed FB information request,
     * containing authorization info.
     *
     * @return bool|string
     */
    public function getAuthRedirectUrl()
    {
          return 'https://api.instagram.com/oauth/authorize/?client_id=' . $this->appId . '&redirect_uri=' . $this->authReturnUrl . '&response_type=code';
    }

    public function getSessionUserId()
    {
//        $user = $this->getUser();
//        return $user ? $user->getId() : 0;
    }
//
    public function getAuthorizationToken()
    {
//        $result = '';
//        $helper = $this->getApi()->getRedirectLoginHelper();
//        try {
//            $result = $helper->getAccessToken();
//        } catch (Facebook\Exceptions\FacebookResponseException $e) {
//            // When Graph returns an error
//            $this->logError($e->getCode() . ' ' . $e->getMessage());
//        } catch (Facebook\Exceptions\FacebookSDKException $e) {
//            // When validation fails or other local issues
//            echo 'Facebook SDK returned an error: ' . $e->getMessage();
//        }
//        return $result;
    }
//
    public function getAuthorizedUserName()
    {
//        $user = $this->getUser();
//        return $user ? $user->getName() : '';
    }
//
    public function getAuthorizedUserData()
    {
//        $result = null;
//        if ($user = $this->getUser()) {
//            $result = new SocialNetworkUserInfo();
//            $result->id = $user->getId();
//            $result->firstName = $user->getFirstName();
//            $result->lastName = $user->getLastName();
//            $result->email = $user->getEmail();
//        }
//        return $result;
    }
//
    protected function getApi()
    {
        if ($this->api === null) {
//            $this->api = new Facebook\Facebook([
//                'app_id'                => $this->appId,
//                'app_secret'            => $this->secret,
//                'default_graph_version' => 'v4.0',
//            ]);

//            $this->api = new InstagramAPI\Instagram(true, false);
//            $this->api->setUser($this->appId, $this->secret);
//
//            try {
//                $this->api->login();
//            } catch (Exception $e) {
//                echo "something went wrong ". $e->getMessage()."\n";
//                exit(0);
//            }
        }
        return $this->api;
    }
//
//    protected function getUser()
//    {
//        $result = null;
//        try {
//            $response = $this->getApi()->get('/me?fields=id,first_name,last_name,email');
//            $result = $response->getGraphUser();
//        } catch (Facebook\Exceptions\FacebookResponseException $e) {
//            // When Graph returns an error
//            $this->logError($e->getCode() . ' ' . $e->getMessage());
//        } catch (Facebook\Exceptions\FacebookSDKException $e) {
//            // When validation fails or other local issues
//            $this->logError($e->getCode() . ' ' . $e->getMessage());
//        }
//        return $result;
//    }

//    public function getOwnerId()
//    {
//        if (is_null($this->appOwnerId)) {
//            if ($response = $this->getApi()->get('/' . $this->appId . '?fields=creator_uid', $this->getAppToken())) {
//                if ($result = $response->getGraphNode()) {
//                    $this->appOwnerId = $result->getField('creator_uid');
//                }
//            }
//        }
//
//        return $this->appOwnerId;
//    }


    public function makePost($data)
    {

    }
}

