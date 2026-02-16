<?php

class BridgeInteractiveAPI {
    private $serverToken;
    private $cacheTime = 3600; // 1 hour
    private $cacheFile = 'mls_cache.json';

    public function __construct($token) {
        $this->serverToken = $token;
    }

    public function authenticate() {
        // In a real scenario, validate token presence and format here
        if (empty($this->serverToken)) {
            throw new Exception('Server token is required for authentication.');
        }
        return true;
    }

    public function fetchListings() {
        $this->authenticate();

        if ($this->isCacheValid()) {
            return json_decode(file_get_contents($this->cacheFile), true);
        } else {
            $listings = $this->fetchFromAPI();
            $this->cacheListings($listings);
            return $listings;
        }
    }

    private function fetchFromAPI() {
        $url = 'https://api.bridgeinteractive.com/mls/listings';
        $headers = [
            'Authorization: Bearer ' . $this->serverToken,
            'Content-Type: application/json'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Error fetching data: ' . curl_error($ch));
        }
        curl_close($ch);

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error decoding JSON response.');
        }
        return $data;
    }

    private function isCacheValid() {
        if (!file_exists($this->cacheFile)) {
            return false;
        }
        return (time() - filemtime($this->cacheFile)) < $this->cacheTime;
    }

    private function cacheListings($listings) {
        file_put_contents($this->cacheFile, json_encode($listings));
    }

    public function clearCache() {
        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }
    }
}

?>