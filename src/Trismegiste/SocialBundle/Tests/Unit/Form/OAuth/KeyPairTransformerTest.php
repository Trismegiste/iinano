<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form\Oauth;

use Trismegiste\SocialBundle\Form\Oauth\KeyPairTransformer;

/**
 * KeyPairTransformerTest tests KeyPairTransformer
 */
class KeyPairTransformerTest extends \PHPUnit_Framework_TestCase
{

    protected $sut;

    protected function setUp()
    {
        $this->sut = new KeyPairTransformer();
    }

    public function testReverseTransform()
    {
        $this->assertNull($this->sut->reverseTransform(['client_id' => null, 'secret_id' => null]));
    }

    public function testIdentity()
    {
        $this->assertEquals(123456, $this->sut->transform(123456));
    }

}
