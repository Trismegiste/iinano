<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\FollowerType;
use Trismegiste\Socialist\Author;

/**
 * FollowerTypeTest tests FollowerType
 */
class FollowerTypeTest extends FormTestCase
{

    protected $repository;
    protected $security;
    protected $currentUser;
    protected $token;

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

        $this->token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->token->expects($this->any())
                ->method('getUser')
                ->will($this->returnValue($this->currentUser));

        $this->security = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->security->expects($this->any())
                ->method('getToken')
                ->will($this->returnValue($this->token));

        $this->repository = $this->getMock('Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface');
        $this->repository->expects($this->any())
                ->method('findByNickname')
                ->with($this->equalTo('spock'))
                ->will($this->returnValue($follower));

        return new FollowerType($this->repository, $this->security);
    }

    public function getInvalidInputs()
    {
        return [
            ['scotty', null]
        ];
    }

    public function getValidInputs()
    {
        return [
            ['spock', new Author('spock')]
        ];
    }

}
