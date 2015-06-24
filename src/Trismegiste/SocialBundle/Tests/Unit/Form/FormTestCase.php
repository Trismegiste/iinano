<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Symfony\Bundle\FrameworkBundle\Validator\ConstraintValidatorFactory;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Validation;

/**
 * a test case template for form type
 */
abstract class FormTestCase extends \PHPUnit_Framework_TestCase
{

    /** @var FormInterface */
    protected $sut;

    /** @var FormFactoryInterface */
    protected $factory;

    protected function setUp()
    {
        $dummyContainer = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $validators = $this->createValidator();
        $validatorFactory = new ConstraintValidatorFactory($dummyContainer, $validators);
        $validator = Validation::createValidatorBuilder()
                ->setConstraintValidatorFactory($validatorFactory)
                ->getValidator();

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

    protected function createValidator()
    {
        return [];
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
