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
use Trismegiste\SocialBundle\Repository\MapReduce\RepeatCounter;

/**
 * MapReduceJob is a ...
 */
class MapReduceJob extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('social:mr')
                ->setDescription('Run a map reduce service');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $coll MongoCollection */
        $coll = $this->getContainer()->get('dokudoki.collection');
        $job = new RepeatCounter($coll, 'repeat_counter');
        $job->compileReport();
        $job->updateRepeat();
    }

}
