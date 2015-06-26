<?php
/* Blitzen
 * Jordan Clark 2015
 */
    require_once('../BlitzenApiWrapper.php');
    $username = $_POST['username'];
    $password = $_POST['password'];
    $client_id = $_POST['clientid'];
    $client_secret = $_POST['clientsecret'];
    $wrapper = new BlitzenApiWrapper($client_id, $client_secret, 'blitzen');

    $response = $wrapper->authenticate($username, $password);
    echo "OAuth2 Request Information:";
    var_dump($response);
?>

<?php
    $forms = $wrapper->getForms();
    $formFields = $wrapper->getFormFields('b64535b934404c1199a6c917e23cf5');
    $notifications = $wrapper->getNotifications();
    $contacts = $wrapper->getContacts();
    $users = $wrapper->getUsers();
    echo "</br></br>Forms:";
    var_dump($forms);
    echo "</br></br>Form Fields:";
    var_dump($formFields);
    echo "</br></br>Notifications:";
    var_dump($notifications);
    echo "</br></br>Contacts:";
    var_dump($contacts);
    echo "</br></br>Users:";
    var_dump($users);