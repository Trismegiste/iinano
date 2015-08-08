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
 * FillWithDummy is CLI tool for dummy data for viewing
 *
 * @codeCoverageIgnore
 */
class FillWithDummy extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('social:fill:dummy')
                ->setDescription('Fill with dummy data')
                ->addArgument('what', InputArgument::REQUIRED)
                ->addArgument('count', InputArgument::REQUIRED, 'how many');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Fill...");
        $cardinal = $input->getArgument('count');
        $whatType = $input->getArgument('what');

        call_user_func([$this, 'fill' . $whatType], $cardinal, $output);
    }

    protected function fillNetizen($count, OutputInterface $output)
    {
        /* @var $userFactory \Trismegiste\SocialBundle\Security\NetizenFactory */
        $userFactory = $this->getContainer()->get('security.netizen.factory');
        /* @var $progressBar \Symfony\Component\Console\Helper\ProgressHelper */
        $progressBar = $this->getHelper('progress');
        /* @var $userRepo \Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface */
        $userRepo = $this->getContainer()->get('social.netizen.repository');

        $progressBar->start($output, $count);
        for ($k = 0; $k < $count; $k++) {
            $user = $userFactory->create("iinano-netizen-$k", 'dummy', 'id' . $k);
            $userRepo->persist($user);
            $progressBar->advance();
        }
        $progressBar->finish();
    }

}
