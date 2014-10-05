<?php

// just an example of a simple bash script without the need of Console Component

require_once __DIR__ . '/app/bootstrap.php.cache';
require_once __DIR__ . '/app/AppKernel.php';

use Symfony\Component\Debug\Debug;

Debug::enable();

$kernel = new AppKernel('dev', true);
$kernel->boot();
/* @var $container Symfony\Component\DependencyInjection\Container */
$container = $kernel->getContainer();

/* @var $repo \Trismegiste\SocialBundle\Repository\AbuseReport */
$repo = $container->get('social.abusereport.repository');
$result = $repo->compileReport();
print_r($result);

$cursor = $repo->findMostReported(0, 6);
foreach ($cursor as $doc) {
    print_r($doc);
}
