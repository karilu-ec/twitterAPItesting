<?php
$FBcache = "FBPosts.txt";
$currentTime = time();
$FBPosts = "";
//Read FB Cache file
if (file_exists($FBcache)) {
    if(file_get_contents($FBcache) !== false) {
        $FBPosts = file_get_contents($FBcache);
    }
    
}
echo "File read:". $FBPosts;
?>