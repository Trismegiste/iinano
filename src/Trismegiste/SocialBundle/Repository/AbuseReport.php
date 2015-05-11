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

    /**
     * Delete all Publishing content with their pk
     *
     * @param array $listing flat array given by the query above
     * @see self::findMostReportedPublish()
     */
    public function batchDeletePublish(array $listing)
    {
        $compilPk = [];
        foreach ($listing as $item) {
            $compilPk[] = $item['_id'];
        }
        $this->collection->remove(['_id' => ['$in' => $compilPk]]);
    }

    /**
     * Reset all abuse report counters of the given Publishing content
     *
     * @param array $listing
     */
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

    /**
     * Reset all abuse report counters of given embedded Commentary
     *
     * @param array $listing a list of document given by findMostReportedCommentary()
     * @see self::findMostReportedCommentary()
     */
    public function batchResetCounterCommentary(array $listing)
    {
        foreach ($listing as $item) {
            $this->collection->update([
                '_id' => $item['_id'],
                'commentary.uuid' => $item['commentary']['uuid']
                    ], [
                '$set' => [
                    'commentary.$.abusiveCount' => 0,
                    'commentary.$.abusive' => [],
                ]]
            );
        }
    }

    /**
     * Delete all given embedded Commentary
     *
     * @param array $listing a list of document given by findMostReportedCommentary()
     * @see self::findMostReportedCommentary()
     */
    public function batchDeleteCommentary(array $listing)
    {
        foreach ($listing as $item) {
            $this->collection->update(
                    ['_id' => $item['_id']], ['$pull' => [
                    'commentary' => [
                        'uuid' => $item['commentary']['uuid']
                    ]
                ]]
            );
        }
    }

}
