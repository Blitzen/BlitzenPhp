<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class BlitzenApiWrapperBase {
    protected $apiKey;
    protected $subdomain;
    protected $domain = 'blitzen.com';
    protected $client_id;
    protected $client_secret;
    protected $access_token;
    
    public function __construct(){
        $this->auth_url = 'http://localhost:8000/v1/o/token/';
    }
    
    public function getFullUrl(){
        return "https://$subdomain.$domain/api/v1/";
    }
    
}

class BlitzenCurl {
    public function __construct() {
        
    }
    
    public function authenticate($client_id, $client_secret, $username, $password){
        $full_url = $this->auth_url . "?grant_type=password&username=$username&password=$password";
        $this->curl = \curl_init($full_url);
        curl_setopt($this->curl, CURLOPT_USERPWD, "$client_id:$client_secret");
        $response = curl_exec($this->curl);
        echo $response;
    }
    
    public function getAuthenticated($url, $access_token){
        $this->curl = \curl_init($url);
        $this->setBasicCurlOptions($access_token);

        $response = curl_exec($this->curl);
        $this->setResultCodes();
        $this->checkForCurlErrors();
        $this->checkForGetErrors($response);
        curl_close($this->curl);
        return $response;
    }
    
    public function postAuthenticated($postParams, $url, $access_token) {
        $this->curl = curl_init($url); 
        $this->setBasicCurlOptions($access_token);

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-type: multipart/form-data', 'Expect:'));
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postParams);

        $response = curl_exec($this->curl);
        $this->setResultCodes();
        $this->checkForCurlErrors();
        $this->checkForPostErrors($response);
        curl_close($this->curl);
        return $response;
    }
    
    public function setBasicCurlOptions($access_token) {
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_USERAGENT, 'Blitzen API Wrapper');
        curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer $access_token"));
    }
    
    private function setResultCodes() {
        $this->ResultStatus = curl_getinfo($this->curl);		
    }
    
    private function checkForCurlErrors() {
        if(curl_errno($this->curl)) {
            throw new Exception(curl_error($this->curl), curl_errno($this->curl));
        }
    }
    
    private function checkForGetErrors($response) {
        switch ($this->ResultStatus['http_code']) {
            case 200:
                //ignore, this is good.
                break;
            case 401:
                throw new Exception('(401) Forbidden.  Check your API key.', 401);
                break;
            default:
                $this->throwResponseError($response);
                break;
        }
    }
    
    private function throwResponseError($response) {
        if ($response) {
            $obj = json_decode($response);
            throw new Exception('('.$obj->HTTPCode.') '.$obj->Text, $this->ResultStatus['HTTP_CODE']);
        } else {
            throw new Exception('(500) Internal server error.  Please contact support@blitzen.com', 500);
        }
        return $response;
    }
    
}

