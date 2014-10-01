<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\AuthorTransformer;

/**
 * AuthorTransformerTest tests AuthorTransformer
 */
class AuthorTransformerTest extends \PHPUnit_Framework_TestCase
{

    /** @var Trismegiste\SocialBundle\Form\AuthorTransformer */
    protected $sut;

    /** @var Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface */
    protected $repository;

    protected function setUp()
    {
        $this->repository = $this->getMock('Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface');
        $this->sut = new AuthorTransformer($this->repository, new \ArrayIterator(['spock' => true, 'kirk' => true]));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage invalid
     */
    public function testScalarToObjectNotAChoice()
    {
        $this->sut->reverseTransform('scotty');
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage scalar
     */
    public function testScalarToObjectBadType()
    {
        $this->sut->reverseTransform(new \stdClass());
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage not found
     */
    public function testScalarToObjectNotFound()
    {
        $this->sut->reverseTransform('spock');
    }

    public function testScalarToObjectValid()
    {

        $userMock = $this->getMockBuilder('Trismegiste\SocialBundle\Security\Netizen')
                ->disableOriginalConstructor()
                ->getMock();
        $authorMock = $this->getMock('Trismegiste\Socialist\AuthorInterface');
        $userMock->expects($this->once())
                ->method('getAuthor')
                ->will($this->returnValue($authorMock));

        $this->repository->expects($this->once())
                ->method('findByNickname')
                ->with($this->equalTo('spock'))
                ->will($this->returnValue($userMock));

        $this->assertEquals($authorMock, $this->sut->reverseTransform('spock'));
    }

    public function testObjectToScalarNull()
    {
        $this->assertNull($this->sut->transform(null));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage not an object
     */
    public function testObjectToScalarInvalidType()
    {
        $this->sut->transform(123);
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage AuthorInterface
     */
    public function testObjectToScalarInvalidClass()
    {
        $this->sut->transform(new \stdClass());
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage chosen
     */
    public function testObjectToScalarNotAChoice()
    {
        $this->sut->transform(new \Trismegiste\Socialist\Author('scotty'));
    }

    public function testObjectToScalarValid()
    {
        $this->assertEquals('spock', $this->sut->transform(new \Trismegiste\Socialist\Author('spock')));
    }

}
