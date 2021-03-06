<?php

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

/**
 * SocialControllerTest tests the SocialController
 */
class SocialControllerTest extends WebTestCasePlus
{

    protected $collection;

    /** @var \Trismegiste\SocialBundle\Repository\NetizenRepository */
    protected $repo;

    protected function setUp()
    {
        parent::setUp();
        $this->client->followRedirects();
        $this->logIn('kirk');
        $this->collection = $this->getService('dokudoki.collection');
        $this->repo = $this->getService('social.netizen.repository');
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
    }

    public function testLikeSomebody()
    {
        $crawler = $this->getPage('wall_index', ['wallNick' => 'spock', 'wallFilter' => 'self']);
        $this->assertEquals(0, (int) $crawler->filter('.netizen button.fan-count')->text());
        $link = $crawler->filter('.netizen button.fan-count')->form();
        $crawler = $this->client->click($link);
        $this->assertEquals(1, (int) $crawler->filter('.netizen button.fan-count')->text());
    }

    public function testUnlikeSomebody()
    {
        $crawler = $this->getPage('wall_index', ['wallNick' => 'spock', 'wallFilter' => 'self']);
        $this->assertEquals(1, (int) $crawler->filter('.netizen button.fan-count')->text());
        $link = $crawler->filter('.netizen button.fan-count')->form();
        $crawler = $this->client->click($link);
        $this->assertEquals(0, (int) $crawler->filter('.netizen button.fan-count')->text());
    }

    public function testFollowSomebody()
    {
        $crawler = $this->getPage('wall_index', ['wallNick' => 'spock', 'wallFilter' => 'self']);
        $link = $crawler->filter('.netizen')->selectButton('Follow')->form();
        $crawler = $this->client->click($link);

        $spock = $this->repo->findByNickname('spock');
        $kirk = $this->repo->findByNickname('kirk');
        // spock has now one follower :
        $this->assertEquals(1, $spock->getFollowerCount());
        // kirk is now following spock :
        $this->assertEquals(1, $kirk->getFollowingCount());
    }

    public function testUnfollowSomebody()
    {
        $crawler = $this->getPage('wall_index', ['wallNick' => 'spock', 'wallFilter' => 'self']);
        $link = $crawler->filter('.netizen')->selectButton('Unfollow')->form();
        $crawler = $this->client->click($link);

        $spock = $this->repo->findByNickname('spock');
        $kirk = $this->repo->findByNickname('kirk');
        // spock has now one follower :
        $this->assertEquals(0, $spock->getFollowerCount());
        // kirk is now following spock :
        $this->assertEquals(0, $kirk->getFollowingCount());
    }

}
