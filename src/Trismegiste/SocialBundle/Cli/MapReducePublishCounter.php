<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Cli;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\SocialBundle\Repository\MapReduce\PublishingCounter;

/**
 * MapReduceJob is a ...
 */
class MapReducePublishCounter extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('social:mr:pc')
                ->setDescription('Run a map reduce service to count and update publishing per user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $coll MongoCollection */
        $coll = $this->getContainer()->get('dokudoki.collection');
        $job = new PublishingCounter($coll, 'publish_counter');
        $job->compileReport();
        $job->updateRepeat();
    }

}
