<?php

class facebookSocialNetworkAdapter extends SocialNetworkAdapter
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
        // done behind the scenes by FB api
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
        $helper = $this->getApi()->getRedirectLoginHelper();
        $permissions = ['public_profile', 'email']; // optional
        return $helper->getLoginUrl($this->authReturnUrl, $permissions);
    }

    public function getSessionUserId()
    {
        $user = $this->getUser();
        return $user ? $user->getId() : 0;
    }

    public function getAuthorizationToken()
    {
        $result = '';
        $helper = $this->getApi()->getRedirectLoginHelper();
        try {
            $result = $helper->getAccessToken();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            $this->logError($e->getCode() . ' ' . $e->getMessage());
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
        }
        return $result;
    }

    public function getAuthorizedUserName()
    {
        $user = $this->getUser();
        return $user ? $user->getName() : '';
    }

    public function getAuthorizedUserData()
    {
        $result = null;
        if ($user = $this->getUser()) {
            $result = new SocialNetworkUserInfo();
            $result->id = $user->getId();
            $result->firstName = $user->getFirstName();
            $result->lastName = $user->getLastName();
            $result->email = $user->getEmail();
        }
        return $result;
    }

    protected function getApi()
    {
        if ($this->api === null) {
            $this->api = new Facebook\Facebook([
                'app_id'                => $this->appId,
                'app_secret'            => $this->secret,
                'default_graph_version' => 'v4.0',
            ]);
        }
        return $this->api;
    }

    protected function getUser()
    {
        $result = null;
        try {
            $response = $this->getApi()->get('/me?fields=id,first_name,last_name,email');
            $result = $response->getGraphUser();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            $this->logError($e->getCode() . ' ' . $e->getMessage());
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            $this->logError($e->getCode() . ' ' . $e->getMessage());
        }
        return $result;
    }

    public function getOwnerId()
    {
        if (is_null($this->appOwnerId)) {
            if ($response = $this->getApi()->get('/' . $this->appId . '?fields=creator_uid', $this->getAppToken())) {
                if ($result = $response->getGraphNode()) {
                    $this->appOwnerId = $result->getField('creator_uid');
                }
            }
        }

        return $this->appOwnerId;
    }

    public function getAppToken()
    {
        if (is_null($this->appToken)) {
            $this->appToken = $this->getApi()->getApp()->getAccessToken();
        }

        return $this->appToken;
    }

    public function getPages()
    {
        if ($currentUserData = $this->getAuthorizedUserData()) {
            if ($currentUserData->id == $this->getOwnerId()) {
                $result = [];
                try {
                    $response = $this->getApi()->get('/me/accounts?type=page');
                    if ($pages = $response->getGraphEdge()) {
                        foreach ($pages as $page) {
                            $result[] = [
                                'socialId' => $page->getField('id'),
                                'title'    => $page->getField('name'),
                                'access_token'    => $page->getField('access_token'),
                            ];
                        }
                    }
                } catch (Facebook\Exceptions\FacebookResponseException $e) {
                    // When Graph returns an error
                    $this->logError($e->getCode() . ' ' . $e->getMessage());
                    return false;
                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                    // When validation fails or other local issues
                    $this->logError($e->getCode() . ' ' . $e->getMessage());
                    return false;
                }
                return $result;
            } else {
                return new SocialErrorMessage('1');
            }
        } else {
            return new SocialErrorMessage('2');
        }
    }

    protected function getPageToken($pageSocialId) {
        $this->getApi()->get('/' . (int)$pageSocialId . '?fields=access_token');
    }

    public function makePost($data)
    {
        if ($data['pagesSocialIds']) {
            $pages = $this->getPages();
            foreach ($data['pagesSocialIds'] as $socialId) {
                $pageToken = '';
                foreach($pages as $page) {
                    if($page['socialId'] == $socialId) {
                        $pageToken = $page['access_token'];
                    }
                }
                try {
                    $response = $this->getApi()->post(
                        '/' . $socialId . '/feed',
                        array(
                            'message' => $data['message'],
                        ),
                        $pageToken
                    );
                    $result = $response->getGraphNode();
                    return $result->getField('id');
                } catch (Facebook\Exceptions\FacebookResponseException $e) {
                    // When Graph returns an error
                    $this->logError($e->getCode() . ' ' . $e->getMessage());
                    return false;
                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                    // When validation fails or other local issues
                    $this->logError($e->getCode() . ' ' . $e->getMessage());
                    return false;
                }
            }
        }
        return false;
    }
}

