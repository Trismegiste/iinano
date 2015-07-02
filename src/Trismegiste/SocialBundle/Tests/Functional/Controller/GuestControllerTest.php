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

    /**
     * @depends initialize
     */
    public function testUnknownUserGoToRegister()
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
    }

    /**
     * @depends testUnknownUserGoToRegister
     */
    public function testRegisterUntilPayment()
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

    /**
     * @depends testRegisterUntilPayment
     */
    public function testLoginWithoutPaymentGoToBuyTicket()
    {
        $crawler = $this->getPage('trismegiste_oauth_connect');
        $authLink = $crawler->selectLink('dummy')->link();
        $crawler = $this->client->click($authLink);
        $oauthForm = $crawler->selectButton('Redirect')->form();
        $crawler = $this->client->submit($oauthForm, [
            'uid' => '1701'
        ]);

        $this->assertCurrentRoute('buy_new_ticket');

        // faking a payment
        $ticketRepo = $this->getService('social.ticket.repository');
        $ticket = $ticketRepo->createTicketFromPayment();
        $ticketRepo->persistNewPayment($ticket);
    }

    /**
     * @depends testRegisterUntilPayment
     */
    public function testLoginPageWithPayment()
    {
        $crawler = $this->getPage('trismegiste_oauth_connect');
        $authLink = $crawler->selectLink('dummy')->link();
        $crawler = $this->client->click($authLink);
        $oauthForm = $crawler->selectButton('Redirect')->form();
        $crawler = $this->client->submit($oauthForm, [
            'uid' => '1701'
        ]);

        $this->assertCurrentRoute('wall_index', [
            'wallNick' => 'spock',
            'wallFilter' => 'self'
        ]);
    }

    public function testLandingWithCoupon()
    {
        $crawler = $this->getPage('guest_coupon_landing', ['code' => 'AZERTY']);
        $this->assertEquals($this->generateUrl('trismegiste_oauth_connect'), $this->client->getHistory()->current()->getUri());
        $this->assertEquals('AZERTY', $this->getService('session')->get('coupon'));
    }

    /**
     * @depends testLandingWithCoupon
     */
    public function testRegisterWithCoupon()
    {
        $crawler = $this->getPage('guest_coupon_landing', ['code' => 'ABCDE']);

        $crawler = $this->getPage('trismegiste_oauth_connect');
        $authLink = $crawler->selectLink('dummy')->link();
        $crawler = $this->client->click($authLink);
        $oauthForm = $crawler->selectButton('Redirect')->form();
        $crawler = $this->client->submit($oauthForm, [
            'uid' => 'fr33',
            'nickname' => 'kirk'
        ]);
        $this->assertCurrentRoute('guest_register');

        $form = $crawler->selectButton('Register')->form();
        $this->client->submit($form, ['netizen_register' => [
                'nickname' => 'jkirk',
                'gender' => 'xy',
                'dateOfBirth' => ['year' => 1984, 'month' => 11, 'day' => 13]
        ]]);

        $this->assertCurrentRoute('wall_index', [
            'wallNick' => 'jkirk',
            'wallFilter' => 'self'
        ]);
    }

}
