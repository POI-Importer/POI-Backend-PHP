<?php
include('conf.php');

$req_url = 'http://www.openstreetmap.org/oauth/request_token';     // OSM Request Token URL
$authurl = 'http://www.openstreetmap.org/oauth/authorize';          // OSM Authorize URL
$acc_url = 'http://www.openstreetmap.org/oauth/access_token';      // OSM Access Token URL
$api_url = 'http://api.openstreetmap.org/api/0.6/';                  // OSM API URL

session_start();
if(isset($_GET['oauth_token']) && isset($_SESSION['secret'])) {
    try {
       $oauth = new OAuth($conskey, $conssec, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);

       $oauth->setToken($_GET['oauth_token'], $_SESSION['secret']);
       $access_token_info = $oauth->getAccessToken($acc_url);
       $_SESSION['token'] = strval($access_token_info['oauth_token']);
       $_SESSION['secret'] = strval($access_token_info['oauth_token_secret']);
       $oauth->setToken($_SESSION['token'], $_SESSION['secret']);
       
       $oauth->fetch($api_url."user/details");
       $user_details = $oauth->getLastResponse();
       
       $xml = simplexml_load_string($user_details);
       $_SESSION['osm_user'] = strval($xml->user['display_name']);
       header('Location: index.php');
    } catch(OAuthException $E) {
        echo "<h3>EXCEPTION:</h3>\n";
        print_r($E);
        }
    }
else {
    try {
         $oauth = new OAuth($conskey,$conssec,OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_URI);
         $request_token_info = $oauth->getRequestToken($req_url);
         $_SESSION['secret'] = $request_token_info['oauth_token_secret'];
    ?>
    <h1>Authorize access</h1>
    <h3>If you're already an OpenStreetMap user, please use the following link to log in.</h3>
    <?php
         echo "<a href=\"".$authurl."?oauth_token=".$request_token_info['oauth_token']."\">Authorize access</a>";
    ?>

    <h3>If you're a new user, please register first!</h3>
    <a href="https://www.openstreetmap.org/user/new">New user</a>

    <?php
    } catch(OAuthException $E) {
         print_r($E);
        }
    }
?>