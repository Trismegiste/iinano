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

    /** @var \Trismegiste\SocialBundle\Config\ProviderInterface */
    protected $sut;

    /** @var Trismegiste\Yuurei\Persistence\RepositoryInterface */
    protected $repository;

    protected function setUp()
    {
        $this->repository = $this->getMock('Trismegiste\Yuurei\Persistence\RepositoryInterface');
        $this->sut = new Provider($this->repository, sys_get_temp_dir());
    }

    public function testWrite()
    {
        $doc = ['config' => 123];
        $this->sut->write($doc);

        $this->assertEquals($doc, $this->sut->read());
    }

}
