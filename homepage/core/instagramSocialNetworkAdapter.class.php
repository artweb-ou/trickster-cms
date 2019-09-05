<?php

class instagramSocialNetworkAdapter extends SocialNetworkAdapter
{
    protected $appId;
    protected $secret;
    protected $token;
    protected $userId;
    protected $session;
    protected $api;

    public function setCredentials(array $credentials)
    {
        $this->appId = $credentials['appId'];
        $this->secret = $credentials['appKey'];
    }

    public function authenticate($code)
    {
        $data = $this->getApi()->getOAuthToken($code);
        $this->getApi()->setAccessToken($data);
    }

    public function useAccessToken($token)
    {
        $this->getApi()->setAccessToken($token);
        $this->token = $token;
    }

    public function getAuthRedirectUrl()
    {
        return $this->getApi()->getLoginUrl();
//          return 'https://api.instagram.com/oauth/authorize/?client_id=' . $this->appId . '&redirect_uri=' . $this->authReturnUrl . '&response_type=code';
    }

    public function getSessionUserId()
    {
        $user = $this->getUser();
        return $user ? $user->id : 0;
    }

    public function getAuthorizationToken()
    {
        return $this->getApi()->getAccessToken();
    }

    public function getAuthorizedUserData()
    {
        $result = null;
        if ($user = $this->getUser()) {
            $result = new SocialNetworkUserInfo();
            $result->id = $user->id;
            $result->firstName = $user->full_name;
            $result->lastName = '';
            $result->email = '';
        }
        return $result;
    }

    protected function getApi()
    {
        if ($this->api === null) {
            $this->api = new MetzWeb\Instagram\Instagram(array(
                'apiKey'      => $this->appId,
                'apiSecret'   => $this->secret,
                'apiCallback' => $this->authReturnUrl
            ));
        }
        return $this->api;
    }

    protected function getUser()
    {
        //errors log?
        return $this->getApi()->getUser();
    }

    public function makePost($data)
    {

    }
}

