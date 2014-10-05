<?php

$map = <<<MAPFUNC
function () {
    var pk = this._id
    // root entity
    if (isObject(this.abusive)) {
        for (var key in this.abusive) {
            emit({id: pk, type: 'root'}, 1)
        }
    }
    // commentaries
    this.commentary.forEach(function (comment) {
        if (isObject(comment.abusive)) {
            for (var key in comment.abusive) {
                emit({id: pk, type: 'commentary', uuid: comment.uuid}, 1)
            }
        }
    })
}
MAPFUNC;

$reduce = <<<REDFUNC
        function (key, values) {
    return Array.sum(values)
}
REDFUNC;

$cnx = new MongoClient();
$db = $cnx->selectDB('iinano_flo');

$result = $db->command(
        [
            'mapreduce' => 'dokudoki',
            'map' => new MongoCode($map),
            'reduce' => new MongoCode($reduce),
            'query' => ['-class' => ['$in' => ['small', 'status']]],
            'out' => ['inline' => 1] // 'abusivereport'
        ]
);

print_r($result);
/*
$cursor = $db->selectCollection('abusivereport')->find()->sort(['value' => -1]);
foreach ($cursor as $doc) {
    print_r($doc);
}

$coll = $db->selectCollection('abusivereport');
print_r($coll->db);
*/