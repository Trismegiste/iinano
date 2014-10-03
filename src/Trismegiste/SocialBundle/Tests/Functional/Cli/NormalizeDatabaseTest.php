<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Cli;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Trismegiste\SocialBundle\Cli\NormalizeDatabase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * NormalizeDatabaseTest tests NormalizeDatabase
 */
class NormalizeDatabaseTest extends WebTestCase
{

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }

    public function testExecute()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new NormalizeDatabase());
        $command = $application->find('social:database:normalize');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['command' => $command->getName()]);
        $this->assertRegExp('#index#', $commandTester->getDisplay());
    }

}
