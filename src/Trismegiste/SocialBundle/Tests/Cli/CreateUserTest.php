<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Cli;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Trismegiste\SocialBundle\Cli\CreateUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * CreateUserTest tests CreateUser
 */
class CreateUserTest extends WebTestCase
{

    public function testExecute()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new CreateUser());
        $command = $application->find('social:user:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'nickname' => 'scotty'
        ]);
        $this->assertRegExp('#scotty#', $commandTester->getDisplay());
    }

}