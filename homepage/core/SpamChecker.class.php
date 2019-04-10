<?php

class SpamChecker
{
    /**
     * @var ConfigManager
     */
    protected $configManager;

    public function setConfigManager($configManager)
    {
        $this->configManager = $configManager;
    }

    public function checkEmail($email)
    {
        $email = trim($email);
        $result = true;
        $key = false;
        if ($config = $this->configManager->getConfig('emails')) {
            $key = $config->get('cleanTalkKey');
        }
        if ($key && function_exists('curl_init')) {
            $params = [
                'method_name' => 'spam_check',
                'auth_key' => $key,
                'email' => $email,
            ];

            $check = curl_init();
            curl_setopt($check, CURLOPT_URL, 'https://api.cleantalk.org/?' . http_build_query($params));
            curl_setopt($check, CURLOPT_TIMEOUT, 10);
            curl_setopt($check, CURLOPT_RETURNTRANSFER, true);
            // resolve 'Expect: 100-continue' issue
            curl_setopt($check, CURLOPT_HTTPHEADER, ['Expect:']);
            curl_setopt($check, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($check, CURLOPT_SSL_VERIFYHOST, false);

            if ($result = curl_exec($check)) {
                if ($data = json_decode($result, true)) {
                    if (isset($data['data']) && isset($data['data'][$email]) && isset($data['data'][$email]['appears'])) {
                        if ($data['data'][$email]['appears'] == 1) {
                            $result = false;
                        }
                    }
                }
            }

            curl_close($check);
        }

        return $result;
    }
}
