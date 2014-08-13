<?php

namespace Trismegiste\SocialBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VertexControllerTest extends WebTestCase
{

    static $client;

    static public function setupBeforeClass()
    {
        static::$client = static::createClient();
    }

    protected function tearDown()
    {
        //don't shutdown the kernel
    }

    public function testIndex()
    {
        $client = static::$client;

        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

}

