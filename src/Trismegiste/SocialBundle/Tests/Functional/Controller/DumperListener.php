<?php

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Response;

class DumperListener extends \PHPUnit_Framework_BaseTestListener
{

    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        if (($test instanceof WebTestCasePlus) && !is_null($test->getCurrentResponse())) {
            $this->dumpResponse('error', $test->getName(), $test->getCurrentResponse());
        }
    }

    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        if (($test instanceof WebTestCasePlus) && !is_null($test->getCurrentResponse())) {
            $this->dumpResponse('failure', $test->getName(), $test->getCurrentResponse());
        }
    }

    protected function dumpResponse($prefix, $test, Response $response)
    {
        $filepath = sys_get_temp_dir() . sprintf("/%s-%s-%d.html", $prefix, $test, $response->getStatusCode());
        file_put_contents($filepath, $response->getContent());
    }

}