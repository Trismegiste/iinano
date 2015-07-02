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
        $loginUrl = $this->generateUrl('trismegiste_oauth_connect');
        $this->getPage('content_index');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect($loginUrl));
    }

    public function testAuthenticate()
    {
        $this->client->followRedirects(true);
        $this->logIn('kirk');

        $crawler = $this->getPage('content_index');
        // redirect to the wall
        $this->assertCurrentRoute('wall_index', ['wallNick' => 'kirk', 'wallFilter' => 'self']);

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

    public function testDeniedAccessAjaxMoreWithoutAjaxCall()
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

    public function testCommentaryPreview()
    {
        $this->logIn('kirk');
        $preview = $this->client->getContainer()->getParameter('social.commentary_preview');

        /* @var $repo \Trismegiste\SocialBundle\Repository\PublishingRepository */
        $repo = $this->getService('social.publishing.repository');
        $pub = $repo->create('small');
        for ($k = 0; $k < $preview + 1; $k++) {
            $auth = new \Trismegiste\Socialist\Author('user' . $k);
            $auth->setAvatar('abcdef.jpg');
            $pub->attachCommentary(new \Trismegiste\Socialist\Commentary($auth));
        }
        $repo->persist($pub);
        $pk = (string) $pub->getId();

        $crawler = $this->getPage('wall_index', ['wallNick' => 'kirk', 'wallFilter' => 'self']);
        $this->assertCount($preview, $crawler->filter("div[data-social-commentary-lst='$pk'] .commentary"));

        return $pk;
    }

    /**
     * @depends testCommentaryPreview
     */
    public function testAjaxGetCommentary($pk)
    {
        $this->logIn('kirk');
        $preview = $this->client->getContainer()->getParameter('social.commentary_preview');

        $more = $this->generateUrl('ajax_commentary_more', ['wallNick' => 'kirk', 'wallFilter' => 'all', 'id' => $pk]);
        $crawler = $this->client->request('GET', $more, [], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertCount($preview + 1, $crawler->filter(".commentary"));
    }

    public function testPublishingNotFound()
    {
        $this->logIn('kirk');
        $more = $this->generateUrl('ajax_commentary_more', ['wallNick' => 'kirk', 'wallFilter' => 'all', 'id' => '446df529e3f4349958f5ebdc']);
        $this->client->request('GET', $more, [], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }

}
