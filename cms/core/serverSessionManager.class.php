<?php

class serverSessionManager
{
    protected $sessionId;
    protected $sessionName;
    protected $sessionLifeTime;

    /**
     * @return string
     */
    public function getSessionName()
    {
        return $this->sessionName;
    }

    /**
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    protected $sessionsPath;
    /** @var serverSessionManager */
    private static $instance = null;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            $name = __CLASS__;
            self::$instance = new $name();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->sessionName = '';
        $pathsManager = controller::getInstance()->getPathsManager();
        $sessionsPath = $pathsManager->getPath('sessionsCache');
        if ($sessionsPath) {
            $this->sessionsPath = $sessionsPath;
        }
        self::$instance = $this;
    }

    public function setSessionLifeTime($lifetime)
    {
        $this->sessionLifeTime = $lifetime;
    }

    public function setSessionName($sessionName)
    {
        $this->sessionName = $sessionName;
    }

    public function startSession()
    {
        $sessionStarted = session_id();
        if ($sessionStarted == '') {
            session_name($this->sessionName);
            if (!is_null($this->sessionId)) {
                session_id($this->sessionId);
            }

            if (!is_null($this->sessionsPath)) {
                $pathsManager = controller::getInstance()->getPathsManager();
                $currentSessionPath = $this->sessionsPath . $this->sessionName . '/';
                $pathsManager->ensureDirectory($currentSessionPath);
                session_save_path($currentSessionPath);
            }
            if ($this->sessionLifeTime) {
                ini_set('session.gc_maxlifetime', $this->sessionLifeTime);
                session_set_cookie_params($this->sessionLifeTime);
            }

            session_start();
            if (is_null($this->sessionId)) {
                $this->sessionId = session_id();
            }
        }
    }

    public function close()
    {
        session_write_close();
    }
}