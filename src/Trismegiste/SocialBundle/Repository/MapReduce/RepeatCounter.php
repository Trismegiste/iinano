<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository\MapReduce;

use Trismegiste\DokudokiBundle\Transform\Mediator\Colleague\MapAlias;

/**
 * RepeatCounter is a map-reduce service against MongoDB for counting and updating Repeat
 * message.
 */
class RepeatCounter extends MruService
{

    public function compileReport()
    {

        $map = <<<MAPFUNC
function () {
    emit(this.embedded.id, {fk: [this._id]})
}
MAPFUNC;

        $reduce = <<<REDFUNC
function (key, values) {
    var compil = []
    values.forEach(function(pkList) {
        compil = compil.concat(pkList.fk)
    })
    return {fk: compil}
}
REDFUNC;

        $finalize = <<<FINAFUNC
function (key, reducedVal) {
    reducedVal.fk = reducedVal.fk
    reducedVal.counter = reducedVal.fk.length

    return reducedVal
}
FINAFUNC;

        $result = $this->database->command(
                [
                    'mapreduce' => $this->sourceColl->getName(),
                    'map' => new \MongoCode($map),
                    'reduce' => new \MongoCode($reduce),
                    'finalize' => new \MongoCode($finalize),
                    'query' => [MapAlias::CLASS_KEY => 'repeat'],
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
        // updating counter for all Repeat
        targetCollection.update(
                {_id: {\$in: doc.value.fk }},
                {\$set: {repeatedCount: doc.value.counter}},
                {multi: true}
        );
        // updating counter for source publishing
        targetCollection.update(
                {_id: doc._id },
                {\$set: {repeatedCount: doc.value.counter}}
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
