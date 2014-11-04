<?php
//To run in a cron. Once every 15 minutes.

//We use already made Twitter OAuth library  https://github.com/jublonet/codebird-php
require_once ('bower_components/codebird-php/src/codebird.php');
$cache = "tweet-cache-json.txt";
$currentTime = time();

//if cache has not expired. Read cache file. Set to check Twitter once every 15 min.
if (file_exists($cache) && ($currentTime - filemtime($cache) > 15*60)) {
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
    $user = 'navalacademy'; //username
    $numTweets = 5;
    $api = "statuses_userTimeline"; //https://dev.twitter.com/rest/reference/get/statuses/user_timeline
    $count = $numTweets;
    
    $params = array(
    'screen_name' => $user,
    'q' => $user,
    'count' => $count
    );
    //Make the REST call
    $data = (array) $cb->$api($params);
    //Output result in JSON, getting it ready for jQuery to process
    $status=$data['httpstatus'];
    $remaining=$data['rate']['remaining'];
    if ($status == 200 && $remaining > 1) {
        file_put_contents($cache,json_encode($data));        
    }
}
?>