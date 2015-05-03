<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository\MapReduce;

/**
 * CounterPerUser is a map-reduce service against MongoDB for counting
 * Publishing/commentary/Like per user and updating Netizen.profile
 *
 * For the Like Counter, this is the RECEIVED Likes by each user on Content only, not
 * how many Likes a user has "sent" to others. Likes on Netizen are not count.
 */
class CounterPerUser extends MruService
{

    protected function mapReduce()
    {

        $map = <<<MAPFUNC
function () {
    emit(this.owner.nickname, { pub:1, comm:0, like:Object.keys(this.fanList).length });

    this.commentary.forEach(function(commentary) {
        emit(commentary.owner.nickname, { pub:0, comm:1, like:Object.keys(commentary.fanList).length });
    })
}
MAPFUNC;

        $reduce = <<<REDFUNC
function (key, values) {
    counter = { pub:0, comm:0, like:0 };

    values.forEach(function(vector) {
        counter.pub += vector.pub;
        counter.comm += vector.comm;
        counter.like += vector.like;
    });

    return counter;
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

    protected function update()
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
                {\$set: {
                    'profile.publishingCounter': doc.value.pub,
                    'profile.commentaryCounter': doc.value.comm,
                    'profile.likeCounter': doc.value.like
                }}
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
