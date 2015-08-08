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
        $quota = 3000;
        $maxSize = $quota * 0.9;

        $output->writeln('<info>Cleaning database</info>');
        $cleaned = 0;

        $health = $this->getContainer()->get('database.status')->getCollectionStats();
        if ($health['size'] > $maxSize) {
            $objectEstimate = ($health['size'] - $maxSize) / $health['avgObjSize'];
            $cursor = $this->getCollection()->find([
                        '-class' => [
                            '$in' => [
                                'small',
                                'status',
                                'picture',
                                'video',
                                'repeat',
                                'private'
                            ]
                        ]
                    ])
                    ->sort(['_id' => 1])
                    ->limit((int) $objectEstimate);

            $pk2Delete = [];
            foreach ($cursor as $doc) {
                $pk2Delete[] = (string) $doc['_id'];
            }
            // delete
            foreach ($pk2Delete as $pk) {
                $this->getContainer()->get('social.publishing.repository')->delete($pk, true);
            }
            $cleaned = count($pk2Delete);
        }

        $output->writeln("<comment>$cleaned</comment> records were deleted");
    }

    /**
     * @return \MongoCollection
     */
    private function getCollection()
    {
        return $this->getContainer()->get('dokudoki.collection');
    }

}