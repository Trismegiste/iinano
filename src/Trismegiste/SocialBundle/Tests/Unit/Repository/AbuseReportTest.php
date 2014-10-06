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

        $doc = new SmallTalk($author[0]);
        $doc->report($author[1]);
        $doc->report($author[2]);
        $comm = new Commentary($author[1]);
        $comm->report($author[0]);
        $doc->attachCommentary($comm);

        return [
            [$doc,
                [
                    ['type' => 'small', 'counter' => 2],
                    ['type' => 'commentary', 'counter' => 1]
                ]
            ]
        ];
    }

    /**
     * @dataProvider getPublishing
     */
    public function testCompileReport(Publishing $doc, array $assertion)
    {
        $this->container->get('dokudoki.collection')->drop();
        $this->container->get('social.content.repository')->persist($doc);
        $this->sut->compileReport();

        $result = iterator_to_array($this->sut->findMostReported(), false);
        foreach ($assertion as $idx => $check) {
            $this->assertEquals($check['type'], $result[$idx]['type']);
            $this->assertEquals($check['counter'], $result[$idx]['counter']);
            $this->assertArrayHasKey('id', $result[$idx]['fk']);
        }
    }

}
