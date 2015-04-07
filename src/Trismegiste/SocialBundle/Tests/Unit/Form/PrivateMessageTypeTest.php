<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\FollowerType;
use Trismegiste\Socialist\Author;
use Trismegiste\SocialBundle\Form\PrivateMessageType;
use Trismegiste\Socialist\PrivateMessage;

/**
 * PrivateMessageTypeTest tests PrivateMessageType
 */
class PrivateMessageTypeTest extends FormTestCase
{

    use \Trismegiste\SocialBundle\Tests\Helper\SecurityContextMock;

    protected $repository;
    protected $security;
    protected $currentUser;

    /**
     * @return \Trismegiste\SocialBundle\Security\Netizen
     */
    private function createUser($nick)
    {
        return new \Trismegiste\SocialBundle\Security\Netizen(new Author($nick));
    }

    protected function createType()
    {
        $this->currentUser = $this->createUser('kirk');
        $follower = $this->createUser('spock');
        $follower->follow($this->currentUser); // kirk is followed by spock

        $this->security = $this->createSecurityContextMockFromUser($this->currentUser);

        $this->repository = $this->getMock('Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface');
        $this->repository->expects($this->any())
                ->method('findByNickname')
                ->with($this->equalTo('spock'))
                ->will($this->returnValue($follower));

        $pmRepo = $this->getMockBuilder('Trismegiste\SocialBundle\Repository\PrivateMessageRepository')
                ->disableOriginalConstructor()
                ->getMock();
        $pmRepo->expects($this->once())
                ->method('createNewMessageTo')
                ->willReturn($this->createPM());

        return [
            new PrivateMessageType($pmRepo),
            new FollowerType($this->repository, $this->security)
        ];
    }

    public function getInvalidInputs()
    {
        $obj = $this->createPM();
        $obj->setMessage('gg');
        return [
            [
                ['target' => 'spock', 'message' => 'gg'],
                $obj,
                ['message']
            ]
        ];
    }

    public function getValidInputs()
    {
        $obj = $this->createPM();
        $obj->setMessage('lol');
        return [
            [
                ['target' => 'spock', 'message' => 'lol'],
                $obj
            ]
        ];
    }

    protected function createPM()
    {
        $pm = new PrivateMessage(new Author('kirk'), new Author('spock'));
        $refl = new \Trismegiste\Yuurei\Utils\InjectionClass($pm);
        $refl->injectProperty($pm, 'sentAt', new \DateTime('2019-04-01'));

        return $pm;
    }

}
