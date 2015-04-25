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
            [$doc0, ['comm' => 0, 'pub' => 0, 'pubCount' => 0]],
            [$doc1, ['comm' => 0, 'pub' => 1, 'pubCount' => 1]],
            [$doc2, ['comm' => 1, 'pub' => 1, 'pubCount' => 2]],
            [$doc3, ['comm' => 1, 'pub' => 0, 'pubCount' => 0]]
        ];
    }

    /**
     * @dataProvider getPublishing
     */
    public function testCompileReport(Publishing $doc, array $assertion)
    {
        $this->container->get('dokudoki.collection')->drop();
        $this->container->get('dokudoki.repository')->persist($doc);

        $result = $this->sut->findMostReportedPublish();
        $this->assertCount($assertion['pub'], $result);
        if ($assertion['pub'] == 1) {
            $result->rewind();
            $pub = $result->current();
            $this->assertEquals($assertion['pubCount'], $pub['abusiveCount']);
        }

        $result = $this->sut->findMostReportedCommentary();
        $this->assertCount($assertion['comm'], $result);
        if ($assertion['comm'] == 1) {
            $result->rewind();
            $comm = $result->current()['commentary'];
            $this->assertEquals(1, $comm['abusiveCount']);
        }
    }

}
