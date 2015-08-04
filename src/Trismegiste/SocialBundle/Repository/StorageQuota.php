<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository;

/**
 * StorageQuota is a monitoring for storage quota
 */
class StorageQuota
{

    /** @var \MongoCollection */
    protected $collection;
    protected $alias;

    public function __construct(\MongoCollection $coll, $pictureAlias)
    {
        $this->collection = $coll;
        $this->alias = $pictureAlias;
    }

    public function getPictureSize()
    {
        $result = $this->collection->aggregate([
            ['$match' => ['-class' => $this->alias]],
            ['$project' => ['size' => true, '-class' => true]],
            ['$group' => ['_id' => '$-class', 'total' => ['$sum' => '$size']]]
        ]);

        $total = 0;

        if (($result['ok'] == 1) && (count($result['result']))) {
            $total = $result['result'][0]['total'];
        }

        return $total;
    }

}
