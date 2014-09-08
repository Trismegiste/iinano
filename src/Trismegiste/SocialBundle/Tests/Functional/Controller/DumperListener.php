<?php

namespace Trismegiste\SocialBundle\Tests\Functional\Controller;

class DumperListener extends \PHPUnit_Framework_BaseTestListener
{

    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        if ($test instanceof WebTestCasePlus) {
            file_put_contents('error-' . $test->getName() . '.html', $test->getCurrentContent());
        }
    }

    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        if ($test instanceof WebTestCasePlus) {
            file_put_contents('failure-' . $test->getName() . '.html', $test->getCurrentContent());
        }
    }

}