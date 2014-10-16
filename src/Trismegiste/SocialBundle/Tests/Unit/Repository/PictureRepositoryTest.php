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
        $this->sut = new PictureRepository(sys_get_temp_dir());
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
        $file->expects($this->once())
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
     */
    public function testBadDirectory()
    {
        new PictureRepository(__DIR__ . 'yopyop');
    }

}
