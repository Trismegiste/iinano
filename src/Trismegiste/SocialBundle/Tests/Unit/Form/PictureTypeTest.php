<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\PictureType;
use Trismegiste\Socialist\Picture;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * PictureTypeTest tests PictureType
 */
class PictureTypeTest extends PublishingTestCase
{

    protected $storage;

    protected function createType()
    {
        $this->storage = $this->getMockBuilder('Trismegiste\SocialBundle\Repository\PictureRepository')
                ->setMethods(['insertUpload'])
                ->disableOriginalConstructor()
                ->getMock();

        $this->storage->expects($this->any())
                ->method('insertUpload')
                ->will($this->returnCallback([$this, 'mockStore']));

        return new PictureType($this->storage);
    }

    protected function getModelFqcn()
    {
        return 'Trismegiste\Socialist\Picture';
    }

    protected function createMockUpload($mime)
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
                ->disableOriginalConstructor()
                ->getMock();
        $file->expects($this->any())
                ->method('getPathname')
                ->will($this->returnValue(__FILE__)); // the file needs to exist
        $file->expects($this->any())
                ->method('getMimeType')
                ->will($this->returnValue($mime));
        $file->expects($this->any())
                ->method('isValid')
                ->will($this->returnValue(true));

        return $file;
    }

    public function getInvalidInputs()
    {
        $msgTooLong = $this->createData();
        $msgTooLong->setMessage(str_repeat('m', 100));
        $msgTooLong->setMimeType(null);

        $badMimeType = $this->createData();
        $badMimeType->setMessage('hello');
        $badMimeType->setMimeType(null);
        $badMimeType->setStorageKey(null);
        $file = $this->createMockUpload('application/pdf');

        return [
            [['message' => str_repeat('m', 100)], $msgTooLong, ['message', 'picture']],
            [['message' => 'hello', 'picture' => $file], $badMimeType, ['picture']]
        ];
    }

    public function getValidInputs()
    {
        $file = $this->createMockUpload('image/png');
        $validated = $this->createData();
        $validated->setMessage('A small message above 10 chars');
        $validated->setStorageKey('123.jpg');  // filled by the mockStore below
        $validated->setMimeType('image/png');

        $post = [
            'message' => 'A small message above 10 chars',
            'picture' => $file
        ];
        return [
            [$post, $validated]
        ];
    }

    public function mockStore(Picture $pic, UploadedFile $dummy)
    {
        if ($dummy->getMimeType() != 'image/png') {
            throw new \Exception('fail');
        }
        $pic->setStorageKey('123.jpg');
        $pic->setMimeType($dummy->getMimeType());
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
