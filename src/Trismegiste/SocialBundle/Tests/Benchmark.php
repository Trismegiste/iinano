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

    /** @var \Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface */
    protected $repository;

    protected function setUp()
    {
        parent::setUp();
        $this->repository = $this->getService('social.netizen.repository');
    }

    protected function getFixtures()
    {
        $small = $this->repository->create('small', 'aaaa');

        $largeFan = $this->repository->create('largefan', 'aaaa');
        for ($k = 0; $k < 1000; $k++) {
            $largeFan->addFan(new Author("fan $k"));
        }

        $largeFollower = $this->repository->create('largefollower', 'aaaa');
        for ($k = 0; $k < 1000; $k++) {
            $f = $this->repository->create("following $k", 'aaaa');
            $largeFollower->follow($f);
            $f->follow($largeFollower);
        }

        $large = $this->repository->create('large', 'aaaa');
        for ($k = 0; $k < 1000; $k++) {
            $large->addFan(new Author("fan $k"));
        }
        for ($k = 0; $k < 1000; $k++) {
            $f = $this->repository->create("following $k", 'aaaa');
            $large->follow($f);
            $f->follow($large);
        }

        $superlargefan = $this->repository->create('superlargefan', 'aaaa');
        for ($k = 0; $k < 10000; $k++) {
            $superlargefan->addFan(new Author("fan $k"));
        }

        $superlargefollower = $this->repository->create('superlargefollower', 'aaaa');
        for ($k = 0; $k < 10000; $k++) {
            $f = $this->repository->create("following $k", 'aaaa');
            $superlargefollower->follow($f);
            $f->follow($superlargefollower);
        }

        return [
            [$small, 10000],
            [$largeFan, 1000],
            [$largeFollower, 100],
            [$large, 100],
            [$superlargefan, 100],
            [$superlargefollower, 10]
        ];
    }

    protected function benchmark(Netizen $user, $counter)
    {
        $key = $user->getUsername();
        $stopwatch = microtime(true);
        for ($k = 0; $k < $counter; $k++) {
            $this->repository->persist($user);
        }
        printf(" write %s %.1f ms\n", $key, (microtime(true) - $stopwatch) * 1000 / $counter);

        $stopwatch = microtime(true);
        for ($k = 0; $k < $counter; $k++) {
            $user = $this->repository->findByNickname($key);
        }
        printf(" read %s %.1f ms\n", $user->getUsername(), (microtime(true) - $stopwatch) * 1000 / $counter);
    }

    public function testBenchmark()
    {
        foreach ($this->getFixtures() as $param) {
            $this->benchmark($param[0], $param[1]);
        }
    }

}
