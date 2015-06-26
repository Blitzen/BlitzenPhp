<?php
/* Blitzen
 * Jordan Clark 2015
 */
    require_once('../BlitzenApiWrapper.php');
    $client_id = $_POST['clientid'];
    $client_secret = $_POST['clientsecret'];
    $access_token = $_POST['access_token'];
    $refresh_token = $_POST['refresh_token'];
    $wrapper = new BlitzenApiWrapper($client_id, $client_secret, 'blitzen', $access_token, $refresh_token);
    $wrapper->refreshToken($refresh_token);

    $response = $wrapper->getForms();
    echo "OAuth2 Request Information:";
    var_dump($response);