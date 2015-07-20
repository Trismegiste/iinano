<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

/**
 * RepeatControllerTest tests the RepeatController
 */
class RepeatControllerTest extends WebTestCasePlus
{

    protected $collection;

    /** @var \Trismegiste\SocialBundle\Repository\PublishingRepository */
    protected $contentRepo;
    static protected $random;

    static public function setUpBeforeClass()
    {
        // each test case has a different random, avoid false positive
        static::$random = rand();
    }

    protected function setUp()
    {
        parent::setUp();
        $this->client->followRedirects();
        $this->logIn('kirk');
        $this->collection = $this->getService('dokudoki.collection');
        $this->contentRepo = $this->getService('social.publishing.repository');
    }

    /**
     * @test
     */
    public function initialize()
    {
        $this->collection->drop();
        $this->assertCount(0, $this->collection->find());
        $this->addUserFixture('kirk');
        $this->addUserFixture('spock');

        $this->logIn('kirk');
        $pub = $this->contentRepo->create('small');
        $pub->setMessage('message' . static::$random);
        $this->contentRepo->persist($pub);

        return $pub->getId();
    }

    /**
     * @depends initialize
     */
    public function testRepeatOnce($pk)
    {
        $this->logIn('spock');
        $crawler = $this->getPage('wall_index', ['wallNick' => 'kirk', 'wallFilter' => 'self']);
        $this->assertCount(1, $crawler->filter(".publishing article:contains('message" . static::$random . "')"));

        $url = $crawler->filter('a[data-repeat-ajaxed]')->attr('data-repeat-ajaxed');
        $this->ajaxPost($url);
        print_r($this->getJsonResponse());
        $this->assertEquals("You've repeated a message from kirk", $this->getJsonResponse()->message);
    }

    /**
     * @depends initialize
     */
    public function testRepeatHimself($pk)
    {
        $crawler = $this->getPage('pub_repeat_create', ['id' => $pk, 'wallNick' => 'spock', 'wallFilter' => 'all']);
        $this->assertCount(2, $crawler->filter(".publishing article:contains('message" . static::$random . "')"));
        $this->assertCount(1, $crawler->filter("script:contains('repeat yourself')"));
    }

    /**
     * @depends initialize
     */
    public function testAlreadyRepeated($pk)
    {
        $this->logIn('spock');
        $crawler = $this->getPage('pub_repeat_create', ['id' => $pk, 'wallNick' => 'spock', 'wallFilter' => 'self']);
        $this->assertCount(1, $crawler->filter(".publishing article:contains('message" . static::$random . "')"));
        $this->assertCount(1, $crawler->filter("script:contains('already have repeated')"));
    }

    public function testNoEditOnRepeat()
    {
        $this->logIn('spock');
        $crawler = $this->getPage('wall_index', ['wallNick' => 'spock', 'wallFilter' => 'self']);
        $this->assertCount(0, $crawler->filter(".publishing nav a:contains('Edit')"));
    }

    public function testDeleteSource()
    {
        $crawler = $this->getPage('wall_index', ['wallNick' => 'kirk', 'wallFilter' => 'self']);
        $button = $crawler->filter(".publishing nav")->selectLink('Delete')->link();
        $crawler = $this->client->click($button);
        $this->assertCount(0, $this->collection->find(['owner.nickname' => 'kirk']));
    }

    public function testSourceNotFound()
    {
        $this->logIn('spock');
        $crawler = $this->getPage('wall_index', ['wallNick' => 'spock', 'wallFilter' => 'self']);
        $button = $crawler->filter(".publishing h3:contains('has said') a:contains('ago')")->link();
        $this->client->click($button);
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteRepeat()
    {
        $this->logIn('spock');
        $crawler = $this->getPage('wall_index', ['wallNick' => 'spock', 'wallFilter' => 'self']);
        $button = $crawler->filter(".publishing nav")->selectLink('Delete')->link();
        $crawler = $this->client->click($button);
        $this->assertCount(0, $crawler->filter(".publishing article:contains('message" . static::$random . "')"));
    }

}
