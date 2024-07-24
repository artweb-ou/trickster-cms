<?php

use Illuminate\Database\Connection;

class SpamChecker
{
    protected ConfigManager $configManager;
    protected Connection $db;

    public function setDb(Connection $db): void
    {
        $this->db = $db;
    }

    public function setConfigManager(ConfigManager $configManager): void
    {
        $this->configManager = $configManager;
    }

    /**
     * @throws JsonException
     */
    public function checkEmail(string $email): bool
    {
        $email = trim($email);

        $address = explode('@', $email)[0];
        if (str_contains($address, '+') || substr_count($address, '.') > 2) {
            return false;
        }

        $domain = explode('@', $email)[1];

        $domainRecord = $this->db->table('domains')->where('name', $domain)->first();
        if ($domainRecord) {
            return (bool)$domainRecord['allowed'];
        }

        $api_key = $this->configManager->getConfig('emails')->get('verifyEmailKey');
        $verifymail = "https://verifymail.io/api/$email?key=$api_key";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_URL, $verifymail);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:127.0) Gecko/20100101 Firefox/127.0');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $json = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (isset($data['message'])) {
            return false;
        }

        $allowed = !$data['block'];
        $this->db->table('domains')->insert(['name' => $domain, 'allowed' => $allowed ? 1 : 0]);

        return $allowed;
    }
}
