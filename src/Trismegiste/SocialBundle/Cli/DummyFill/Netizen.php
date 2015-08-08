<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Cli\DummyFill;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Netizen is CLI tool for filling the db with dummy Netizen
 *
 * @codeCoverageIgnore
 */
class Netizen extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('social:filldummy:netizen')
                ->setDescription('Fill with dummy netizen')
                ->addArgument('count', InputArgument::REQUIRED, 'how many');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Fill...");
        $cardinal = $input->getArgument('count');

        /* @var $userFactory \Trismegiste\SocialBundle\Security\NetizenFactory */
        $userFactory = $this->getContainer()->get('security.netizen.factory');
        /* @var $progressBar \Symfony\Component\Console\Helper\ProgressHelper */
        $progressBar = $this->getHelper('progress');
        /* @var $userRepo \Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface */
        $userRepo = $this->getContainer()->get('social.netizen.repository');
        $seed = rand(0, 999);

        $progressBar->start($output, $cardinal);
        for ($k = 0; $k < $cardinal; $k++) {
            $user = $userFactory->create("netizen-$seed-$k", 'dummy', "id-$seed-$k");
            $userRepo->persist($user);
            $progressBar->advance();
        }
        $progressBar->finish();
    }

}
