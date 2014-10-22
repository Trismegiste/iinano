<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository\MapReduce;

/**
 * MruService is a map-reduce & update service
 */
abstract class MruService
{

    /** @var \MongoCollection */
    protected $sourceColl;

    /** @var \MongoCollection */
    protected $mapReducedColl;

    /** @var \MongoDB */
    protected $database;

    /**
     * Ctor
     *
     * @param \MongoCollection $sourceCollection the source collection of Content that will be updated
     * @param string $reducedName the target collection's name for reduced values
     * (in the same db as $sourceCollection)
     */
    public function __construct(\MongoCollection $sourceCollection, $reducedName)
    {
        $this->database = $sourceCollection->db;
        $this->sourceColl = $sourceCollection;
        $this->mapReducedColl = $this->database->selectCollection($reducedName);
    }

}
