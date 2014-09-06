<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Symfony\Component\Form\Forms;
use Trismegiste\SocialBundle\Form\GenderType;

/**
 * GenderTypeTest tests GenderType
 */
class GenderTypeTest extends \PHPUnit_Framework_TestCase
{

    protected $sut;
    protected $factory;

    protected function setUp()
    {
        $this->factory = Forms::createFormFactoryBuilder()
                ->addType(new GenderType())
                ->getFormFactory();

        $this->sut = $this->factory->create('gender');
    }

    public function getChoices()
    {
        return [
            ['xx', 'xx'],
            ['xy', 'xy'],
            ['ab', null],
            [['xx', 'xy'], 'xy']
        ];
    }

    /**
     * @dataProvider getChoices
     */
    public function testSubmit($submitted, $expected)
    {
        $this->sut->submit($submitted);
        $this->assertEquals($expected, $this->sut->getData());
    }

    public function testView()
    {
        $view = $this->sut->createView();
        $this->assertNull($view->vars['empty_value']);
    }

}