<?php

$cnx = new MongoClient();
$db = $cnx->selectDB('iinano_flo');

$result = $db->execute(new MongoCode(file_get_contents('mapreduce.js')));


$cursor = $db->selectCollection('abusivereport')->find()->sort(['value' => -1]);
foreach ($cursor as $doc) {
    print_r($doc);
}

$coll = $db->selectCollection('abusivereport');
print_r($coll->db);
