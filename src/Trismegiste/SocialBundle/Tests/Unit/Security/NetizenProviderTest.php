<?php

/*
 * iinano
 */

namespace Trismegiste \SocialBundle\Tests\Unit\Security;

use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\SocialBundle\Security\NetizenProvider;
use Trismegiste\Socialist\Author;

/**
 * NetizenProviderTest tests NetizenProvider
 */
class NetizenProviderTest extends \PHPUnit_Framework_TestCase
{

    /** @var NetizenProvider */
    protected $sut;
    protected $repository;
    protected $someUser;
    protected $concreteUser;

    protected function setUp()
    {
        $this->someUser = $this->getMockBuilder('Trismegiste\SocialBundle\Security\Netizen')
                ->disableOriginalConstructor()
                ->getMock();
        $this->concreteUser = new Netizen(new Author('kirk')); // mainly because supportsClass break Liskov
        $this->repository = $this->getMock('Trismegiste\Yuurei\Persistence\RepositoryInterface');
        $this->sut = new NetizenProvider($this->repository, 'netizen');
    }

    public function testSupportedClass()
    {
        $this->assertTrue($this->sut->supportsClass('Trismegiste\SocialBundle\Security\Netizen'));
    }

    public function testloadUserFound()
    {
        $this->repository->expects($this->once())
                ->method('findOne')
                ->with($this->equalTo([
                            'author.nickname' => 'kirk',
                            '-class' => 'netizen'
                ]))
                ->will($this->returnValue($this->concreteUser));

        $this->assertEquals($this->concreteUser, $this->sut->loadUserByUsername('kirk'));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testloadUserNotFound()
    {
        $this->repository->expects($this->once())
                ->method('findOne')
                ->with($this->equalTo([
                            'author.nickname' => 'kirk',
                            '-class' => 'netizen'
                ]))
                ->will($this->returnValue(null));

        $this->sut->loadUserByUsername('kirk');
    }

    public function testRefreshUserOk()
    {
        $pk = new \MongoId();
        $this->concreteUser->setId($pk);

        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($pk)
                ->will($this->returnValue($this->concreteUser));

        $this->assertEquals($this->concreteUser, $this->sut->refreshUser($this->concreteUser));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function testRefreshBadUser()
    {
        $randomUser = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $randomUser->expects($this->never())
                ->method('getId');

        $this->repository->expects($this->never())
                ->method('findByPk');

        $this->sut->refreshUser($randomUser);
    }

    public function testFindUserByOAuth()
    {
        $this->repository->expects($this->once())
                ->method('findOne')
                ->with($this->equalTo([
                            'cred' => [
                                '-class' => 'oauth',
                                'uid' => '1701',
                                'provider' => 'ufp'
                            ],
                            '-class' => 'netizen'
                ]))
                ->will($this->returnValue($this->concreteUser));

        $this->assertEquals($this->concreteUser, $this->sut->findByOauthId('ufp', '1701'));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testNotFoundUserByOAuth()
    {
        $this->assertEquals($this->concreteUser, $this->sut->findByOauthId('ufp', '1701'));
    }

}
