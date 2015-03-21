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

    protected $namespaceForMruService = 'Trismegiste\SocialBundle\Repository\MapReduce\\';

    public function configure()
    {
        $this->setName('social:mr')
                ->setDescription('Run a map reduce service')
                ->addArgument('classname', InputArgument::REQUIRED, "the classname in {$this->namespaceForMruService} namespace");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $coll MongoCollection */
        $coll = $this->getContainer()->get('dokudoki.collection');
        $fqcn = $this->namespaceForMruService . $input->getArgument('classname');
        // check
        if (!is_subclass_of($fqcn, $this->namespaceForMruService . 'MruService')) {
            throw new \InvalidArgumentException("$fqcn is not a valid class");
        }

        $refl = new \ReflectionClass($fqcn);
        /** @var $job \Trismegiste\SocialBundle\Repository\MapReduce\MruService */
        $job = $refl->newInstance($coll, $fqcn);
        $job->execute();
    }

}
