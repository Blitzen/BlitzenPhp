#Blitzen PHP

Install:
Clone https://github.com/Blitzen/BlitzenPhp.git

Apply for a client_id and client_secret at https://blitzen.com/api/v1/o/applications/
Initialize the wrapper:
$wrapper = new BlitzenApiWrapper($client_id, $client_secret, $subdomain)

If you already have an access_token and a refresh_token just pass them in on the initialization
$wrapper = new BlitzenApiWrapper($client_id, $client_secret, $subdomain, $access_token, $refresh_token)

If you don't just authenticate and the tokens will be generated:
$wrapper->authenticate($username, $password)

Getting information:
Forms:
$wrapper->getForms()
Notifications:
$wrapper->getNotifications()
Contacts:
$wrapper->getContacts()
Users:
$wrapper->getUsers()
