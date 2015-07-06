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

    public function testReverseTransformToNull()
    {
        $this->assertNull($this->sut->reverseTransform(['client_id' => null, 'secret_id' => null]));
    }

    public function testReverseTransform()
    {
        $this->assertNotNull($this->sut->reverseTransform(['client_id' => 213, 'secret_id' => null]));
    }

    public function testIdentity()
    {
        $this->assertEquals(123456, $this->sut->transform(123456));
    }

}
