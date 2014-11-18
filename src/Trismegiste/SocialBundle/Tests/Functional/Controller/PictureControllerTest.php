<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

/**
 * PictureControllerTest tests the PictureController
 */
class PictureControllerTest extends WebTestCasePlus
{

    protected $collection;

    protected function setUp()
    {
        parent::setUp();
        $this->collection = $this->getService('dokudoki.collection');
    }

    /**
     * @test
     */
    public function initialize()
    {
        $this->collection->drop();
        $this->assertCount(0, $this->collection->find());
        $this->addUserFixture('kirk');
    }

    public function testNotFound()
    {
        $this->logIn('kirk');
        $this->getPage('picture_get', ['size' => 'full', 'storageKey' => '07f0d.jpg']);
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode()); // headers->get('Content-Type')
    }

}
