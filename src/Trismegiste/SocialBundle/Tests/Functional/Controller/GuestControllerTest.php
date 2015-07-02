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
        // entrance fee config
        $fee = new \Trismegiste\SocialBundle\Ticket\EntranceFee();
        $fee->setAmount(1000);
        $fee->setCurrency('JPY');
        $fee->setDurationValue(12); // 12 months
        $this->getService('dokudoki.repository')->persist($fee);
        // coupon
        $coupon = new \Trismegiste\SocialBundle\Ticket\Coupon();
        $coupon->hashKey = 'ABCDE';
        $coupon->expiredAt = new \DateTime('tomorrow');
        $coupon->setDurationValue(5);
        $this->getService('dokudoki.repository')->persist($coupon);
    }

    public function testAbout()
    {
        $crawler = $this->getPage('guest_about');
        $iter = $crawler->selectLink('SOLID');
        $this->assertCount(1, $iter);
    }

    public function testRegisterWithPayment()
    {
        $crawler = $this->getPage('trismegiste_oauth_connect');
        $authLink = $crawler->selectLink('dummy')->link();
        $crawler = $this->client->click($authLink);
        $this->assertCurrentRoute('trismegiste_oauth_dummyserver', ['redirect' => $this->generateUrl('trismegiste_oauth_check', ['provider' => 'dummy'])]);
        $oauthForm = $crawler->selectButton('Redirect')->form();
        $crawler = $this->client->submit($oauthForm, [
            'uid' => '1701',
            'nickname' => 'spock'
        ]);
        $this->assertCurrentRoute('guest_register');

        $form = $crawler->selectButton('Register')->form();
        $this->client->submit($form, ['netizen_register' => [
                'nickname' => 'spock',
                'gender' => 'xy',
                'dateOfBirth' => ['year' => 1984, 'month' => 11, 'day' => 13]
        ]]);

        $this->assertCurrentRoute('buy_new_ticket');

        $user = $this->repo->findByNickname('spock');
        $this->assertEquals('spock', $user->getUsername());
        $this->assertEquals('1701', $user->getCredential()->getUid());

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

    public function testLoginPageFailWithoutPayment()
    {
        $crawler = $this->getPage('trismegiste_login');
        $form = $crawler->filter('form')->selectButton('Sign in')->form();
        $this->client->submit($form, ['_username' => 'spock', '_password' => 'idic']);

        $this->assertEquals($this->generateUrl('buy_new_ticket'), $this->client->getHistory()->current()->getUri());
    }

    public function testLoginPageWithPayment()
    {
        $crawler = $this->getPage('trismegiste_login');
        $form = $crawler->filter('form')->selectButton('Sign in')->form();
        $this->client->submit($form, ['_username' => 'spock', '_password' => 'idic']);
        $this->assertEquals($this->generateUrl('buy_new_ticket'), $this->client->getHistory()->current()->getUri());

        // faking a payment
        $ticketRepo = $this->getService('social.ticket.repository');
        $ticket = $ticketRepo->createTicketFromPayment();
        $ticketRepo->persistNewPayment($ticket);

        $crawler = $this->getPage('trismegiste_login');
        $form = $crawler->filter('form')->selectButton('Sign in')->form();
        $this->client->submit($form, ['_username' => 'spock', '_password' => 'idic']);

        $this->assertEquals($this->generateUrl('wall_index', [
                    'wallNick' => 'spock',
                    'wallFilter' => 'self'
                ]), $this->client->getHistory()->current()->getUri());
    }

    public function testRegisterWithCoupon()
    {
        $crawler = $this->getPage('guest_register');
        $form = $crawler->selectButton('Register')->form();
        $this->client->submit($form, ['netizen_register' => [
                'nickname' => 'coupon',
                'password' => ['password' => 'idic', 'confirm_password' => 'idic'],
                'fullName' => 'coupon',
                'gender' => 'xy',
                'email' => 'dfsdfssdf@sddsqsdq.fr',
                'dateOfBirth' => ['year' => 1984, 'month' => 11, 'day' => 13],
                'optionalCoupon' => 'ABCDE'
        ]]);

        $this->assertEquals($this->generateUrl('wall_index', [
                    'wallNick' => 'coupon',
                    'wallFilter' => 'self'
                ]), $this->client->getHistory()->current()->getUri());
    }

    public function testLoginPageWithCoupon()
    {
        $crawler = $this->getPage('trismegiste_login');
        $form = $crawler->filter('form')->selectButton('Sign in')->form();
        $this->client->submit($form, ['_username' => 'coupon', '_password' => 'idic']);

        $this->assertEquals($this->generateUrl('wall_index', [
                    'wallNick' => 'coupon',
                    'wallFilter' => 'self'
                ]), $this->client->getHistory()->current()->getUri());
    }

    public function testLandingWithCoupon()
    {
        $crawler = $this->getPage('guest_coupon_landing', ['code' => 'AZERTY']);
        $this->assertEquals($this->generateUrl('trismegiste_oauth_connect'), $this->client->getHistory()->current()->getUri());
        $this->assertEquals('AZERTY', $this->getService('session')->get('coupon'));
    }

}
