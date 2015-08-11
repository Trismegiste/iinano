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
        $this->setName('social:database:clear');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Cleaning database</info>');

        $cursor = $this->getContainer()
                ->get('database.status')
                ->findExceedingQuotaDocument();

        $pk2Delete = [];
        foreach ($cursor as $doc) {
            $pk2Delete[] = (string) $doc['_id'];
        }
        $cleaned = count($pk2Delete);
        // delete
        $progressBar = $this->getHelper('progress');
        $progressBar->start($output, $cleaned);

        foreach ($pk2Delete as $pk) {
            $this->getContainer()
                    ->get('social.publishing.repository')
                    ->deleteAdmin($pk);
            $progressBar->advance();
        }
        $progressBar->finish();

        $output->writeln("<comment>$cleaned</comment> records were deleted");
    }

}
