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
        return new CommentaryType();
    }

    public function getValidInputs()
    {
        return [
            [['message' => 'lol'], ['message' => 'lol']]
        ];
    }

    public function getInvalidInputs()
    {
        return [
            [['message' => 'gg'], ['message' => 'gg'], ['message']]
        ];
    }

}
