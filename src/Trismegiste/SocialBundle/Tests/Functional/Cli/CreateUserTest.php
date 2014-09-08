<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Functional\Cli;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Trismegiste\SocialBundle\Cli\CreateUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * CreateUserTest tests CreateUser
 */
class CreateUserTest extends WebTestCase
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
        $application->add(new CreateUser());
        $command = $application->find('social:user:create');
        $commandTester = new CommandTester($command);
        $dialog = $command->getHelper('dialog');
        $dialog->setInputStream($this->getInputStream("Test\n0\n"));

        $commandTester->execute([
            'command' => $command->getName(),
            'nickname' => 'scotty',
            'password' => 'warp'
        ]);
        $this->assertRegExp('#scotty#', $commandTester->getDisplay());
    }

}