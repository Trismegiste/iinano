<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

/**
 * GuestControllerTest tests public pages
 */
class GuestControllerTest extends WebTestCasePlus
{

    protected $collection;

    /** @var \Trismegiste\SocialBundle\Repository\NetizenRepository */
    protected $repo;

    protected function setUp()
    {
        parent::setUp();
        $this->client->followRedirects();
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
    }

    public function testAbout()
    {
        $crawler = $this->getPage('guest_about');
        $iter = $crawler->selectLink('SOLID');
        $this->assertCount(1, $iter);
    }

    public function testRegister()
    {
        $crawler = $this->getPage('guest_register');
        $form = $crawler->selectButton('Register')->form();
        $this->client->submit($form, ['netizen_register' => [
                'nickname' => 'spock',
                'password' => 'idic',
                'fullName' => 'Spock',
                'gender' => 'xy'
        ]]);

        $user = $this->repo->findByNickname('spock');
        $this->assertEquals('spock', $user->getUsername());

        $token = $this->client->getContainer()->get('security.context')->getToken();
        $this->assertEquals($user, $token->getUser());
    }

    public function testLoginPageFail()
    {
        $crawler = $this->getPage('trismegiste_login');
        $form = $crawler->filter('form')->selectButton('Sign in')->form();
        $this->client->submit($form, ['_username' => 'unknown', '_password' => 'passwoes']);

        $this->assertEquals($this->generateUrl('trismegiste_login'), $this->client->getHistory()->current()->getUri());
    }

    public function testLoginPageOk()
    {
        $crawler = $this->getPage('trismegiste_login');
        $form = $crawler->filter('form')->selectButton('Sign in')->form();
        $this->client->submit($form, ['_username' => 'spock', '_password' => 'idic']);

        $this->assertEquals($this->generateUrl('wall_index', [
                    'wallNick' => 'spock',
                    'wallFilter' => 'self'
                ]), $this->client->getHistory()->current()->getUri());
    }

}
