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
    protected $item;

    protected function setUp()
    {
        $this->item = new \stdClass();
        $choices = new \ArrayIterator([555 => $this->item]);
        $this->sut = new AdminSelectionChoice($choices);
    }

    public function testGetChoices()
    {
        $this->assertCount(1, $this->sut->getChoices());
        $this->assertInstanceOf('stdClass', $this->sut->getChoices()[0]);
    }

    public function testGetChoicesForValues()
    {
        $this->assertEquals([$this->item], $this->sut->getChoicesForValues([555]));
    }

    public function testGetIndicesForChoices()
    {
        $this->sut->getIndicesForChoices([]);
    }

    public function testGetIndicesForValues()
    {
        $this->assertEquals([0], $this->sut->getIndicesForValues([555]));
    }

}
