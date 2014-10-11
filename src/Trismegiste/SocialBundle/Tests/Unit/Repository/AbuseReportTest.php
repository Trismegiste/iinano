<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Trismegiste\SocialBundle\Repository\AbuseReport;
use Trismegiste\Socialist\Author;
use Trismegiste\Socialist\SmallTalk;
use Trismegiste\Socialist\Commentary;
use Trismegiste\Socialist\Publishing;

/**
 * AbuseReportTest tests AbuseReport repository
 */
class AbuseReportTest extends WebTestCase
{

    /** @var AbuseReport */
    protected $sut;

    /** @var Symfony\Component\DependencyInjection\Container */
    protected $container;

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->sut = $this->container->get('social.abusereport.repository');
    }

    public function getPublishing()
    {
        $author = [];
        foreach (['kirk', 'spock', 'mccoy'] as $nick) {
            $author[] = new Author($nick);
        }

        $doc0 = new SmallTalk($author[0]);
        $doc1 = clone $doc0;
        $doc1->report($author[1]);
        $doc2 = clone $doc1;
        $doc2->report($author[2]);
        $comm = new Commentary($author[1]);
        $comm->report($author[0]);
        $doc2->attachCommentary($comm);
        $doc3 = clone $doc0;
        $doc3->attachCommentary($comm);

        return [
            [$doc0, []],
            [$doc1, [['type' => 'small', 'counter' => 1]]],
            [$doc2,
                [
                    ['type' => 'small', 'counter' => 2],
                    ['type' => 'comm', 'counter' => 1]
                ]
            ],
            [$doc3, [['type' => 'comm', 'counter' => 1]]]
        ];
    }

    /**
     * @dataProvider getPublishing
     */
    public function testCompileReport(Publishing $doc, array $assertion)
    {
        $this->container->get('dokudoki.collection')->drop();
        $this->container->get('dokudoki.repository')->persist($doc);
        $this->sut->compileReport();

        $result = iterator_to_array($this->sut->findMostReported(), false);
        $this->assertCount(count($assertion), $result);
        foreach ($assertion as $idx => $check) {
            $this->assertEquals($check['type'], $result[$idx]['type']);
            $this->assertEquals($check['counter'], $result[$idx]['counter']);
            $this->assertArrayHasKey('id', $result[$idx]['fk']);
        }
    }

}
