<?php

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

/**
 * NetizenControllerTest tests the NetizenController
 */
class NetizenControllerTest extends WebTestCasePlus
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

    public function testMyProfile()
    {
        $crawler = $this->getPage('netizen_show', ['author' => 'kirk']);
        $this->assertGreaterThan(
                0, $crawler->filter('div.profile-body:contains("kirk")')->count()
        );

        $this->assertCount(1, $crawler->filter('div.content')->selectLink('Edit my profile'), 'The logged user can edit his profile');
    }

    public function testOtherProfile()
    {
        $crawler = $this->getPage('netizen_show', ['author' => 'spock']);
        $this->assertGreaterThan(
                0, $crawler->filter('div.profile-body:contains("spock")')->count()
        );

        $this->assertCount(0, $crawler->filter('div.content')->selectLink('Edit my profile'), 'A logged user cannot edit others profile');
    }

    public function testEditMyProfile()
    {
        $newFullName = 'James Tiberius Kirk';
        $crawler = $this->getPage('netizen_profile_edit');
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, ['profile' => ['fullName' => $newFullName]]);

        $user = $this->repo->findByNickname('kirk');
        $this->assertEquals($newFullName, $user->getProfile()->fullName);
    }

    public function testAvatarForm()
    {
        $crawler = $this->getPage('netizen_avatar_edit');
        $this->assertCount(1, $crawler->selectButton('send-crop'));
    }

    public function testAjaxSendAvatar()
    {
        $filename = $this->client->getContainer()->getParameter('kernel.root_dir') . '/../storage/00.jpg';

        $send = $this->generateUrl('netizen_avatar_edit');
        $this->client->request('POST', $send, [
            'content' => 'data:image/jpg;base64,' . base64_encode(file_get_contents($filename))
                ], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }

}
