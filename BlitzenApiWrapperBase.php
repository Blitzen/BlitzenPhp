<?php
/* Blitzen
 * Jordan Clark 2015
 */
class BlitzenApiWrapperBase {
    //Testing URLs
    //protected $auth_url = 'http://localhost:8000/v1/o/token/';
    protected $auth_url = 'http://blitzen.blitzen.localhost/api/v1/o/token/';
    //protected $auth_url = 'https://blitzen.com/v1/o/token/';
    
    public function __construct($client_id, $client_secret, $subdomain = Null, $access_token = Null, $refresh_token = Null){
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->access_token = $access_token;
        $this->refresh_token = $refresh_token;
        $this->subdomain = $subdomain;
    }
    
    public function getFullUrl($url){
        //Testing URL
        return "http://blitzen.blitzen.localhost/api/v1/$url/";
        //return "http://localhost:8000/v1/$url/";
        //return "https://$this->subdomain.$this->domain/api/v1/$url";
        
    }

    public function getHelper($url){
      $this->curl = new BlitzenCurl();
      return $this->curl->getAuthenticated($url, $this->access_token);
    }

    public function authenticate($username, $password){
      $this->curl = new BlitzenCurl();
      $response = $this->curl->authenticate($this->auth_url, $this->client_id, $this->client_secret, $username, $password);
      $this->access_token = $response->access_token;
      $this->refresh_token = $response->refresh_token;
      return $response;
    }
    
}

class BlitzenCurl {
    public function __construct() {
        
    }
    
    public function authenticate($auth_url, $client_id, $client_secret, $username, $password){
        $full_url = "$auth_url?grant_type=password&username=$username&password=$password";
        $this->curl = \curl_init($full_url);
        curl_setopt($this->curl, CURLOPT_USERPWD, "$client_id:$client_secret");
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_POST, true);
        $response = curl_exec($this->curl);
        $json_response = json_decode($response);
        curl_close($this->curl);
        return $json_response;
    }
    
    public function getAuthenticated($url, $access_token){
        $this->curl = \curl_init($url);
        $this->setBasicCurlOptions($access_token);
        $response = json_decode(curl_exec($this->curl));
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
                throw new Exception("(401) Forbidden.  $response->error", 401);
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