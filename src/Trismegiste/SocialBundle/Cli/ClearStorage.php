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
 * ClearStorage is a cleaner for storage :
 * - clear old cached images
 * - delete old pictures if storage is reaching quota
 */
class ClearStorage extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('social:storage:clear')
                ->setDescription('Run a map reduce service')
                ->addArgument('dayOld', InputArgument::OPTIONAL, 'how old, in days, files should purge', 60);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Cleaning storage</info>');
        $dayOld = $input->getArgument('dayOld');
        $storage = $this->getContainer()->get('social.picture.storage');
        // old cached :
        $cached = $storage->clearCache($dayOld);
        $output->writeln("<comment>$cached</comment> cached images were cleaned");
        // quota :
        $quota = $this->getContainer()->get('social.storage.monitoring');
        $purged = $quota->deleteExceedingQuota(110e3);
        $output->writeln("<comment>$purged</comment> images were deleted due to exceeding quota");
    }

}
