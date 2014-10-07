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

    protected $sourceName;

    /** @var \MongoCollection */
    protected $compiledReport;

    /** @var array */
    protected $pubAlias;

    /** @var \MongoDB */
    protected $database;

    /**
     * Ctor
     *
     * @param \MongoCollection $sourceCollection the source collection of Content
     * @param string $targetName the target collection's name (in the same db as $sourceCollection
     * @param array $aliases an array of aliases for Publishing subclasses
     */
    public function __construct(\MongoCollection $sourceCollection, $targetName, array $aliases)
    {
        $this->database = $sourceCollection->db;
        $this->sourceName = $sourceCollection->getName();
        $this->compiledReport = $this->database->selectCollection($targetName);
        $this->pubAlias = $aliases;
    }

    /**
     * Runs a script in MongoDB
     *
     * @throws \RuntimeException
     */
    public function compileReport()
    {
        $result = $this->database
                ->execute(new \MongoCode(file_get_contents(__DIR__ . '/v8/abusereport.js'), [
            'aliases' => $this->pubAlias,
            'sourceName' => $this->sourceName,
            'targetName' => $this->compiledReport->getName(),
            'classAliasKey' => \Trismegiste\DokudokiBundle\Transform\Mediator\Colleague\MapAlias::CLASS_KEY
        ]));

        if (!$result['ok']) {
            throw new \RuntimeException($result['errmsg']);
        }
    }

    /**
     * Retrieves a cursor on a compiled list on abuse reports
     *
     * @param int $offset
     * @param int $limit
     *
     * @return \MongoCursor
     */
    public function findMostReported($offset = 0, $limit = 20)
    {
        return $this->compiledReport->find()
                        ->sort(['counter' => -1])
                        ->skip($offset)
                        ->limit($limit);
    }

}
