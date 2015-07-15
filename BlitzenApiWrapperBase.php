<?php
/* Blitzen
 * Jordan Clark 2015
 */
class BlitzenApiWrapperBase {
    protected $auth_url = 'https://blitzen.com/api/v1/o/token/';
    
    public function __construct($client_id, $client_secret, $subdomain = Null, $access_token = Null, $refresh_token = Null){
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->access_token = $access_token;
        $this->refresh_token = $refresh_token;
        $this->subdomain = $subdomain;
    }
    
    public function getFullUrl($url){
        return "https://$this->subdomain.blitzen.com/api/v1/$url";
    }
    
    public function refreshToken(){
        $params = array(
            'refresh_token' => $this->refresh_token,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'refresh_token'
        );
        $this->curl = new BlitzenCurl();
        $response = $this->curl->postAuthenticated($params, $this->auth_url);
        $decoded = json_decode($response);
        $this->access_token = $decoded->access_token;
        $this->refresh_token = $decoded->refresh_token;
        return $response;
    }

    public function getHelper($url, $refreshed = False){
      try{
        $this->curl = new BlitzenCurl();
        $response = $this->curl->getAuthenticated($url, $this->access_token);
      } catch (Exception $ex) {
        if($refreshed === False){
          $this->refreshToken();
          $response = $this->getHelper($url, True);
        }
      }
      return $response;
      
    }
    
    public function postHelper($url, $params, $refreshed = False){
        try{
            $this->curl = new BlitzenCurl();
            $response = $this->curl->postAuthenticated($params, $url, $this->access_token);
        } catch (Exception $ex) {
          if($refreshed === False){
              $this->refreshToken();
              $response = $this->postHelper($url, $params, True);
          }
        }
        return $response;
    }

    public function authenticate($username, $password){
      $this->curl = new BlitzenCurl();
      $response = $this->curl->authenticate($this->auth_url, $this->client_id, $this->client_secret, $username, $password);
      $decoded = json_decode($response);
      $this->access_token = $decoded->access_token;
      $this->refresh_token = $decoded->refresh_token;
      return $response;
    }
    
}

class BlitzenCurl {
    public function __construct() {
        
    }
    
    public function authenticate($auth_url, $client_id, $client_secret, $username, $password){
        $full_url = "$auth_url?grant_type=password";
        $postParams = Array("username"=>$username,
                            "password"=>$password);
        $this->curl = \curl_init($full_url);
        curl_setopt($this->curl, CURLOPT_USERPWD, "$client_id:$client_secret");
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postParams);
        $response = curl_exec($this->curl);
        curl_close($this->curl);
        return $response;
    }
    
    public function getAuthenticated($url, $access_token){
        $this->curl = \curl_init($url);
        $this->setBasicCurlOptions($access_token);
        $response = curl_exec($this->curl);
        $this->setResultCodes();
        $this->checkForCurlErrors();
        $this->checkForErrors($response);
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
        $this->checkForErrors($response);
        curl_close($this->curl);
        return $response;
    }
    
    public function setBasicCurlOptions($access_token) {
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, true);
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
    
    private function checkForErrors($response) {
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
        $response = json_decode($response);
        if ($response) {
            throw new Exception('('.$response->HTTPCode.') '.$response->Text, $this->ResultStatus['HTTP_CODE']);
        } else {
            throw new Exception('(500) Internal server error.  Please contact support@blitzen.com', 500);
        }
        return $response;
    }
}
