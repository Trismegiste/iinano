<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Trismegiste\SocialBundle\Repository\AvatarRepository;
use Trismegiste\Socialist\Author;
use Trismegiste\SocialBundle\Security\Profile;

/**
 * AvatarRepositoryTest tests AvatarRepository
 */
class AvatarRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /** @var AvatarRepository */
    protected $sut;
    protected $imageTool;
    protected $tmpDir;

    protected function setUp()
    {
        $this->imageTool = $this->getMockBuilder('Trismegiste\SocialBundle\Utils\ImageRefiner')
                ->disableOriginalConstructor()
                ->getMock();
        $this->tmpDir = sys_get_temp_dir();
        $this->sut = new AvatarRepository($this->tmpDir, $this->imageTool, 50);

        $this->dummyFile = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
                ->disableOriginalConstructor()
                ->getMock();

        $this->author = new Author('kirk');
        $this->profile = new Profile();
    }

    public function testConstructor()
    {
        $this->assertAttributeEquals($this->tmpDir . DIRECTORY_SEPARATOR, 'storage', $this->sut);
    }

    public function testAbsolutePath()
    {
        $path = $this->sut->getAvatarAbsolutePath('aaa.jpg');
        $path_parts = pathinfo($path);

        $this->assertEquals([
            'dirname' => $this->tmpDir,
            'basename' => 'aaa.jpg',
            'extension' => 'jpg',
            'filename' => 'aaa'
                ], $path_parts);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCheckOnNull()
    {
        $this->sut->updateAvatar($this->author, null);
    }

    public function testCheckOnMime()
    {
        $this->dummyFile->expects($this->once())
                ->method('getMimeType')
                ->will($this->returnValue('image/png'));
        $this->dummyFile->expects($this->never())
                ->method('move');

        $this->sut->updateAvatar($this->author, $this->profile, $this->dummyFile);
        $this->assertEquals('01.jpg', $this->author->getAvatar());
    }

    public function testCheckOnMoveAndNaming()
    {
        $this->dummyFile->expects($this->once())
                ->method('getMimeType')
                ->will($this->returnValue('image/jpeg'));
        $this->dummyFile->expects($this->once())
                ->method('move');

        $this->sut->updateAvatar($this->author, $this->profile, $this->dummyFile);
        $this->assertEquals('6b69726b.jpg', $this->author->getAvatar());
    }

}
