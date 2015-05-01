<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository;

/**
 * AbuseReport is a repository for compiling and listing abusive or spam report
 * on Content (Commentary and Publishing subclasses)
 */
class AbuseReport
{

    /** @var \MongoCollection */
    protected $collection;

    /** @var array */
    protected $pubAlias;

    /**
     * Ctor
     *
     * @param \MongoCollection $coll collection of Content
     * @param array $aliases an array of aliases for Publishing subclasses
     */
    public function __construct(\MongoCollection $coll, array $aliases)
    {
        $this->pubAlias = $aliases;
        $this->collection = $coll;
    }

    /**
     * Retrieves an iterator on a list of abusive Publishing (root) entities
     *
     * @param int $offset
     * @param int $limit
     *
     * @return \MongoCursor
     */
    public function findMostReportedPublish($offset = 0, $limit = 20)
    {
        return $this->collection->find([
                            'abusiveCount' => ['$gt' => 0]
                                ], [
                            'fanList' => false,
                            'commentary' => false
                        ])
                        ->sort(['abusiveCount' => -1])
                        ->skip($offset)
                        ->limit($limit);
    }

    /**
     * Retrieves an iterator on a list of abusive Commentary
     *
     * @param int $offset
     * @param int $limit
     *
     * @return \MongoCursor
     */
    public function findMostReportedCommentary($offset = 0, $limit = 20)
    {
        return $this->collection->aggregateCursor([
                    ['$match' => [
                            'commentary.0' => ['$exists' => true],
                            'commentary' => ['$elemMatch' => ['abusiveCount' => ['$gt' => 0]]]
                        ]
                    ],
                    ['$unwind' => '$commentary'],
                    ['$match' => ['commentary.abusiveCount' => ['$gt' => 0]]],
                    ['$project' => ['commentary' => true]],
                    ['$sort' => ['commentary.abusiveCount' => -1]]
        ]); // I love arrays
    }

    public function batchDeletePublish(array $listing)
    {
        $compilPk = [];
        foreach ($listing as $item) {
            $compilPk[] = $item['_id'];
        }
        $this->collection->remove(['_id' => ['$in' => $compilPk]]);
    }

    public function batchResetCounterPublish(array $listing)
    {
        foreach ($listing as $item) {
            $this->collection->update(
                    ['_id' => $item['_id']]
                    , ['$set' => ['abusiveCount' => 0, 'abusive' => []]]
                    // too bad update {multi:true} is not supported by the driver...
            );
        }
    }

    public function batchResetCounterCommentary(array $listing)
    {
        // @todo
    }

    public function batchDeleteCommentary(array $listing)
    {
        // @todo
    }

}
