<?php
/* Blitzen
 * Jordan Clark 2015
 */
?>
Test Authentication

<form action="authenticate.php" method="POST">
  <input type='text' placeholder='client id' name='clientid'/><br/>
  <input type='text' placeholder='client secret' name='clientsecret'/><br/>
  <input type='text' placeholder='username' name='username'/><br/>
  <input type='password' placeholder='password' name='password'/><br/>

  <input type='submit'/>
</form>


Test Refresh

<form action="refresh.php" method="POST">
    <input type='text' placeholder='client id' name='clientid'/><br/>
    <input type='text' placeholder='client secret' name='clientsecret'/><br/>
    <input type='text' placeholder='Access Token' name='access_token'/>
    <input type='text' placeholder='Refresh Token' name='refresh_token'/>
    
    <input type='submit'/>
</form>
