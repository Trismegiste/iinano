<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Trismegiste\SocialBundle\Repository\AvatarRepository;

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

        $this->author = $this->getMock('Trismegiste\Socialist\AuthorInterface');
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

    public function testCheckOnResize()
    {
        $img = \imagecreatetruecolor(50, 50);
        $this->imageTool->expects($this->once())
                ->method('makeSquareThumbnailFrom');
        $this->author->expects($this->once())
                ->method('getNickname')
                ->will($this->returnValue('kirk'));
        $this->author->expects($this->once())
                ->method('setAvatar')
                ->with($this->equalTo('6b69726b.jpg'));

        $this->sut->updateAvatar($this->author, $img);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCheckResizeProblem()
    {
        $img = \imagecreatetruecolor(50, 50);
        $this->imageTool->expects($this->once())
                ->method('makeSquareThumbnailFrom')
                ->will($this->throwException(new \Exception));

        $this->sut->updateAvatar($this->author, $img);
    }

}
