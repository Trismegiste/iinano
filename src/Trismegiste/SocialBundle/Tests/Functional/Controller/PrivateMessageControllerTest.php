<?php

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

/**
 * PrivateMessageControllerTest tests the PrivateMessageController
 */
class PrivateMessageControllerTest extends WebTestCasePlus
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

    public function testSendToFollower()
    {
        $current = $this->repo->findByNickname('kirk');
        $follower = $this->repo->findByNickname('spock');
        $follower->follow($current);
        $this->repo->persist($current);
        $this->repo->persist($follower);
        $randomMsg = "Scanning" . rand();

        $crawler = $this->getPage('private_create', ['author' => 'spock']);
        $form = $crawler->filter('.pm-form')->selectButton('Send')->form();
        $crawler = $this->client->submit($form, ['social_private_message' => [
                'message' => $randomMsg
        ]]);
        $this->assertCount(1, $crawler->filter("div.pm-sent:contains('spock')"));
        $this->assertCount(1, $crawler->filter("div.pm-sent:contains('$randomMsg')"));

        return $randomMsg;
    }

    /**
     * @depends testSendToFollower
     */
    public function testReceivedInFollower($expected)
    {
        $this->logIn('spock');
        $crawler = $this->getPage('private_create');
        $this->assertCount(1, $crawler->filter("div.pm-received:contains('kirk')"));
        $this->assertCount(1, $crawler->filter("div.pm-received:contains('$expected')"));
    }

}
