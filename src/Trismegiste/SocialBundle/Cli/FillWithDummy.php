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
                ->setDescription('Fill with dummy data')
                ->addArgument('nickname', InputArgument::REQUIRED)
                ->addArgument('count', InputArgument::OPTIONAL, 'how many', 20);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Fill...");
        $cardinal = $input->getArgument('count');
        $nickname = $input->getArgument('nickname');

        $userRepo = $this->getContainer()->get('social.netizen.repository');
        $contentRepo = $this->getContainer()->get('social.content.repository');

        $user = $userRepo->findByNickname($nickname);
        if (is_null($user)) {
            throw new \InvalidArgumentException("$nickname does not exists");
        }

        for ($k = 0; $k < $cardinal; $k++) {
            $doc = new \Trismegiste\Socialist\SmallTalk($user->getAuthor());
            $doc->setMessage("One small talk $k for iinano, one giant doc for mongo");
            $contentRepo->persist($doc);
            sleep(1);
        }
    }

}