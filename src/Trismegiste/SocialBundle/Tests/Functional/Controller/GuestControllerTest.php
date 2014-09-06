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

    public function testAbout()
    {
        $crawler = $this->getPage('guest_about');
        $iter = $crawler->selectLink('SOLID');
        $this->assertCount(1, $iter);
    }

    public function testRegister()
    {
        $crawler = $this->getPage('guest_register');
        $iter = $crawler->selectButton('Register');
        $this->assertCount(1, $iter);
    }

    public function testLoginPage()
    {
        $crawler = $this->getPage('trismegiste_login');
        $form = $crawler->filter('form')->selectButton('Sign in')->form();
        $this->client->submit($form, ['_username' => 'unknown', '_password' => 'passwoes']);
    }

}