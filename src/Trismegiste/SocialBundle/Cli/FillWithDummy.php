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
 */
class FillWithDummy extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('social:fill:dummy')
                ->setDescription('Fill with dummy data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Fill...");

        $userRepo = $this->getContainer()->get('social.netizen.repository');
        $contentRepo = $this->getContainer()->get('social.content.repository');

        $user = $userRepo->findByNickname('konpaku');

        for ($k = 0; $k < 120; $k++) {
            $doc = new \Trismegiste\Socialist\SimplePost($user->getAuthor());
            $doc->setTitle("Un titre $k");
            $doc->setBody("Un content $k");
            $contentRepo->persist($doc);
            sleep(1);
        }
    }

}