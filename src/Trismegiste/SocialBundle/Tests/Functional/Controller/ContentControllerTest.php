<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

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
        $this->addUserFixture('kirk');
        $this->client->followRedirects(true);

        $crawler = $this->getPage('trismegiste_login');
        $form = $crawler->selectButton('Sign in')->form();
        // set some values
        $form['_username'] = 'kirk';
        $form['_password'] = 'mellon';
        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();

        // redirect to the wall
        $wallUri = $this->generateUrl('wall_index', ['wallNick' => 'kirk', 'wallFilter' => 'all']);
        $this->assertEquals($wallUri, $this->client->getHistory()->current()->getUri());

        // check homepage
        $this->assertEquals(1, $crawler->filter('nav.top-bar a:contains("kirk")')->count());
    }

}