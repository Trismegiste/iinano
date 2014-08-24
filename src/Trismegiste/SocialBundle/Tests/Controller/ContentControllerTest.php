<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Controller;

/**
 * ContentControllerTest tests ContentController
 */
class ContentControllerTest extends WebTestCasePlus
{

    public function testSecuredIndex()
    {
        $loginUrl = $this->generateUrl('trismegiste_login');
        $this->getPage('content_index');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect($loginUrl));
    }

    public function testAuthenticate()
    {
        $repo = $this->getService('social.netizen.repository');
        $user = $repo->create('kirk');
        $repo->persist($user);

        $crawler = $this->getPage('trismegiste_login');
        $form = $crawler->selectButton('Sign in')->form();
        // set some values
        $form['_username'] = 'kirk';
        $form['_password'] = 'aaaa';
        $this->client->submit($form);
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect($this->generateUrl('content_index')));
        $crawler = $this->client->followRedirect();
        // check homepage
        $this->assertEquals(1, $crawler->filter('nav.top-bar a:contains("kirk")')->count());
    }

}