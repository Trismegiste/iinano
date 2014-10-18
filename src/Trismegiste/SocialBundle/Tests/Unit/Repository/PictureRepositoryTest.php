<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Trismegiste\SocialBundle\Repository\PictureRepository;
use Trismegiste\Socialist\Author;
use Trismegiste\Socialist\Picture;

/**
 * PictureRepositoryTest tests PictureRepository
 */
class PictureRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /** @var PictureRepository */
    protected $sut;
    protected $author;
    protected $picture;

    protected function setUp()
    {
        $this->sut = new PictureRepository(sys_get_temp_dir(), sys_get_temp_dir(), ['full' => 1000]);
        $this->author = new Author('kirk');
        $this->picture = new Picture($this->author);
    }

    public function testValid()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
                ->disableOriginalConstructor()
                ->getMock();
        $file->expects($this->atLeastOnce())
                ->method('getMimeType')
                ->will($this->returnValue('image/png'));
        $file->expects($this->atLeastOnce())
                ->method('isValid')
                ->will($this->returnValue(true));
        $file->expects($this->atLeastOnce())
                ->method('getPathname')
                ->will($this->returnValue(__DIR__ . '/../../../Resources/public/img/mascot.png'));
        $file->expects($this->never())  // I don't keep original picture for saving storage space, I don't make a clone of Picasa or Flickr
                ->method('move');

        $this->sut->store($this->picture, $file);

        $this->assertEquals('image/png', $this->picture->getMimeType());
        $this->assertRegexp('#^[\da-f]{40}\.png$#', $this->picture->getStorageKey());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage incomplete
     */
    public function testBadUpload()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
                ->disableOriginalConstructor()
                ->getMock();
        $file->expects($this->never())
                ->method('getMimeType')
                ->will($this->returnValue('image/png'));
        $file->expects($this->any())
                ->method('isValid')
                ->will($this->returnValue(false));
        $file->expects($this->never())
                ->method('move');

        $this->sut->store($this->picture, $file);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage application/octet-stream
     */
    public function testBadType()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
                ->disableOriginalConstructor()
                ->getMock();
        $file->expects($this->once())
                ->method('getMimeType')
                ->will($this->returnValue('application/octet-stream'));
        $file->expects($this->any())
                ->method('isValid')
                ->will($this->returnValue(true));
        $file->expects($this->never())
                ->method('move');

        $this->sut->store($this->picture, $file);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage for storage
     */
    public function testBadStorageDirectory()
    {
        new PictureRepository(__DIR__ . 'yopyop', __DIR__, []);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage for cache
     */
    public function testBadCacheDirectory()
    {
        new PictureRepository(__DIR__, __DIR__ . 'yopyop', []);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage size configuration
     */
    public function testBadSizeConfig()
    {
        new PictureRepository(__DIR__, __DIR__, ['yo' => 42]);
    }

}
