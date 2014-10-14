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
class FillForBench extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('social:fill:bench')
                ->setDescription('Fill with benchmark data')
                ->addArgument('userCount', InputArgument::OPTIONAL, 'how many', 1000)
                ->addArgument('followCount', InputArgument::OPTIONAL, 'how many', 100)
                ->addArgument('likeCount', InputArgument::OPTIONAL, 'how many', 100)
                ->addArgument('messageCount', InputArgument::OPTIONAL, 'how many', 10000)
                ->addArgument('commentaryPerMessage', InputArgument::OPTIONAL, 'how many', 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('dokudoki.collection')->drop();

        $output->writeln("Fill...");
        $userCount = $input->getArgument('userCount');
        $followCount = $input->getArgument('followCount');
        $likeCount = $input->getArgument('likeCount');
        $messageCount = $input->getArgument('messageCount');
        $commentaryPerMessage = $input->getArgument('commentaryPerMessage');

        /* @var $userRepo \Trismegiste\SocialBundle\Repository\NetizenRepository */
        $userRepo = $this->getContainer()->get('social.netizen.repository');
        /* @var $contentRepo Trismegiste\SocialBundle\Repository\PublishingRepository */
        $contentRepo = $this->getContainer()->get('dokudoki.repository');

        $progressBar = $this->getHelper('progress');

        $user = [];
        $output->writeln("Users");
        $progressBar->start($output, $userCount);
        for ($k = 0; $k <= $userCount; $k++) {
            $user[$k] = $userRepo->create("iinano-netizen-$k", 'thisisabigpassword');
            $userRepo->persist($user[$k]);
            $progressBar->advance();
        }
        $progressBar->finish();

        $output->writeln("Following and Likes");
        $progressBar->start($output, $userCount);
        for ($i = 0; $i <= $userCount; $i++) {
            for ($j = 0; $j < $followCount; $j++) {
                $user[$i]->follow($user[rand(0, $userCount)]);
            }
            for ($j = 0; $j < $likeCount; $j++) {
                $user[$i]->addFan($user[rand(0, $userCount)]->getAuthor());
            }
            $userRepo->persist($user[$i]);
            $progressBar->advance();
        }
        $progressBar->finish();

        $output->writeln("Publishing");
        $progressBar->start($output, $messageCount);
        for ($i = 0; $i < $messageCount; $i++) {
            $author = $user[rand(0, $userCount)]->getAuthor();
            $doc = new \Trismegiste\Socialist\SmallTalk($author);
            $doc->setMessage("One small talk $i for iinano benchmark, one giant doc for mongo but it's not a big deal for this database");
            for ($j = 0; $j < $likeCount / 10; $j++) {
                $doc->addFan($user[rand(0, $userCount)]->getAuthor());
            }
            for ($j = 0; $j < $commentaryPerMessage; $j++) {
                $comm = new \Trismegiste\Socialist\Commentary($user[rand(0, $userCount)]->getAuthor());
                $comm->setMessage('This is not a very long commentary but I think it is a good approx');
                $doc->attachCommentary($comm);
                for ($k = 0; $k < $likeCount / 10; $k++) {
                    $comm->addFan($user[rand(0, $userCount)]->getAuthor());
                }
            }
            $contentRepo->persist($doc);
            $progressBar->advance();
        }
        $progressBar->finish();
    }

}
