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
 * AddFanRelation creates fan relation between Netizen
 *
 * @codeCoverageIgnore
 */
class AddFanRelation extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('social:filldummy:netizen:addfan')
                ->setDescription('Add fan between netizen')
                ->addArgument('percent', InputArgument::REQUIRED, 'how much percent netizen are fan of a netizen');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Fill...");
        $percent = $input->getArgument('percent');

        /* @var $progressBar \Symfony\Component\Console\Helper\ProgressHelper */
        $progressBar = $this->getHelper('progress');
        /* @var $userRepo \Trismegiste\Yuurei\Persistence\RepositoryInterface */
        $userRepo = $this->getContainer()->get('dokudoki.repository');
        $netizenCount = $userRepo->getCursor(['-class' => 'netizen'])->count();
        $cardinal = (int) ceil($netizenCount * $percent / 100);

        $progressBar->start($output, $netizenCount);

        $cursor = $userRepo->find(['-class' => 'netizen']);
        $authorList = [];
        foreach ($cursor as $netizen) {
            /* @var $netizen \Trismegiste\SocialBundle\Security\Netizen */
            $authorList[] = $netizen->getAuthor();
        }

        $cursor = $userRepo->find(['-class' => 'netizen']);
        foreach ($cursor as $netizen) {
            for ($k = 0; $k < $cardinal; $k++) {
                $netizen->addFan($authorList[rand(0, $netizenCount - 1)]);
            }
            $userRepo->persist($netizen);
            $progressBar->advance();
        }
        $progressBar->finish();
    }

}
