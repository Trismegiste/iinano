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
    protected $mapReducedReport;

    /** @var \Trismegiste\SocialBundle\Repository\PublishingRepositoryInterface */
    protected $publishRepo;

    /** @var array */
    protected $pubAlias;

    public function __construct(PublishingRepositoryInterface $content, \MongoCollection $siblingColl, array $aliases, $collName)
    {
        $this->publishRepo = $content;
        $this->mapReducedReport = $siblingColl->db->selectCollection($collName);
        $this->pubAlias = $aliases;
    }

    public function compileReport()
    {

        $map = <<<MAPFUNC
function () {
    var pk = this._id.str
    // root entity
    if (isObject(this.abusive)) {
        emit({type: this['-class'], id: pk}, Object.keys(this.abusive).length)
    }
    // commentaries
    this.commentary.forEach(function (comment) {
        if (isObject(comment.abusive)) {
            emit({type: 'commentary', id: pk, uuid: comment.uuid}, Object.keys(comment.abusive).length)
        }
    })
}
MAPFUNC;

        $reduce = <<<REDFUNC
        function (key, values) {
    return Array.sum(values)
}
REDFUNC;

        $result = $this->mapReducedReport->db->command(
                [
                    'mapreduce' => 'dokudoki',
                    'map' => new \MongoCode($map),
                    'reduce' => new \MongoCode($reduce),
                    'query' => ['-class' => ['$in' => $this->pubAlias]],
                    'out' => $this->mapReducedReport->getName()
                ]
        );

        if ($result['ok'] != 1) {  // mongodb returns this value as a double ?!?
            throw new \RuntimeException($result['errmsg']);
        }

        return $result['counts'];
    }

    public function findMostReported($offset = 0, $limit = 20)
    {
        return $this->mapReducedReport->find()
                        ->sort(['value' => -1])
                        ->skip($offset)
                        ->limit($limit);
    }

}
