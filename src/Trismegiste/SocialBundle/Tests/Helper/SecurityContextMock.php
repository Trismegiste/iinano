<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Helper;

use Trismegiste\Socialist\AuthorInterface;

/**
 * SecurityContextMock is a factory for mocking a security context
 */
trait SecurityContextMock
{

    public function createSecurityContextMock(AuthorInterface $author)
    {
        $currentUser = $this->getMockBuilder('Trismegiste\SocialBundle\Security\Netizen')
                ->disableOriginalConstructor()
                ->getMock();
        $currentUser->expects($this->any())
                ->method('getAuthor')
                ->will($this->returnValue($author));

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->any())
                ->method('getUser')
                ->will($this->returnValue($currentUser));

        $security = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $security->expects($this->any())
                ->method('getToken')
                ->will($this->returnValue($token));

        return $security;
    }

}
