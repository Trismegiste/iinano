<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Repository;

use Trismegiste\SocialBundle\Repository\StorageQuota;

/**
 * StorageQuotaTest tests StorageQuota
 */
class StorageQuotaTest extends \PHPUnit_Framework_TestCase
{

    protected $collection;
    protected $storage;

    /** @var StorageQuota */
    protected $sut;

    protected function setUp()
    {
        $this->collection = $this->getMockBuilder('MongoCollection')
                ->disableOriginalConstructor()
                ->getMock();
        $this->storage = $this->getMockBuilder('Trismegiste\SocialBundle\Repository\PictureRepository')
                ->disableOriginalConstructor()
                ->getMock();
        $this->sut = new StorageQuota($this->collection, 'picture', $this->storage);
    }

    public function testTotal()
    {
        $this->collection->expects($this->once())
                ->method('aggregate')
                ->willReturn(['ok' => 1, 'result' => []]);

        $this->sut->getPictureTotalSize();
    }

    public function test_deleteExceedingQuota()
    {
        $this->sut->deleteExceedingQuota(50);
    }

}
