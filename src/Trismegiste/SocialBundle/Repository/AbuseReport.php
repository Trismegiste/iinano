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
    protected $compiledReport;

    /** @var \Trismegiste\SocialBundle\Repository\PublishingRepositoryInterface */
    protected $publishRepo;

    /** @var array */
    protected $pubAlias;

    public function __construct(PublishingRepositoryInterface $content, \MongoCollection $siblingColl, array $aliases, $collName)
    {
        $this->publishRepo = $content;
        $this->compiledReport = $siblingColl->db->selectCollection($collName);
        $this->pubAlias = $aliases;
    }

    public function compileReport()
    {
        $result = $this->compiledReport->db
                ->execute(new \MongoCode(file_get_contents(__DIR__ . '/v8/abusereport.js')));

        if (!$result['ok']) {
            throw new \RuntimeException($result['errmsg']);
        }
    }

    public function findMostReported($offset = 0, $limit = 20)
    {
        return $this->compiledReport->find()
                        ->sort(['counter' => -1])
                        ->skip($offset)
                        ->limit($limit);
    }

}
