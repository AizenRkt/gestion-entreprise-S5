<?php

namespace app\models;

use Flight;
use PDO;

class Status {
    private $apiUrl = "http://localhost/dolibarr-21.0.1/htdocs/api/index.php/";
    private $apiKey = "031aaad0efb7bdb8457212289c8cb7014bc1b770";

    public function getStatus()
    {
        $url = $this->apiUrl . "status";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "DOLAPIKEY: {$this->apiKey}"
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'code' => $http_code,
            'body' => json_decode($response, true)
        ];
    }

}
