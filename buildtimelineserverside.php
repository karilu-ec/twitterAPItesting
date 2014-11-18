<?php
// Retrieve Twitter timeline for USNA
//To run in a cron. Once every 5 minutes.

//We use already made Twitter OAuth library  https://github.com/jublonet/codebird-php
require_once ('bower_components/codebird-php/src/codebird.php');
$cache = "tweet-cache-html.txt";
$currentTime = time();

//if cache has not expired. Read cache file. Set to check Twitter once every 5 min.
if (file_exists($cache) && ($currentTime - filemtime($cache) > 5*60)) {
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
    
    $params = array(
    'screen_name' => $user,
    'q' => $user,
    'count' => $numTweets
    );
    //Make the REST call
    $data = (array) $cb->$api($params);
    //Results come in JSON
    $status=$data['httpstatus'];
    $remaining=$data['rate']['remaining'];
    if ($status == 200 && $remaining > 1) {
        //Need to build the markup right here and have Jquery just display what I built.
        
        $markup="";
        for($i = 0; $i < $numTweets; $i++) {
            $img = '';
            $obj = $data[$i];
            $url = 'http://twitter.com/' . $obj->user->screen_name . '/status/' . $obj->id_str;            
            if(isset($obj->entities->media) ) {
              $firstImage =  $obj->entities->media;
              $imageURL = $firstImage[0]->media_url;
              $height = $firstImage[0]->sizes->small->h;
              if ($height < 260) { //don't include images that are way too long.
                $img = '<a href="'.$url.'" target="_blank"><img src="'.$imageURL.':small" alt="USNA Tweet image '.$height.'" width="340"  /></a>';
              }
            }                                    
            $ago = timeAgo($obj->created_at);
            $divTitle = "<div class='title'><h4> @" . $obj->user->screen_name . "</h4><span class='timestamp'>" . timeAgo($obj->created_at) . "</span></div>";
            //$text = "<p>" .makeTwitterLinks($obj->text) . $img . "</p>";
            $text = "<p>" .makeTwitterLinks($obj->text) . $img . "</p>";
            $markup .= '<div class="feed-container">'.$divTitle.$text.'</div>';            
        }
        file_put_contents($cache, $markup);
    }
}

function timeAgo($dateString) {
    $rightNow = new DateTime("now", new DateTimeZone('UTC'));
    $then = new DateTime($dateString);
    $diff = $rightNow->diff($then);
    $suffix = ($diff->invert) ? ' ago' : '' ;
    if ( $diff->y >= 1 ) return $then->format('F d y');
    if ( $diff->m >= 1 ) return $then->format('F d');
    if ( $diff->d >= 1 && $diff->d < 2  ) return pluralize($diff->d, 'day' ) . $suffix; else return $then->format('F d');;
    if ( $diff->h >= 1 ) return pluralize($diff->h, 'hour' ) . $suffix;
    if ( $diff->i >= 1 ) return pluralize($diff->i, 'minute' ) . $suffix;
    return pluralize($diff->s, 'second' ) . $suffix;
}
function pluralize( $count, $text )
{
    return $count . ( ( $count == 1 ) ? ( " $text" ) : ( " ${text}s" ) );
}

function makeTwitterLinks($tweet) {
    //links
    $tweet = preg_replace("/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a target=\"_blank\" href=\"$1\">$1</a>", $tweet);
    //hash tags
    $tweet = preg_replace("/(^|\s+)#(\w+)/i", "$1<a target=\"_blank\" href=\"http://twitter.com/search?q=%23$2\">#$2</a>", $tweet);
    //at
    $tweet = preg_replace("/\B[@＠]([a-zA-Z0-9_]{1,20})/", "<a target=\"_blank\" href=\"http://twitter.com/intent/user?screen_name='$1'\">@$1</a>", $tweet);
    //user list
    $tweet = preg_replace("/\B[@＠]([a-zA-Z0-9_]{1,20}\/\w+)/", "<a target=\"_blank\" href=\"http://twitter.com/$1\">@$1</a>", $tweet);    
  

    return $tweet;
}
?>