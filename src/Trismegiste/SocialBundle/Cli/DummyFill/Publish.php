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
 * Publish creates follow relation between Netizen
 *
 * @codeCoverageIgnore
 */
class Publish extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('social:filldummy:publish')
                ->setDescription('Add publishing content')
                ->addArgument('count', InputArgument::REQUIRED, 'how many');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Fill...");
        $cardinal = $input->getArgument('count');
        $seed = sha1(microtime() . rand() . $cardinal);

        /* @var $progressBar \Symfony\Component\Console\Helper\ProgressHelper */
        $progressBar = $this->getHelper('progress');
        /* @var $repo \Trismegiste\Yuurei\Persistence\RepositoryInterface */
        $repo = $this->getContainer()->get('dokudoki.repository');

        $netizenList = iterator_to_array($repo->find(['-class' => 'netizen']), false);
        $netizenCount = count($netizenList);

        $progressBar->start($output, $cardinal);

        for ($k = 0; $k < $cardinal; $k++) {
            /* @var $picked \Trismegiste\SocialBundle\Security\Netizen */
            $picked = $netizenList[rand(0, $netizenCount - 1)];
            $pub = new \Trismegiste\Socialist\SmallTalk($picked->getAuthor());
            $pub->setMessage("This is a not so short message ($seed) to fill the database for benchmark purpose");
            foreach ($picked->getFanIterator() as $fan => $dummy) {
                $tmpAuth = new \Trismegiste\Socialist\Author($fan);
                $pub->addFan($tmpAuth);
            }

            $repo->persist($pub);
            $progressBar->advance();
        }

        $progressBar->finish();
    }

}
