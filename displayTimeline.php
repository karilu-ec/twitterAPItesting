<?php
//We use already made Twitter OAuth library  https://github.com/jublonet/codebird-php
require_once ('bower_components/codebird-php/src/codebird.php');
$cache = "tweet-cache-html.txt";
// Read cache file. Cache File will update once every 15 min.
if (file_exists($cache)) {
    //print json data from file.
    $cachefile = fopen($cache, 'r');
    $data = fgets($cachefile);
    fclose($cachefile);
    header('Content-type: application/html');
    echo $data;
}
?>