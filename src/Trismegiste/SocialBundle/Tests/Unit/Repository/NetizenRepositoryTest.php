<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Trismegiste\SocialBundle\Repository\NetizenRepository;
use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\Socialist\Author;
use Trismegiste\SocialBundle\Security\Profile;

/**
 * NetizenRepositoryTest tests NetizenRepository
 */
class NetizenRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /** @var NetizenRepository */
    protected $sut;
    protected $repository;
    protected $storage;

    protected function setUp()
    {
        $this->repository = $this->getMock('Trismegiste\Yuurei\Persistence\RepositoryInterface');
        $this->storage = $this->getMock('Trismegiste\SocialBundle\Repository\AvatarRepository', [], [], '', false);

        $this->sut = new NetizenRepository($this->repository, 'netizen', $this->storage);
    }

    public function testFindByNickname()
    {
        $this->repository->expects($this->once())
                ->method('findOne')
                ->with($this->equalTo(['-class' => 'netizen', 'author.nickname' => 'kirk']));
        $this->sut->findByNickname('kirk');
    }

    /**
     * @expectedException \LogicException
     */
    public function testInvalidTypeFindByPk()
    {
        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo(123));
        $this->sut->findByPk(123);
    }

    public function testFindByPk()
    {
        $userMock = $this->getMockBuilder('Trismegiste\SocialBundle\Security\Netizen')
                ->disableOriginalConstructor()
                ->getMock();
        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo(123))
                ->will($this->returnValue($userMock));
        $this->sut->findByPk(123);
    }

    public function testPersist()
    {
        $user = new Netizen(new Author('kirk'));
        $user->setProfile(new Profile());
        $this->sut->persist($user);
        $this->assertNotNull($user->getAuthor()->getAvatar());

        return $user;
    }

    /**
     * @depends testPersist
     */
    public function testUpdateAvatar(Netizen $user)
    {
        $this->storage->expects($this->once())
                ->method('updateAvatar');

        $img = \imagecreatetruecolor(10, 10);
        $this->sut->updateAvatar($user, $img);
    }

    public function testIsExistingNicknameFalse()
    {
        $this->assertFalse($this->sut->isExistingNickname('spock'));
    }

    public function testIsExistingNicknameTrue()
    {
        $this->repository->expects($this->once())
                ->method('findOne')
                ->will($this->returnValue("user"));

        $this->assertTrue($this->sut->isExistingNickname('spock'));
    }

    /**
     * @depends testPersist
     * @expectedException \RuntimeException
     */
    public function testSomethingWentWrongForUpdatingAvatar(Netizen $user)
    {
        $this->storage->expects($this->once())
                ->method('updateAvatar')
                ->will($this->throwException(new \Exception));

        $img = \imagecreatetruecolor(10, 10);
        $this->sut->updateAvatar($user, $img);
    }

    public function testFindBatchNickname()
    {
        $this->repository->expects($this->once())
                ->method('find')
                ->with($this->equalTo(['-class' => 'netizen', 'author.nickname' => ['$in' => ['spock']]]));

        $this->sut->findBatchNickname(new \ArrayIterator(['spock' => true]));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadParamCtor()
    {
        new NetizenRepository($this->repository, 123, $this->storage);
    }

    public function testSearchUser()
    {
        $this->repository->expects($this->once())
                ->method('find')
                ->with($this->equalTo(['-class' => 'netizen', 'author.nickname' => new \MongoRegex('/^user/')]));

        $this->sut->search('user');
    }

}
