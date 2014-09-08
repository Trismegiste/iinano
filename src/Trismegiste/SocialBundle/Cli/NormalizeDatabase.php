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
                ->setDescription('ACME')
                ->addArgument('nickname', InputArgument::REQUIRED)
                ->addArgument('count', InputArgument::OPTIONAL, 'how many', 120);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // @todo ensureIndex   
    }

}