<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Controller;

/**
 * GuestControllerTest tests public pages
 */
class GuestControllerTest extends WebTestCasePlus
{

    public function testAbout()
    {
        $crawler = $this->getPage('guest_about');
        $iter = $crawler->selectLink('SOLID');
        $this->assertCount(1, $iter);
    }

}