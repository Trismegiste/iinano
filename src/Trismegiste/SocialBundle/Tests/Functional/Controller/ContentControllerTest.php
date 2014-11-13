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

    /**
     * @test
     */
    public function initialize()
    {
        $this->getService('dokudoki.collection')->drop();
        $this->addUserFixture('kirk');
    }

    public function testSecuredIndex()
    {
        $loginUrl = $this->generateUrl('trismegiste_login');
        $this->getPage('content_index');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect($loginUrl));
    }

    public function testAuthenticate()
    {
        $this->client->followRedirects(true);

        $crawler = $this->getPage('trismegiste_login');
        $form = $crawler->selectButton('Sign in')->form();
        // set some values
        $form['_username'] = 'kirk';
        $form['_password'] = 'mellon';
        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();

        // redirect to the wall
        $wallUri = $this->generateUrl('wall_index', ['wallNick' => 'kirk', 'wallFilter' => 'self']);
        $this->assertEquals($wallUri, $this->client->getHistory()->current()->getUri());

        // check homepage
        $this->assertEquals(1, $crawler->filter('div#menu a[href$="kirk/self/"]:contains("Myself")')->count());
        $this->assertEquals(0, $crawler->filter('div.netizen')->count());
    }

    public function testSecuredAjaxMore()
    {
        $more = $this->generateUrl('ajax_content_more', ['wallNick' => 'kirk', 'wallFilter' => 'all', 'offset' => 0]);
        $this->client->request('GET', $more);
        $response = $this->client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testDeniedAccessAjaxMore()
    {
        $this->logIn('kirk');
        $more = $this->generateUrl('ajax_content_more', ['wallNick' => 'kirk', 'wallFilter' => 'all', 'offset' => 0]);
        $this->client->request('GET', $more);
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testOnlyAjaxMore()
    {
        $this->logIn('kirk');
        $more = $this->generateUrl('ajax_content_more', ['wallNick' => 'kirk', 'wallFilter' => 'all', 'offset' => 0]);
        $this->client->request('GET', $more, [], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testWallWithOtherNetizen()
    {
        $this->addUserFixture('spock');
        $this->logIn('kirk');
        $crawler = $this->getPage('wall_index', ['wallNick' => 'spock', 'wallFilter' => 'all']);

        $this->assertCount(1, $crawler->filter('div#menu a[href$="kirk/self/"]:contains("Myself")'));
        $this->assertCount(1, $crawler->filter('div.netizen article:contains("Spock")'));
    }

    public function testNotFoundNetizen()
    {
        $this->logIn('kirk');
        $this->getPage('wall_index', ['wallNick' => 'gorn', 'wallFilter' => 'all']);
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

}
