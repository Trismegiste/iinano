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
 * NormalizeDatabase is CLI tool for installing and 
 * re-synchronizing the denormalized database
 */
class NormalizeDatabase extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('social:database:normalize')
                ->setDescription('Ensuring and analyzing the iinano repository');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Creating indexes');
        /* @var $collection \MongoCollection */
        $collection = $this->getContainer()->get('dokudoki.collection');
        // indexing object class alias :
        $collection->ensureIndex(['-class' => 1]);
        // indexing user's nickname and ensuring uniqueness :
        $collection->ensureIndex(['author.nickname' => 1], ['sparse' => true, 'unique' => true]);
        // indexing author of a publishing
        $collection->ensureIndex(['owner.nickname' => 1], ['sparse' => true]);
    }

}