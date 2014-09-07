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

}