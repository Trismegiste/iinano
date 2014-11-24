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

class CliqueBench extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('social:clique:bench')
                ->setDescription('Benchmark with cliques')
                ->addArgument('maxUser', InputArgument::OPTIONAL, 'how many', 100)
                ->addArgument('messagePerUser', InputArgument::OPTIONAL, 'how many', 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('dokudoki.collection')->drop();

        $output->writeln("Fill...");
        $maxUser = $input->getArgument('maxUser');
        $messagePerUser = $input->getArgument('messagePerUser');

        for ($iter = 1; $iter <= 10; $iter++) {
            $geometricIter = (int) pow($maxUser, $iter / 10.0);
            $this->init();
            $this->fill($output, $geometricIter, $messagePerUser);
            $this->bench($output, $geometricIter);
        }
    }

    protected function init()
    {
        /* @var $collection \MongoCollection */
        $collection = $this->getContainer()->get('dokudoki.collection');
        $collection->drop();
        // indexing object class alias :
        $collection->ensureIndex(['-class' => 1]);
        // indexing user's nickname and ensuring uniqueness :
        $collection->ensureIndex(['author.nickname' => 1], ['sparse' => true, 'unique' => true]);
        // indexing author of a publishing
        $collection->ensureIndex(['owner.nickname' => 1], ['sparse' => true]);
    }

    protected function fill(OutputInterface $output, $numUser, $msgPerUser)
    {
        /* @var $userRepo \Trismegiste\SocialBundle\Repository\NetizenRepository */
        $userRepo = $this->getContainer()->get('social.netizen.repository');
        /* @var $userFactory \Trismegiste\SocialBundle\Security\NetizenFactory */
        $userFactory = $this->getContainer()->get('security.netizen.factory');
        /* @var $contentRepo Trismegiste\SocialBundle\Repository\PublishingRepository */
        $contentRepo = $this->getContainer()->get('dokudoki.repository');
        /* @var $progressBar \Symfony\Component\Console\Helper\ProgressHelper */
        $progressBar = $this->getHelper('progress');

        $output->writeln("With $numUser Users");
        $progressBar->start($output, $numUser * 3);
        for ($k = 0; $k < $numUser; $k++) {
            $user[$k] = $userFactory->create("iinano-netizen-$k", 'aaaa');
            $progressBar->advance();
        }
        // follow & like user
        for ($k = 0; $k < $numUser; $k++) {
            for ($j = 0; $j < $numUser; $j++) {
                $user[$k]->follow($user[$j]);
                $user[$k]->addFan($user[$j]->getAuthor());
            }
            $progressBar->advance();
        }
        for ($k = 0; $k < $numUser; $k++) {
            $userRepo->persist($user[$k]);
            $progressBar->advance();
        }
        $progressBar->finish();

        // message
        $output->writeln("Writing message");
        $progressBar->start($output, $numUser * $msgPerUser);
        for ($k = 0; $k < $numUser; $k++) {
            $author = $user[$k]->getAuthor();
            for ($j = 0; $j < $msgPerUser; $j++) {
                $doc = new \Trismegiste\Socialist\SmallTalk($author);
                $doc->setMessage("One small talk $k-$j for iinano benchmark, one giant doc for mongo but it's not a big deal for this database");
                for ($i = 0; $i < $numUser; $i++) {
                    $doc->addFan($user[$i]->getAuthor());
                }
                $contentRepo->persist($doc);
                $progressBar->advance();
            }
        }
        $progressBar->finish();
    }

    protected function bench(OutputInterface $output, $numUser)
    {
        /* @var $pubRepo Trismegiste\SocialBundle\Repository\PublishingRepositoryInterface */
        $pubRepo = $this->getContainer()->get('social.publishing.repository');
        /* @var $userRepo \Trismegiste\SocialBundle\Repository\NetizenRepository */
        $userRepo = $this->getContainer()->get('social.netizen.repository');

        $delta = 0;
        for ($k = 0; $k < 10; $k++) {
            $user = $userRepo->findByNickname('iinano-netizen-' . rand(0, $numUser - 1));
            $stopwatch = microtime(true);
            $wall = $pubRepo->findWallEntries($user, 'following');
            foreach ($wall as $pub) {
                // nothing
            }
            $delta += microtime(true) - $stopwatch;
        }
        $output->writeln(sprintf("Clique #$numUser: %.0f ms", $delta * 1000));
    }

}
