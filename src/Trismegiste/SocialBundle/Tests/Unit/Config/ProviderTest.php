<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Config;

use Trismegiste\SocialBundle\Config\Provider;

/**
 * ProviderTest tests the cachec config provider
 */
class ProviderTest extends \PHPUnit_Framework_TestCase
{

    /** @var \Trismegiste\SocialBundle\Config\Provider */
    protected $sut;

    /** @var Trismegiste\Yuurei\Persistence\RepositoryInterface */
    protected $repository;
    protected $targetFile;

    protected function setUp()
    {
        $this->repository = $this->getMock('Trismegiste\Yuurei\Persistence\RepositoryInterface');
        $this->sut = new Provider($this->repository, sys_get_temp_dir(), ['default' => 123]);
        $this->targetFile = sys_get_temp_dir() . '/' . Provider::FILENAME;
        @unlink($this->targetFile);
    }

    public function testWrite()
    {
        $this->repository->expects($this->once())
                ->method('persist')
                ->with($this->isInstanceOf('Trismegiste\SocialBundle\Config\ParameterBag'));

        $this->sut->write([]);
        $this->assertFileExists($this->targetFile);
    }

    public function testRead()
    {
        $doc = ['config' => 456];
        $this->sut->write($doc);

        $this->assertEquals($doc, $this->sut->read());
    }

    public function testCachedRead()
    {
        $doc = ['config' => 456];
        $this->sut->write($doc);

        $this->assertEquals($doc, $this->sut->read());
        @unlink($this->targetFile);
        $this->assertEquals($doc, $this->sut->read());
    }

    public function testNoOptionalWarmUp()
    {
        $this->assertFalse($this->sut->isOptional());
    }

    public function testWarmupWithDefaultValues()
    {
        $this->assertFileNotExists($this->targetFile);
        $this->sut->warmUp(sys_get_temp_dir());
        $this->assertFileExists($this->targetFile);
        $this->assertEquals(['default' => 123], $this->sut->read());
    }

    public function testWarmupWithDatabase()
    {
        $this->repository->expects($this->once())
                ->method('findOne')
                ->with(['-class' => 'config'])
                ->willReturn(new \Trismegiste\SocialBundle\Config\ParameterBag(['database' => 789]));

        $this->sut->warmUp(sys_get_temp_dir());
        $this->assertEquals(['database' => 789], $this->sut->read());
    }

}
