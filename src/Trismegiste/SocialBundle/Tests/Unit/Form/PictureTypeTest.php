<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\PictureType;
use Trismegiste\Socialist\Picture;

/**
 * PictureTypeTest tests PictureType
 */
class PictureTypeTest extends PublishingTestCase
{

    protected $storage;

    protected function createType()
    {
        $this->storage = $this->getMockBuilder('Trismegiste\SocialBundle\Repository\PictureRepository')
                ->setMethods(['store'])
                ->setConstructorArgs([sys_get_temp_dir()])
                ->getMock();

        $this->storage->expects($this->once())
                ->method('store')
                ->will($this->returnCallback([$this, 'mockStore']));

        return new PictureType($this->storage);
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
        $upload = '/home/flo/Develop/iinano/src/Trismegiste/SocialBundle/Resources/public/img/mascot.png';
        $validated = $this->createData();
        $validated->setMessage('A small message above 10 chars');
        $validated->setStorageKey('123.jpg');
        $validated->setMimeType('image/jpeg');

        $post = [
            'message' => 'A small message above 10 chars',
            'picture' => new \Symfony\Component\HttpFoundation\File\UploadedFile($upload, 'dummy.jpg')
        ];
        return [
            [$post, $validated]
        ];
    }

    public function mockStore(Picture $pic, $dummy)
    {
        $pic->setStorageKey('123.jpg');
        $pic->setMimeType('image/jpeg');
    }

}
