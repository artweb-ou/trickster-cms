<?php

class socialApplication extends controllerApplication
{
    protected $applicationName = 'social';
    protected $sessionKey;
    protected $sessionType = '';
    protected $concertsIdString;
    protected $action;
    protected $socialPlugin;
    protected $accessToken;
    protected $socialId;
    protected $returnUrl;
    protected $pluginElement;
    protected $pluginApi;
    protected $pluginId;
    protected $socialDataManager;
    public $rendererName = 'smarty';

    public function initialize()
    {
        $this->startSession('public');
        $this->createRenderer();
        $this->getService('structureManager')->setPrivilegeChecking(false);
        $this->parseRequestParameters();
        $this->socialDataManager = $this->getService('SocialDataManager');
    }

    public function execute($controller)
    {
        if ($this->pluginId) {
            $structureManager = $this->getService('structureManager');
            $this->pluginElement = $structureManager->getElementById($this->pluginId);
        }
        if ($this->pluginElement) {
            $this->pluginApi = $this->pluginElement->getApi();
            $this->pluginApi->setAuthReturnUrl($controller->baseURL . 'social/action:auth/');
        }
        $methodExists = method_exists($this, 'action_' . $this->action);
        if ($this->action && ($this->action === 'auth' || $this->returnUrl) && $this->pluginApi
            && $methodExists
        ) {
            call_user_func_array([
                $this,
                'action_' . $this->action,
            ], []);
        } elseif (!$methodExists) {
            $this->renderer->fileNotFound();
        } else {
            // no plugin in the URL or session - user is probing or doesn't allow storing session cookie
            $user = $this->getService('user');
            $user->setStorageAttribute('socialActionSuccess', false);
            $user->setStorageAttribute('socialActionMessage', $this->translate('user.social_auth_failure'));
            $this->redirect($controller->baseURL);
        }
    }

    protected function action_disconnect()
    {
        $user = $this->getService('user');
        $currentUserId = $user->userName !== 'anonymous' ? (int)$user->readUserId() : 0;
        if ($currentUserId > 0) {
            $this->socialDataManager->removeSocialUser($currentUserId, $this->pluginElement->getName());
        }
        $user->setStorageAttribute('lastSocialAction', $this->action);
        $this->redirect($this->returnUrl);
    }

    protected function action_connect()
    {
        $this->performAuthorization();

        $socialActionSuccess = false;
        $socialActionMessage = '';
        $apiType = $this->pluginElement->getName();
        $userId = $this->socialDataManager->getCmsUserId($apiType, $this->socialId);
        $user = $this->getService('user');
        if ($userId === 0) {
            $currentUserId = $user->userName !== 'anonymous' ? (int)$user->readUserId() : 0;
            if ($currentUserId > 0) {
                //user connects account to existing
                $this->socialDataManager->addSocialUser($apiType, $this->socialId, $currentUserId);
                $socialActionSuccess = true;
                $socialActionMessage = $this->translate('user.social_network_connected');
            }
        } else {
            $socialActionMessage = $this->translate('user.social_connection_existing');
        }
        if ($socialActionSuccess === false) {
            $socialActionMessage = $this->translate('user.social_connect_fail');
        }
        // account already exists for this social user
        $user->setStorageAttribute('lastSocialAction', $this->action);
        $user->setStorageAttribute('socialActionSuccess', $socialActionSuccess);
        $user->setStorageAttribute('socialActionMessage', $socialActionMessage);
        $this->storeAccessTokenInSession('');
        $this->redirect($this->returnUrl);
    }

    protected function action_login()
    {
        $this->performAuthorization();

        $socialActionSuccess = false;
        $apiType = $this->pluginElement->getName();
        $userId = $this->socialDataManager->getCmsUserId($apiType, $this->socialId);
        $user = $this->getService('user');
        if (!$userId) {
            $socialData = $this->pluginApi->getAuthorizedUserData();
            if ($socialData && $socialData->email) {
                $userCmsData = $user->queryUserData(['email' => $socialData->email]);
                if ($userCmsData) {
                    $userId = $userCmsData->id;
                } else {
                    $userElement = $this->createNewUserFromSocialData($socialData);
                    $userId = $userElement->id;
                }
                if ($userId) {
                    $this->socialDataManager->addSocialUser($apiType, $this->socialId, $userId);
                }
            } else {
                errorLog::getInstance()
                    ->logMessage('', 'Social login failed, insufficient social data');
            }
        }
        if ($userId) {
            $user->switchUser($userId, true);
            $socialActionSuccess = true;
            $socialActionMessage = $this->translate('user.social_login_success');
        } else {
            $socialActionMessage = $this->translate('user.social_login_fail');
        }
        $user->setStorageAttribute('lastSocialAction', $this->action);
        $user->setStorageAttribute('socialActionSuccess', $socialActionSuccess);
        $user->setStorageAttribute('socialActionMessage', $socialActionMessage);
        $this->storeAccessTokenInSession('');
        $this->redirect($this->returnUrl);
    }

    protected function action_auth()
    {
        if (!isset($_GET['code'])) {
            $controller = controller::getInstance();
            if ($url = $this->pluginApi->getAuthRedirectUrl()) {
                $controller->redirect($url, '302');
            }
        } else {
            $this->pluginApi->authenticate($_GET['code']);
            $token = $this->pluginApi->getAuthorizationToken();
            $this->storeAccessTokenInSession($token);
            $user = $this->getService('user');
            $authCallBackUrl = $user->getStorageAttribute('authCallBackUrl');
            $this->redirect($authCallBackUrl);
        }
    }

    protected function action_updatePages()
    {
        $this->performAuthorization();//???
        $pages = $this->pluginApi->getPages();
        if($pages !== false) {
            if($pages instanceof SocialErrorMessage) {
                $this->redirect($this->returnUrl . (strpos($this->returnUrl, '?') !== false ? '&' : '?') . 'social_error=' . $pages->messageCode);
            }else {
                $this->redirect($this->returnUrl . (strpos($this->returnUrl, '?') !== false ? '&' : '?') . 'pagesdata=' . urlencode(serialize($pages)));
            }
        }else {
            //???
        }
    }

    protected function performAuthorization()
    {
        $this->accessToken = $this->getAccessTokenFromSession();

        if ($this->accessToken) {
            $this->pluginApi->useAccessToken($this->accessToken);
            $this->socialId = $this->pluginApi->getSessionUserId();
            if (!$this->socialId) {
                errorLog::getInstance()
                    ->logMessage("", "Attempt to login with zero facebook user id \r\n"
                        . "\r\n\$_REQUEST = " . var_export($_REQUEST, true) . ';'
                        . "\r\n\$_SESSION = " . var_export($_SESSION, true) . ';'
                        . "\r\n\$_COOKIE = " . var_export($_COOKIE, true) . ';');
                $user = $this->getService('user');
                $user->setStorageAttribute('lastSocialAction', $this->action);
                $user->setStorageAttribute('socialActionSuccess', false);
                $user->setStorageAttribute('socialActionMessage', $this->translate('user.social_auth_failure'));
                $this->redirect($this->returnUrl);
            }
        } else {
            $controller = controller::getInstance();
            $uri = $_SERVER['REQUEST_URI'];
            if (substr($uri, 0, 1) === '/') {
                $uri = substr($uri, 1);
            }
            $returnUrl = $controller->baseURL . $uri;
            $user = $this->getService('user');
            $user->setStorageAttribute('lastSocialPlugin', $this->pluginElement->id);
            $user->setStorageAttribute('authCallBackUrl', $returnUrl);
            $this->redirect($controller->baseURL . 'social/action:auth/');
        }
    }

    protected function createNewUserFromSocialData($socialData)
    {
        $structureManager = $this->getService('structureManager');
        $checkedBefore = $structureManager->getPrivilegeChecking();
        $structureManager->setPrivilegeChecking(false);
        $usersElementId = $structureManager->getElementIdByMarker("users");
        $element = $structureManager->createElement('user', 'show', $usersElementId);
        if ($element) {
            $element->getId();
            $data = [
                'userName' => $socialData->email,
                'email' => $socialData->email,
                'firstName' => $socialData->firstName,
                'lastName' => $socialData->lastName,
            ];
            $element->importExternalData($data, array_keys($data));
            $element->persistElementData();
            $groups = [];
            $group = $structureManager->getElementIdByMarker('userGroup-authorized');
            $groups[] = $group;
            $group = $structureManager->getElementIdByMarker('userGroup-public');
            $groups[] = $group;
            foreach ($groups as $group) {
                if (!$group) {
                    continue;
                }
                $linksManager = $this->getService('linksManager');
                $linksManager->linkElements($group, $element->id, 'userRelation');
            }
        }
        $structureManager->setPrivilegeChecking($checkedBefore);
        return $element;
    }

    protected function parseRequestParameters()
    {
        $controller = $this->getService('controller');
        $this->action = $controller->getParameter('action');
        if ($this->action == 'auth') {
            $user = $this->getService('user');
            $this->pluginId = $user->getStorageAttribute('lastSocialPlugin');
        } else {
            $this->pluginId = $controller->getParameter('plugin');
        }
        $this->returnUrl = $controller->getParameter('return');
    }

    protected function redirect($target)
    {
        $controller = $this->getService('controller');
        $target = $target ?: $controller->baseURL;
        $controller->redirect($target);
    }

    protected function translate($code)
    {
        return $this->getService('translationsManager')
            ->getTranslationByName($code, 'public_translations', null, false, false);
    }

    protected function storeAccessTokenInSession($token)
    {
        $tokenKey = get_class($this->pluginApi) . '_access_token';
        $this->getService('user')->setStorageAttribute($tokenKey, $token);
    }

    protected function getAccessTokenFromSession()
    {
        $tokenKey = get_class($this->pluginApi) . '_access_token';
        return $this->getService('user')->getStorageAttribute($tokenKey);
    }

    //todo: move to element action? privileges doesnot check
    protected function action_publish()
    {
        $this->performAuthorization();
        $structureManager = $this->getService('structureManager');
        $controller = controller::getInstance();
        if ($socialPostId = $controller->getParameter('socialPostId')) {
            if ($socialPostElement = $structureManager->getElementById($socialPostId)) {
                $info = [];
                if ($socialPostElement->message) {
                    $info['message'] = $socialPostElement->message;
                }
                if ($socialPostElement->linkTitle) {
                    $info['linkTitle'] = $socialPostElement->linkTitle;
                }
                if ($socialPostElement->linkTitle) {
                    $info['linkDescription'] = $socialPostElement->linkDescription;
                }
                if ($socialPostElement->linkURL) {
                    $info['linkURL'] = $socialPostElement->linkURL;
                }
                if ($socialPostElement->originalName) {
                    $info['image'] = $controller->baseURL . 'image/type:socialImage/id:' . $socialPostElement->image . '/filename:' . $socialPostElement->originalName;
                }
                $info['pagesSocialIds'] = [];
                if($pagesIds = $controller->getParameter('pages')) {
                    foreach($pagesIds as $pageId) {
                        $page = $structureManager->getElementById($pageId);
                        $info['pagesSocialIds'][] = $page->socialId;
                    }
                }

                if ($this->pluginApi->makePost($info)) {
                    $socialPostElement->updateStatusInfo($this->pluginId, 'success', $pagesIds);
                } else {
                    $socialPostElement->updateStatusInfo($this->pluginId, 'error', $pagesIds);
                }
            }
        }
        $this->redirect($this->returnUrl);
    }
}

