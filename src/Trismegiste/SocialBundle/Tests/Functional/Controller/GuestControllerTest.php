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
        $crawler = $this->getPage('guest_register');
        $form = $crawler->selectButton('Register')->form();
        $this->client->submit($form, ['netizen_register' => [
                'nickname' => 'spock',
                'password' => ['password' => 'idic', 'confirm_password' => 'idic'],
                'fullName' => 'Spock',
                'gender' => 'xy',
                'email' => 'dfsdfssdf@sddsqsdq.fr',
                'dateOfBirth' => ['year' => 1984, 'month' => 11, 'day' => 13]
        ]]);

        $this->assertEquals($this->generateUrl('confirm_buy_ticket'), $this->client->getHistory()->current()->getUri());

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

    public function testLoginPageFailWithoutPayment()
    {
        $crawler = $this->getPage('trismegiste_login');
        $form = $crawler->filter('form')->selectButton('Sign in')->form();
        $this->client->submit($form, ['_username' => 'spock', '_password' => 'idic']);

        $this->assertEquals($this->generateUrl('confirm_buy_ticket'), $this->client->getHistory()->current()->getUri());
    }

    public function testLoginPageWithPayment()
    {
        $crawler = $this->getPage('trismegiste_login');
        $form = $crawler->filter('form')->selectButton('Sign in')->form();
        $this->client->submit($form, ['_username' => 'spock', '_password' => 'idic']);
        $this->assertEquals($this->generateUrl('confirm_buy_ticket'), $this->client->getHistory()->current()->getUri());

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

}
