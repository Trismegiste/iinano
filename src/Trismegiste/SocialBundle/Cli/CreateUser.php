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
                ->setDescription('Create a user and privileges')
                ->addArgument('nickname', InputArgument::REQUIRED)
                ->addArgument('password', InputArgument::REQUIRED)
                ->addOption('role', null, InputOption::VALUE_REQUIRED, 'the role of the new user', 'ROLE_USER');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $nickname = $input->getArgument('nickname');
        $password = $input->getArgument('password');
        $dialog = $this->getHelperSet()->get('dialog');
        $output->writeln("Creating user $nickname...");

        $repository = $this->getContainer()->get('social.netizen.repository');
        $user = $repository->create($nickname, $password);
        $profile = $user->getProfile();

        // additional info
        $profile->fullName = $dialog->ask($output, 'Full name ', ucfirst($nickname));
        $gender = ['xy', 'xx'];
        $choice = $dialog->select($output, 'Gender', $gender, 0);
        $profile->gender = $gender[$choice];

        // roles
        $user->setGroup($input->getOption('role'));

        $repository->persist($user);
        $output->writeln("$nickname created");
    }

}
