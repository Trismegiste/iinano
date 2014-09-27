<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests;

use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\SocialBundle\Tests\Functional\Controller\WebTestCasePlus;
use Trismegiste\Socialist\Author;

/**
 * Benchmark tests Benchmark
 */
class Benchmark extends WebTestCasePlus
{

    protected $stopwatch;

    /** @var \Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface */
    protected $repository;

    protected function setUp()
    {
        parent::setUp();
        $this->repository = $this->getService('social.netizen.repository');
        $this->stopwatch = time();
    }

    protected function tearDown()
    {
        printf(" %ds\n", time() - $this->stopwatch);
        parent::tearDown();
    }

    public function testWriteSmall()
    {
        $user = $this->repository->create('small', 'aaaa');

        $this->stopwatch = time();
        for ($k = 0; $k < 10000; $k++) {
            $this->repository->persist($user);
        }
    }

    public function testReadSmall()
    {
        for ($k = 0; $k < 10000; $k++) {
            $user = $this->repository->findByNickname('small');
        }
    }

    public function testWriteLarge()
    {
        $user = $this->repository->create('large', 'aaaa');
        for ($k = 0; $k < 1000; $k++) {
            $user->addFan(new Author("fan $k"));
        }
        for ($k = 0; $k < 1000; $k++) {
            $f = $this->repository->create("following $k", 'aaaa');
            $user->follow($f);
            $f->follow($user);
        }

        $this->stopwatch = time();
        for ($k = 0; $k < 100; $k++) {
            $this->repository->persist($user);
        }
    }

    public function testReadLarge()
    {
        for ($k = 0; $k < 100; $k++) {
            $user = $this->repository->findByNickname('large');
        }
    }

    public function testWriteSuperLarge()
    {
        $user = $this->repository->create('superlarge', 'aaaa');
        for ($k = 0; $k < 10000; $k++) {
            $user->addFan(new Author("fan $k"));
        }
        for ($k = 0; $k < 10000; $k++) {
            $f = $this->repository->create("following $k", 'aaaa');
            $user->follow($f);
            $f->follow($user);
        }

        $this->stopwatch = time();
        for ($k = 0; $k < 10; $k++) {
            $this->repository->persist($user);
        }
    }

    public function testReadSuperLarge()
    {
        for ($k = 0; $k < 10; $k++) {
            $user = $this->repository->findByNickname('superlarge');
        }
    }

}
