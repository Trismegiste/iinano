<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;

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

    public function testFirstRequest()
    {
        $img = \imagecreatetruecolor(20, 20);
        $path = tempnam(sys_get_temp_dir(), 'pic');
        \imagejpeg($img, $path);
        $fh = new UploadedFile($path, 'functest.jpg', null, null, null, true);

        $doc = new \Trismegiste\Socialist\Picture($this->createAuthor('spock'));

        $this->getService('social.picture.storage')->insertUpload($doc, $fh);

        $this->logIn('kirk');
        $this->getPage('picture_get', ['size' => 'full', 'storageKey' => $doc->getStorageKey()]);
    }

}
