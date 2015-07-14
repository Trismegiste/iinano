<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Trismegiste\SocialBundle\Repository\PrivateMessageRepository;
use Trismegiste\Socialist\Author;
use Trismegiste\Yuurei\Persistence\CollectionIterator;
use Trismegiste\Socialist\PrivateMessage;
use Trismegiste\SocialBundle\Security\TicketVoter;

/**
 * PrivateMessageRepositoryTest tests PrivateMessageRepository
 */
class PrivateMessageRepositoryTest extends \PHPUnit_Framework_TestCase
{

    use \Trismegiste\SocialBundle\Tests\Helper\SecurityContextMock;

    /** @var PrivateMessageRepository */
    protected $sut;
    protected $repository;
    protected $security;
    protected $source;
    protected $target;
    protected $document;

    protected function setUp()
    {
        $this->source = new Author('kirk');
        $this->target = new Author('spock');

        $this->repository = $this->getMock('Trismegiste\Yuurei\Persistence\RepositoryInterface');
        $this->security = $this->createSecurityContextMock($this->source);
        $this->sut = new PrivateMessageRepository($this->repository, $this->security, 'alias');

        $this->document = new PrivateMessage($this->source, $this->target);
    }

    protected function createMockCursor()
    {
        return new CollectionIterator($this->getMock('MongoCursor', [], [], '', false), $this->repository);
    }

    public function testReceived()
    {
        $this->security->expects($this->once())
                ->method('isGranted')
                ->with($this->equalTo(TicketVoter::SUPPORTED_ATTRIBUTE))
                ->will($this->returnValue(true));

        $this->repository->expects($this->once())
                ->method('find')
                ->will($this->returnValue($this->createMockCursor()));

        $this->sut->findAllReceived();
    }

    public function testSent()
    {
        $this->security->expects($this->once())
                ->method('isGranted')
                ->with($this->equalTo(TicketVoter::SUPPORTED_ATTRIBUTE))
                ->will($this->returnValue(true));

        $this->repository->expects($this->once())
                ->method('find')
                ->will($this->returnValue($this->createMockCursor()));

        $this->sut->findAllSent();
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testReceivedNotLogged()
    {
        $this->sut->findAllReceived();
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testSentNotLogged()
    {
        $this->sut->findAllReceived();
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testNewMessageToNonFollower()
    {
        $this->security->expects($this->at(0))
                ->method('isGranted')
                ->with($this->equalTo('LISTENER'))
                ->will($this->returnValue(false));

        $this->sut->createNewMessageTo($this->target);
    }

    public function testNewMessageToFollower()
    {
        $this->security->expects($this->at(0))
                ->method('isGranted')
                ->with($this->equalTo('LISTENER'))
                ->will($this->returnValue(true));

        $this->security->expects($this->at(1))
                ->method('isGranted')
                ->with($this->equalTo(TicketVoter::SUPPORTED_ATTRIBUTE))
                ->will($this->returnValue(true));

        $this->assertInstanceOf('Trismegiste\Socialist\PrivateMessage', $this->sut->createNewMessageTo($this->target));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @expectedExceptionMessage sender
     */
    public function testOnlySenderCanPersist()
    {
        $this->security->expects($this->once())
                ->method('isGranted')
                ->with($this->equalTo(TicketVoter::SUPPORTED_ATTRIBUTE))
                ->will($this->returnValue(true));

        $msg = new \Trismegiste\Socialist\PrivateMessage($this->target, $this->source);

        $this->sut->persist($msg);
    }

    public function testPersist()
    {
        $this->security->expects($this->once())
                ->method('isGranted')
                ->with($this->equalTo(TicketVoter::SUPPORTED_ATTRIBUTE))
                ->will($this->returnValue(true));

        $this->repository->expects($this->once())
                ->method('persist');

        $msg = new \Trismegiste\Socialist\PrivateMessage($this->source, $this->target);

        $this->sut->persist($msg);
    }

    /**
     * @expectedException \LogicException
     */
    public function testInvalidPkPersistAsRead()
    {
        $this->sut->persistAsRead(123);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @expectedExceptionMessage receipient
     */
    public function testOnlyReceipientCanPersistAsRead()
    {
        $this->security->expects($this->once())
                ->method('isGranted')
                ->with($this->equalTo(TicketVoter::SUPPORTED_ATTRIBUTE))
                ->will($this->returnValue(true));

        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo(123))
                ->will($this->returnValue($this->document));

        $this->sut->persistAsRead(123);
    }

    public function testPersistAsRead()
    {
        $received = new \Trismegiste\Socialist\PrivateMessage($this->target, $this->source);

        $this->security->expects($this->once())
                ->method('isGranted')
                ->with($this->equalTo(TicketVoter::SUPPORTED_ATTRIBUTE))
                ->will($this->returnValue(true));

        $this->repository->expects($this->once())
                ->method('findByPk')
                ->with($this->equalTo(123))
                ->will($this->returnValue($received));

        $this->repository->expects($this->once())
                ->method('persist')
                ->with($this->attributeEqualTo('read', true));

        $this->sut->persistAsRead(123);
    }

    public function testGetLastReceived()
    {
        $this->security->expects($this->once())
                ->method('isGranted')
                ->with($this->equalTo(TicketVoter::SUPPORTED_ATTRIBUTE))
                ->will($this->returnValue(true));

        $this->repository->expects($this->once())
                ->method('find')
                ->will($this->returnValue($this->createMockCursor()));

        $pm = $this->sut->getLastReceived();
        $this->assertNull($pm);
    }

}
