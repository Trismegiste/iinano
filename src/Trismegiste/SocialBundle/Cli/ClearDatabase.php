<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Cli;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ClearDatabase is a cleaner for old publishing in the database
 * Purpose : pervent reaching storage quota
 */
class ClearDatabase extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('social:database:clear')
                ->addArgument('quota', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('<info>Cleaning database</info>');
    }

}
