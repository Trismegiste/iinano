<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Security\Credential;

use Trismegiste\SocialBundle\Security\Credential\OAuth;

/**
 * OAuthTest tests OAuth credential entity
 */
class OAuthTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $cred = new OAuth('123', 'dummy');
        $this->assertEquals('123', $cred->getUid());
        $this->assertEquals('dummy', $cred->getProviderKey());
    }

}
