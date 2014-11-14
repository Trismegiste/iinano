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
    protected $pictureRepo;

    protected function setUp()
    {
        $this->pictureRepo = $this->getMockBuilder('Trismegiste\SocialBundle\Repository\PictureRepository')
                ->disableOriginalConstructor()
                ->getMock();
        $this->sut = new AvatarRepository($this->pictureRepo);

        $this->author = $this->getMock('Trismegiste\Socialist\AuthorInterface');
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
        $this->pictureRepo->expects($this->once())
                ->method('upsertResource');
        $this->author->expects($this->once())
                ->method('getNickname')
                ->will($this->returnValue('kirk'));
        $this->author->expects($this->once())
                ->method('setAvatar');

        $this->sut->updateAvatar($this->author, $img);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCheckResizeProblem()
    {
        $img = \imagecreatetruecolor(50, 50);
        $this->pictureRepo->expects($this->once())
                ->method('upsertResource')
                ->will($this->throwException(new \Exception()));

        $this->sut->updateAvatar($this->author, $img);
    }

}
