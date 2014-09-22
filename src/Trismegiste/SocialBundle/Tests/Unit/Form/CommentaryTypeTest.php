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

    public function getInputs()
    {
        return [
            [['message' => 'lol'], ['message' => 'lol']]
        ];
    }

}
