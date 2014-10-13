<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Trismegiste\Socialist\Author;
use Trismegiste\SocialBundle\Security\Profile;

/**
 * WebTestCasePlus is an extended WebTestCase with usefull helper
 */
class WebTestCasePlus extends WebTestCase
{

    /** @var \Symfony\Bundle\FrameworkBundle\Client */
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
                        ->generate($route, $param, \Symfony\Component\Routing\Router::ABSOLUTE_URL);
    }

    /**
     * For phpunit listener
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCurrentResponse()
    {
        return $this->client->getResponse();
    }

    /**
     * @return \Symfony\Component\DomCrawler\Crawler
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
            $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
            $session->set('_security_' . $firewall, serialize($token));
            $session->save();
            $cookie = new Cookie($session->getName(), $session->getId());
            $this->client->getCookieJar()->set($cookie);
// @todo is this redundant with the lines above ?
            $this->getService('security.context')->setToken($token);
        }
    }

    protected function addUserFixture($nickname)
    {
        $repo = $this->getService('social.netizen.repository');
        $user = $repo->create($nickname, 'mellon');
        $user->getAuthor()->setAvatar('00.jpg');
        $prof = new Profile();
        $prof->fullName = ucfirst($nickname);
        $user->setProfile($prof);
        $repo->persist($user);
    }

    protected function createAuthor($name)
    {
        $author = new Author($name);
        $author->setAvatar('00.jpg');

        return $author;
    }

    /**
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function ajaxPost($uri)
    {
        return $this->client->request('POST', $uri, [], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

    }

}
