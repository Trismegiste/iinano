<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository\MapReduce;

use Trismegiste\DokudokiBundle\Transform\Mediator\Colleague\MapAlias;

/**
 * PublishingCounter is a map-reduce service against MongoDB for counting
 * Publishing per user and updating Netizen.profile
 */
class PublishingCounter extends MruService
{

    public function compileReport()
    {

        $map = <<<MAPFUNC
function () {
    emit(this.owner.nickname, 1)
}
MAPFUNC;

        $reduce = <<<REDFUNC
function (key, values) {
    return Array.sum(values)
}
REDFUNC;

        $result = $this->database->command(
                [
                    'mapreduce' => $this->sourceColl->getName(),
                    'map' => new \MongoCode($map),
                    'reduce' => new \MongoCode($reduce),
                    'query' => ['owner.nickname' => ['$exists' => true]],
                    'out' => $this->mapReducedColl->getName()
                ]
        );

        if ($result['ok'] != 1) {  // mongodb returns this value as a double ?!?
            throw new \RuntimeException($result['errmsg']);
        }

        return $result['counts'];
    }

    public function updateRepeat()
    {

        $update = <<<UPDATEFUNC
(function () {
    var sourceCollection = db[sourceName];
    var targetCollection = db[targetName];

    var cursor = sourceCollection.find();
    while (cursor.hasNext()) {
        var doc = cursor.next();
        // updating counter for user found by nickname
        targetCollection.update(
                {'author.nickname': doc._id },
                {\$set: {'profile.publishingCounter': doc.value}}
        );
    }
})()
UPDATEFUNC;

        $result = $this->database->execute(new \MongoCode($update, [
            'sourceName' => $this->mapReducedColl->getName(),
            'targetName' => $this->sourceColl->getName()
        ]));

        if (!$result['ok']) {
            throw new \RuntimeException($result['errmsg']);
        }
    }

}
