<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller\Admin;

/**
 * CouponControllerTest tests CouponController
 */
class CouponControllerTest extends AdminControllerTestCase
{

    /**
     * @test
     */
    public function initialize()
    {
        parent::initialize();
        $repo = $this->getService('dokudoki.repository');
        $coupon = new \Trismegiste\SocialBundle\Ticket\Coupon();
        $coupon->hashKey = 'ABCDEF';
        $repo->persist($coupon);
    }

    public function testAccessListingCoupon()
    {
        $this->assertSecuredPage('admin_coupon_listing');
        $crawler = $this->client->getCrawler();
        $this->assertCount(1, $crawler->filter('div.content td:contains("ABCDEF")'));
    }

    public function testCreateNewCoupon()
    {
        $this->client->followRedirects();
        $crawler = $this->getPage('admin_coupon_create');
        $form = $crawler->filter('div.content form')->form();
        $this->client->submit($form, ['social_coupon' => [
                'haskKey' => 'YUIOP',
        ]]);

        $this->assertCount(1, $crawler->filter('div.content td:contains("YUIOP")'));
    }

}
