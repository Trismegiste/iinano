<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

/**
 * a test case template for form type
 */
abstract class FormTestCase extends \PHPUnit_Framework_TestCase
{

    /** @var \Symfony\Component\Form\FormInterface */
    protected $sut;

    /** @var \Symfony\Component\Form\FormFactoryInterface */
    protected $factory;

    protected function setUp()
    {
        $validator = Validation::createValidator();
        $type = $this->createType();
        if (!is_array($type)) {
            $type = [$type];
        }
        $this->factory = Forms::createFormFactoryBuilder()
                ->addExtension(new ValidatorExtension($validator))
                ->addTypes($type)
                ->getFormFactory();

        $data = $this->createData();
        $this->sut = $this->factory->create($type[0]->getName(), $data);
    }

    abstract protected function createType();

    abstract public function getValidInputs();

    abstract public function getInvalidInputs();

    /**
     * Override if your form needs a valid object
     *
     * @return mixed
     */
    protected function createData()
    {
        return null;
    }

    /**
     * @dataProvider getValidInputs
     */
    public function testSubmit($submitted, $expected)
    {
        $this->sut->submit($submitted);
        $msg = '';
        if (!$this->sut->isValid()) {
            $msg = print_r($this->sut->getErrors(), true);
            foreach ($this->sut->all() as $name => $child) {
                if (count($child->getErrors())) {
                    $msg .= "\n$name: " . print_r($child->getErrors(), true);
                }
            }
        }
        $this->assertTrue($this->sut->isValid(), $msg);
        $this->assertEquals($expected, $this->sut->getData());
    }

    /**
     * @dataProvider getInvalidInputs
     */
    public function testErrorSubmit($submitted, $expected, array $invalidFields = [])
    {
        $this->sut->submit($submitted);
        $this->assertFalse($this->sut->isValid());
        foreach ($invalidFields as $child) {
            $this->assertFalse($this->sut->get($child)->isValid(), "$child isValid");
        }
        $this->assertEquals($expected, $this->sut->getData());
    }

}
