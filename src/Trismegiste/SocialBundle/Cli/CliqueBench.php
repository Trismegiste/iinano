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

    protected $collector = [];

    public function configure()
    {
        $this->setName('social:clique:bench')
                ->setDescription('Benchmark with cliques')
                ->addArgument('maxUser', InputArgument::OPTIONAL, 'how many', 100)
                ->addArgument('messagePerUser', InputArgument::OPTIONAL, 'how many', 10)
                ->addOption('iter', null, InputOption::VALUE_REQUIRED, 'how many', 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('dokudoki.collection')->drop();

        $output->writeln("Fill...");
        $maxUser = $input->getArgument('maxUser');
        $messagePerUser = $input->getArgument('messagePerUser');
        $maxIteration = $input->getOption('iter');

        for ($iter = 1; $iter <= $maxIteration; $iter++) {
            $geometricIter = (int) pow($maxUser, $iter / (float) $maxIteration);
            $this->init();
            $this->fill($output, $geometricIter, $messagePerUser);
            $this->bench($output, $geometricIter);
        }

        $this->writeProfiling();
    }

    protected function writeProfiling()
    {
        $handle = fopen('result.csv', 'w');
        foreach ($this->collector as $fields) {
            fputcsv($handle, $fields);
        }
        fclose($handle);
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
        //$collection->ensureIndex(['owner.nickname' => 1], ['sparse' => true]);
        $collection->ensureIndex(['owner.nickname' => 1, '_id' => -1], ['sparse' => true]);
    }

    protected function fill(OutputInterface $output, $numUser, $msgPerUser)
    {
        /* @var $userRepo \Trismegiste\SocialBundle\Repository\NetizenRepository */
        $userRepo = $this->getContainer()->get('social.netizen.repository');
        /* @var $userFactory \Trismegiste\SocialBundle\Security\NetizenFactory */
        $userFactory = $this->getContainer()->get('security.netizen.factory');
        /* @var $contentRepo \Trismegiste\Yuurei\Persistence\Repository */
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
            $batch = [];
            $author = $user[$k]->getAuthor();
            // template for one author :
            $docTemplate = new \Trismegiste\Socialist\SmallTalk($author);
            for ($i = 0; $i < $numUser; $i++) {
                $docTemplate->addFan($user[$i]->getAuthor());
            }
            for ($i = 0; $i < 10; $i++) {
                $comm = new \Trismegiste\Socialist\Commentary($user[rand(0, $numUser - 1)]->getAuthor());
                $comm->setMessage('This is not a very long commentary but I think it is a good approx');
                $docTemplate->attachCommentary($comm);
            }

            for ($j = 0; $j < $msgPerUser; $j++) {
                $doc = clone $docTemplate;
                $doc->setMessage("One small talk $k-$j for iinano benchmark, one giant doc for mongo but it's not a big deal for this database");
                $batch[] = $doc;
                //$contentRepo->persist($doc);
                $progressBar->advance();
            }
            $contentRepo->batchPersist($batch);
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
        $this->collector[] = ['userCount' => $numUser, 'duration' => $delta * 1000];
    }

}
