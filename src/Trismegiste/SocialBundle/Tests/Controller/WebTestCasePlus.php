<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\Socialist\Author;

/**
 * WebTestCasePlus is an extended WebTestCase with usefull helper
 */
class WebTestCasePlus extends WebTestCase
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

    protected function logIn($nick)
    {
        $repo = $this->getService('social.netizen.repository');
        $user = $repo->findByNickname($nick);

        $session = $this->client->getContainer()->get('session');
        $firewall = 'secured_area';
        $token = new UsernamePasswordToken($user, null, $firewall, array('ROLE_USER'));
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    protected function addUserFixture($nickname)
    {
        $repo = $this->getService('social.netizen.repository');
        $user = $repo->create($nickname);
        $repo->persist($user);
    }

}