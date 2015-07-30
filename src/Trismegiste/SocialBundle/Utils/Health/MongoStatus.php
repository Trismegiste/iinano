<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Utils\Health;

/**
 * MongoStatus is a collection of helpers about mongo statistics for this app
 */
class MongoStatus
{

    protected $collection;

    public function __construct(\MongoCollection $coll)
    {
        $this->collection = $coll;
    }

    public function getCollectionStats()
    {
        return $this->collection
                        ->db
                        ->execute(new \MongoCode('db.' . $this->collection->getName() . '.stats();'))['retval'];
    }

    public function getDbStats()
    {
        return $this->collection
                        ->db
                        ->execute(new \MongoCode('db.stats();'))['retval'];
    }

    public function getCounterPerAlias()
    {
        return $this->collection
                        ->aggregateCursor([[
                        '$group' => [
                            '_id' => '$-class', 'counter' => ['$sum' => 1]
                        ]
        ]]);
    }

}
