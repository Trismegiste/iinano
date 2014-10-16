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

        $this->storage->expects($this->any())
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
        $validated->setMimeType(null);

        return [
            [['message' => str_repeat('m', 100)], $validated, ['message', 'picture']],
        ];
    }

    public function getValidInputs()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
                ->disableOriginalConstructor()
                ->getMock();
        $file->expects($this->any())
                ->method('getPathname')
                ->will($this->returnValue(__FILE__)); // the file needs to exist
        $file->expects($this->any())
                ->method('getMimeType')
                ->will($this->returnValue('image/png'));
        $file->expects($this->any())
                ->method('isValid')
                ->will($this->returnValue(true));

        $validated = $this->createData();
        $validated->setMessage('A small message above 10 chars');
        $validated->setStorageKey('123.jpg');  // filled by the mockStore below
        $validated->setMimeType('image/jpeg');

        $post = [
            'message' => 'A small message above 10 chars',
            'picture' => $file
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

    public function testEditBehavior()
    {
        $pub = $this->createData();
        $pub->setId(new \MongoId());
        $pub->setMessage('original');
        $this->sut->setData($pub); // we edit a picture

        $this->sut->submit(['message' => 'edited']);
        $this->assertTrue($this->sut->isValid());
        $this->assertEquals('edited', $pub->getMessage());
    }

}
