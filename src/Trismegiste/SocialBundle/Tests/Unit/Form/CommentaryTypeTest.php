<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\CommentaryType;

/**
 * CommentaryTypeTest tests CommentaryType
 */
class CommentaryTypeTest extends FormTestCase
{

    protected function createType()
    {
        $repository = $this->getMockBuilder('Trismegiste\SocialBundle\Repository\CommentaryRepository')
                ->disableOriginalConstructor()
                ->getMock();

        $repository->expects($this->any())
                ->method('create')
                ->will($this->returnValue($this->createCommentary()));

        return new CommentaryType($repository);
    }

    protected function createCommentary()
    {
        return $this->getMockBuilder('Trismegiste\Socialist\Commentary')
                        ->disableOriginalConstructor()
                        ->setMethods(null)
                        ->getMock();
    }

    public function getValidInputs()
    {
        $result = $this->createCommentary();
        $result->setMessage('lol');
        return [
            [['message' => 'lol'], $result]
        ];
    }

    public function getInvalidInputs()
    {
        $result = $this->createCommentary();
        $result->setMessage('gg');
        return [
            [['message' => 'gg'], $result]
        ];
    }

}
