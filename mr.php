<?php

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

$cursor = $repo->findMostReported(0, 3);
foreach ($cursor as $doc) {
    print_r($doc);
}

/*
$cnx = new MongoClient();
$db = $cnx->selectDB('iinano_flo');



$cursor = $db->selectCollection('abusivereport')->find()->sort(['value' => -1]);


$coll = $db->selectCollection('abusivereport');
print_r($coll->db);
*/