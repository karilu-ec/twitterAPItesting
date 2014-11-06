<?php
$cache = "tweet-cache-json.txt";
// Read cache file. Cache File will update once every 15 min.
if (file_exists($cache)) {
    //print json data from file.
    $cachefile = fopen($cache, 'r');
    $data = fgets($cachefile);
    fclose($cachefile);
    header('Content-type: application/json');
    echo $data;
}
?>