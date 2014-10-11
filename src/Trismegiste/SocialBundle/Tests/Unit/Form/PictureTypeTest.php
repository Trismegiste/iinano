<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\PictureType;

/**
 * PictureTypeTest tests PictureType
 */
class PictureTypeTest extends PublishingTestCase
{

    protected function createType()
    {
        return new PictureType();
    }

    protected function getModelFqcn()
    {
        return 'Trismegiste\Socialist\Picture';
    }

    public function getInvalidInputs()
    {
        $validated = $this->createData();
        $validated->setMessage(str_repeat('m', 100));

        return [
            [['message' => str_repeat('m', 100)], $validated, ['message']],
        ];
    }

    public function getValidInputs()
    {
        $validated = $this->createData();
        $validated->setMessage('A small message above 10 chars');
        $post = [
            'message' => 'A small message above 10 chars'
        ];
        return [
            [$post, $validated]
        ];
    }

}
