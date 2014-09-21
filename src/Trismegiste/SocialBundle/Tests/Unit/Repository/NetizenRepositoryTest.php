<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Trismegiste\SocialBundle\Repository\NetizenRepository;
use Trismegiste\SocialBundle\Security\Netizen;

/**
 * NetizenRepositoryTest tests NetizenRepository
 */
class NetizenRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /** @var NetizenRepository */
    protected $sut;
    protected $repository;
    protected $encoder;
    protected $storage;

    protected function setUp()
    {
        $this->encoder = $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $this->repository = $this->getMock('Trismegiste\Yuurei\Persistence\RepositoryInterface');
        $this->storage = $this->getMock('Trismegiste\SocialBundle\Repository\AvatarRepository', [], [], '', false);

        $this->sut = new NetizenRepository($this->repository, $this->encoder, 'netizen', $this->storage);
    }

    public function testFindByNickname()
    {
        $this->repository->expects($this->once())
                ->method('findOne')
                ->with($this->equalTo(['-class' => 'netizen', 'author.nickname' => 'kirk']));
        $this->sut->findByNickname('kirk');
    }

    public function testFindByPk()
    {
        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo(123));
        $this->sut->findByPk(123);
    }

    public function testUserCreation()
    {
        $this->encoder->expects($this->once())
                ->method('getEncoder')
                ->will($this->returnValue($this->getMock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface')));

        $user = $this->sut->create('kirk', 'ncc1701');

        $this->assertInstanceOf('Trismegiste\Socialist\Author', $user->getAuthor());
        $this->assertInstanceOf('Trismegiste\SocialBundle\Security\Profile', $user->getProfile());
        $this->assertTrue(class_exists($user->getCredentialType()));

        return $user;
    }

    /**
     * @depends testUserCreation
     */
    public function testPersist(Netizen $user)
    {
        $this->sut->persist($user);
        $this->assertNotNull($user->getAuthor()->getAvatar());
    }

    /**
     * @depends testUserCreation
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
     * @depends testUserCreation
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

}
