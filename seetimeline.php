<?php
//We use already made Twitter OAuth library  https://github.com/jublonet/codebird-php
require_once ('bower_components/codebird-php/src/codebird.php');
//Twitter OAuth Settings
$CONSUMER_KEY = '...';
$CONSUMER_SECRET = '...';
$ACCESS_TOKEN = '...';
$ACCESS_TOKEN_SECRET = '...';
//Get authenticated
\Codebird\Codebird::setConsumerKey($CONSUMER_KEY, $CONSUMER_SECRET);
$cb = \Codebird\Codebird::getInstance();
$cb->setToken($ACCESS_TOKEN, $ACCESS_TOKEN_SECRET);
//retrieve posts
$q = $_POST['q'];
$count = $_POST['count'];
$api = $_POST['api'];
//https://dev.twitter.com/rest/reference/get/statuses/user_timeline
//https://dev.twitter.com/docs/api/1.1/get/search/tweets
$params = array(
'screen_name' => $q,
'q' => $q,
'count' => $count
);
//Make the REST call
$data = (array) $cb->$api($params);
//Output result in JSON, getting it ready for jQuery to process
echo json_encode($data);
?>