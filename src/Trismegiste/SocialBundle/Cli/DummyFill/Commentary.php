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
 * Commentary creates commentary attached to Publish content
 *
 * @codeCoverageIgnore
 */
class Commentary extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('social:filldummy:comment')
                ->setDescription('Add commentary content')
                ->addArgument('min', InputArgument::REQUIRED)
                ->addArgument('max', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Fill...");
        $minCard = $input->getArgument('min');
        $maxCard = $input->getArgument('max');
        $seed = sha1(microtime() . rand() . $minCard . $maxCard);

        /* @var $progressBar \Symfony\Component\Console\Helper\ProgressHelper */
        $progressBar = $this->getHelper('progress');
        /* @var $repo \Trismegiste\Yuurei\Persistence\RepositoryInterface */
        $repo = $this->getContainer()->get('dokudoki.repository');

        $netizenList = iterator_to_array($repo->find(['-class' => 'netizen']), false);
        $netizenCount = count($netizenList);
        $query = ['-class' => ['$in' => ['small', 'picture', 'video', 'repeat', 'status']]];
        $pubCount = $repo->getCursor($query)->count();

        $progressBar->start($output, $pubCount);

        $cursor = $repo->find($query);
        /* @var $publish \Trismegiste\Socialist\Publishing */
        foreach ($cursor as $publish) {
            $commentaryCount = rand($minCard, $maxCard);
            for ($k = 0; $k < $commentaryCount; $k++) {
                /* @var $picked \Trismegiste\SocialBundle\Security\Netizen */
                $picked = $netizenList[rand(0, $netizenCount - 1)];
                $comment = new \Trismegiste\Socialist\Commentary($picked->getAuthor());
                $comment->setMessage("This is a commentary ($seed) to fill the database for benchmark purpose");
                foreach ($picked->getFanIterator() as $fan => $dummy) {
                    $tmpAuth = new \Trismegiste\Socialist\Author($fan);
                    $comment->addFan($tmpAuth);
                }
                $publish->attachCommentary($comment);
            }

            $repo->persist($publish);
            $progressBar->advance();
        }

        $progressBar->finish();
    }

}
