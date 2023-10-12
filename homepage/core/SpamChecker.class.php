<?php

class SpamChecker
{
    protected $configManager;
    protected $db;

    public function setDb(\Illuminate\Database\Connection $db): void
    {
        $this->db = $db;
    }

    public function setConfigManager($configManager)
    {
        $this->configManager = $configManager;
    }

    public function checkEmail($email)
    {
        $email = trim($email);

        $address = explode('@', $email)[0];
        if (str_contains($address, '+')) {
            return false;
        }
        if (substr_count($address, '.') > 2) {
            return false;
        }

        $domain = explode('@', $email)[1];

        $domainRecord = $this->db->table('domains')->where('name', $domain)->first();
        if ($domainRecord) {
            return $domainRecord['allowed'];
        }
	
        $api_key = $this->configManager->getConfig('emails')->get('verifyEmailKey');
        $verifymail = "https://verifymail.io/api/$email?key=$api_key";
        $json = file_get_contents($verifymail);
        $data = json_decode($json, TRUE);

        if (isset($data['message'])) {
            return false;
        }

        $allowed = !$data['block'];
        $this->db->table('domains')->insert(['name' => $domain, 'allowed' => $allowed ? 1 : 0]);

        return $allowed;
    }
}
