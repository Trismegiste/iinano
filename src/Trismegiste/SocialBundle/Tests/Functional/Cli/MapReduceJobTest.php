<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Cli;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Trismegiste\SocialBundle\Cli\MapReduceJob;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * MapReduceJobTest is a ...
 */
class MapReduceJobTest extends WebTestCase
{

    protected $kernel;

    protected function setUp()
    {
        parent::setUp();
        $this->kernel=  $this->createKernel();
        $kernel->boot();
    }
    protected function getService($id){
        return $this->kernel->getContainer()->get($id);
    }

    /**
     * @test
     */
    public function initDb()
    {
        $coll = $this->getService('dokudoki.collection');
        $coll->drop();
        
    }

    public function testExecute()
    {
        $application = new Application($kernel);
        $application->add(new MapReduceJob());
        $command = $application->find('social:mr');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['command' => $command->getName()]);
        $this->assertRegExp('#index#', $commandTester->getDisplay());
    }

}
