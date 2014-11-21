<?php
$cache = "tweet-FB-cache-html.txt";
// Read cache file. Cache File will update once every 15 min.
if (file_exists($cache)) {
    //print html data from file.
    $data = file_get_contents($cache);
    header('Content-type: application/html');
    echo $data;
}
?>