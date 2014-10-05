<?php

$follower = [];
for ($k = 0; $k < 10000; $k++) {
    $follower[$k] = "user $k";
}

$stop = microtime(true);
for ($k = 0; $k < 10000; $k++) {
    $search = rand(0, 9999);
    if (!in_array("user $search", $follower)) {
        die('ooops');
    }
}
printf("in_array: %.1f\n", 1000 * (microtime(true) - $stop));

$follower = array_flip($follower);
$stop = microtime(true);
for ($k = 0; $k < 10000; $k++) {
    $search = rand(0, 9999);
    if (!array_key_exists("user $search", $follower)) {
        die('eeeh?');
    }
}
printf("array_key_exists: %.1f\n", 1000 * (microtime(true) - $stop));
