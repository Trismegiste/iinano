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
        $validated->setMessage('gg');
        $validated->setMimeType('adobe/pdf');
        $post = [
            'message' => 'gg', 'mimeType' => 'adobe/pdf'
        ];
        return [
            [$post, $validated, ['message', 'mimeType', 'storageKey']]
        ];
    }

    public function getValidInputs()
    {
        $validated = $this->createData();
        $validated->setMessage('A small message above 10 chars');
        $validated->setStorageKey('photo.jpg');
        $validated->setMimeType('image/jpeg');
        $post = [
            'message' => 'A small message above 10 chars',
            'mimeType' => 'image/jpeg',
            'storageKey' => 'photo.jpg'
        ];
        return [
            [$post, $validated]
        ];
    }

}
