<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * SimplePostControllerTest tests SimplePostController
 */
class SimplePostControllerTest extends WebTestCase
{

    protected $client = null;

    protected function setUp()
    {
        $this->client = static::createClient();
    }

    protected function getService($id)
    {
        return $this->client->getContainer()->get($id);
    }

    protected function generateUrl($route, $param = [])
    {
        return $this->getService('router')->generate($route, $param, \Symfony\Component\Routing\Router::ABSOLUTE_URL);
    }

    protected function getPage($route, $param = [])
    {
        return $this->client->request('GET', $this->generateUrl($route, $param));
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
        $crawler = $this->getPage('trismegiste_login');
        $form = $crawler->selectButton('Sign in')->form();
        // set some values
        $form['_username'] = 'Kirk';
        $form['_password'] = 'aaaa';
        $this->client->submit($form);
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect($this->generateUrl('content_index')));
    }

}