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
        $doc = new \Trismegiste\SocialBundle\Config\Parameter();
        $this->sut->write($doc);

        print_r($this->sut->read());
    }

}
