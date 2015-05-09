<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Trismegiste\SocialBundle\Form\AdminSelectionChoice;

/**
 * AdminSelectionChoiceTest is a ...
 */
class AdminSelectionChoiceTest extends \PHPUnit_Framework_TestCase
{

    /** @var Trismegiste\SocialBundle\Form\AdminSelectionChoice */
    protected $sut;

    protected function setUp()
    {
        $choices = new \ArrayIterator([555 => new \stdClass()]);
        $this->sut = new AdminSelectionChoice($choices);
    }

    public function testGetChoices()
    {
        $this->assertCount(1, $this->sut->getChoices());
        $this->assertInstanceOf('stdClass', $this->sut->getChoices()[0]);
    }

}
