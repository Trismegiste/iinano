<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Cli;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CreateUser is CLI tool for managing user
 */
class CreateUser extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('social:user:create')
                ->setDescription('Create a user with privileges')
                ->addArgument('nickname', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $nickname = $input->getArgument('nickname');
        $output->writeln("Create $nickname");

        $user = $this->getContainer()->get('social.netizen.repository')
                ->create($nickname);

        $this->getContainer()->get('dokudoki.repository')
                ->persist($user);
    }

}