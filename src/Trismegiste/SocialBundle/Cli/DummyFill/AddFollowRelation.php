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
 * AddFollowRelation creates follow relation between Netizen
 *
 * @codeCoverageIgnore
 */
class AddFollowRelation extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('social:filldummy:netizen:addfollow')
                ->setDescription('Add follow between netizen')
                ->addArgument('percent', InputArgument::REQUIRED, 'how much percent netizen are follower of a netizen');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Fill...");
        $percent = $input->getArgument('percent');

        /* @var $progressBar \Symfony\Component\Console\Helper\ProgressHelper */
        $progressBar = $this->getHelper('progress');
        /* @var $userRepo \Trismegiste\Yuurei\Persistence\RepositoryInterface */
        $userRepo = $this->getContainer()->get('dokudoki.repository');

        $cursor = $userRepo->find(['-class' => 'netizen']);
        $netizenList = iterator_to_array($cursor, false);
        $netizenCount = count($netizenList);
        $cardinal = (int) ceil($netizenCount * $percent / 100);
        $progressBar->start($output, $netizenCount);

        foreach ($netizenList as $netizen) {
            /* @var $netizen \Trismegiste\SocialBundle\Security\Netizen */
            for ($k = 0; $k < $cardinal; $k++) {
                $netizen->follow($netizenList[rand(0, $netizenCount - 1)]);
            }
            $progressBar->advance();
        }

        foreach ($netizenList as $netizen) {
            $userRepo->persist($netizen);
        }
        $progressBar->finish();
    }

}
