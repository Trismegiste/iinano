<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Trismegiste\OAuthBundle\Security\Token;
use Trismegiste\SocialBundle\Ticket\EntranceFee;
use Trismegiste\SocialBundle\Ticket\Ticket;
use Trismegiste\Socialist\Author;

/**
 * WebTestCasePlus is an extended WebTestCase with usefull helper
 */
class WebTestCasePlus extends WebTestCase
{

    /** @var Client */
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
        return $this->getService('router')
                        ->generate($route, $param, Router::ABSOLUTE_URL);
    }

    /**
     * For phpunit listener
     *
     * @return Response
     */
    public function getCurrentResponse()
    {
        return $this->client->getResponse();
    }

    /**
     * @return Crawler
     */
    protected function getPage($route, $param = [])
    {
        return $this->client->request('GET', $this->generateUrl($route, $param));
    }

    /**
     * Do not use this method in dataProvider since they are called before setUp !
     */
    protected function logIn($nick)
    {
        $repo = $this->getService('social.netizen.repository');
        $user = $repo->findByNickname($nick);

        if (!is_null($user)) {
            $session = $this->client->getContainer()->get('session');
            $firewall = 'secured_area';
            $cred = $user->getCredential();
            $token = new Token($firewall, $cred->getProviderKey(), $cred->getUid(), $user->getRoles());
            $token->setUser($user);
            $session->set('_security_' . $firewall, serialize($token));
            $session->save();
            $cookie = new Cookie($session->getName(), $session->getId());
            $this->client->getCookieJar()->set($cookie);
            $this->getService('security.context')->setToken($token);
        }
    }

    protected function addUserFixture($nickname, $uid = '123456789')
    {
        $user = $this->getService('security.netizen.factory')->create($nickname, 'dummy', $uid);
        $fee = new EntranceFee();
        $fee->setDurationValue(12);
        $user->addTicket(new Ticket($fee));
        $prof = $user->getProfile();
        $prof->fullName = ucfirst($nickname);
        $prof->gender = 'xy';
        $prof->dateOfBirth = DateTime::createFromFormat(DateTime::ISO8601, '1918-10-11T00:00:00Z');
        $prof->email = $nickname . '@server.tld';
        $this->getService('social.netizen.repository')->persist($user);
    }

    protected function createAuthor($name)
    {
        $author = new Author($name);
        $author->setAvatar('00.jpg');

        return $author;
    }

    /**
     * @return Crawler
     */
    protected function ajaxPost($uri)
    {
        return $this->client->request('POST', $uri, [], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
    }

    protected function assertCurrentRoute($route, $param = [])
    {
        $this->assertEquals($this->generateUrl($route, $param), $this->client->getHistory()->current()->getUri());
    }

    public function getJsonResponse()
    {
        return json_decode($this->client->getResponse()->getContent());
    }

}
