<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Symfony\Component\Form\Forms;

/**
 * a test case template for form type
 */
abstract class FormTestCase extends \PHPUnit_Framework_TestCase
{

    protected $sut;
    protected $factory;

    protected function setUp()
    {
        $type = $this->createType();
        $this->factory = Forms::createFormFactoryBuilder()
                ->addType($type)
                ->getFormFactory();

        $this->sut = $this->factory->create($type->getName());
    }

    abstract protected function createType();

    abstract public function getInputs();

    /**
     * @dataProvider getInputs
     */
    public function testSubmit($submitted, $expected)
    {
//        $this->sut->submit($submitted);
//        $this->assertEquals($expected, $this->sut->getData());
    }

}
