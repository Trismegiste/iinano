<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Controller;

/**
 * ForeignerControllerTest tests public pages
 */
class ForeignerControllerTest extends WebTestCasePlus
{

    public function testAbout()
    {
        $crawler = $this->getPage('foreigner_about');
        $iter = $crawler->selectLink('SOLID');
        $this->assertCount(1, $iter);
    }

}