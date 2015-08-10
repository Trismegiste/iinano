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
    protected $dbMaxSize;

    /**
     * Ctor
     *
     * @param \MongoCollection $coll
     * @param int $size size in bytes
     */
    public function __construct(\MongoCollection $coll, $size)
    {
        $this->dbMaxSize = (int) $size;
        $this->collection = $coll;
    }

    /**
     * Gets stats on app's main collection
     *
     * @return array
     */
    public function getCollectionStats()
    {
        return $this->collection
                        ->db
                        ->execute(new \MongoCode('db.' . $this->collection->getName() . '.stats();'))['retval'];
    }

    /**
     * Gets stats on mongodb current database
     *
     * @return array
     */
    public function getDbStats()
    {
        return $this->collection
                        ->db
                        ->execute(new \MongoCode('db.stats();'))['retval'];
    }

    /**
     * Gets a cursor on counters for each content type
     *
     * @return \MongoCommandCursor
     */
    public function getCounterPerAlias()
    {
        return $this->collection
                        ->aggregateCursor([[
                        '$group' => [
                            '_id' => '$-class', 'counter' => ['$sum' => 1]
                        ]
        ]]);
    }

    /**
     * Get a cursor on old object to delete to get under the sotorage quota for this collection
     *
     * @return \Traversable
     */
    public function findExceedingQuotaDocument()
    {
        $health = $this->getCollectionStats();

        if ($health['size'] > $this->dbMaxSize) {
            $objectEstimate = ($health['size'] - $this->dbMaxSize) / $health['avgObjSize'];
            return $this->collection->find([
                                '-class' => [
                                    '$in' => [
                                        'small',
                                        'status',
                                        'picture',
                                        'video',
                                        'repeat',
                                        'private'
                                    ]
                                ]
                            ])
                            ->sort(['_id' => 1])
                            ->limit((int) $objectEstimate);
        }

        return [];
    }

}
