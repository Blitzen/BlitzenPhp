<?php
/* Blitzen
 * Jordan Clark 2015
 */
require_once('BlitzenApiWrapperBase.php');

class BlitzenApiWrapper extends BlitzenApiWrapperBase {
  public function __construct($client_id, $client_secret, $subdomain, $access_token, $refresh_token){
    parent::__construct($client_id, $client_secret, $subdomain, $access_token, $refresh_token);
  }
  
  public function setAccessToken($access_token){
      $this->access_token = $access_token;
  }
  
  public function getForms(){
      return $this->getHelper($this->getFullUrl('forms'));
  }
  
  public function getForm($formId){
      return $this->getHelper($this->getFullUrl("forms/$formId"));
  }
  
  public function getFormFields($formId){
      return $this->getHelper($this->getFullUrl("forms/$formId/fields"));
  }
  
  public function getUserStats(){
      return $this->getHelper($this->getFullUrl("auth/stats"));
  }
  
  public function getContacts(){
      return $this->getHelper($this->getFullUrl("contacts"));
  }
  
  public function getNotifications(){
      return $this->getHelper($this->getFullUrl('notifications'));
  }
  
  public function getUsers(){
      return $this->getHelper($this->getFullUrl('users/get'));
  }
  
  public function getTriggers(){
      return $this->getHelper($this->getFullUrl('events/trigger'));
  }
}