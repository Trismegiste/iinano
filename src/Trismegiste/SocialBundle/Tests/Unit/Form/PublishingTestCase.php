<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Trismegiste\SocialBundle\Form\PublishingType;

/**
 * a test case template for form type
 *
 * @todo inheritance from FormTestCase ?
 */
abstract class PublishingTestCase extends \PHPUnit_Framework_TestCase
{

    /** @var \Symfony\Component\Form\FormInterface */
    protected $sut;

    /** @var \Symfony\Component\Form\FormFactoryInterface */
    protected $factory;

    /** @var \Trismegiste\SocialBundle\Repository\PublishingRepository */
    protected $repository;

    protected function setUp()
    {
        $this->repository = $this->getMockBuilder('Trismegiste\SocialBundle\Repository\PublishingRepository')
                ->disableOriginalConstructor()
                ->getMock();

        $this->repository->expects($this->any())
                ->method('create')
                ->will($this->returnValue($this->createData()));

        $validator = Validation::createValidator();
        $type = $this->createType();
        $this->factory = Forms::createFormFactoryBuilder()
                ->addExtension(new ValidatorExtension($validator))
                ->addType(new PublishingType($this->repository))
                ->addType($type)
                ->getFormFactory();

        $this->sut = $this->factory->create($type->getName());
    }

    abstract protected function createType();

    abstract public function getValidInputs();

    abstract public function getInvalidInputs();

    abstract protected function getModelFqcn();

    /**
     * Create a new instance of the object manage by the form
     *
     * @return mixed
     */
    protected function createData()
    {
        return $this->getMockBuilder($this->getModelFqcn())
                        ->disableOriginalConstructor()
                        ->setMethods(null)
                        ->getMock();
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
            foreach ($this->sut->all() as $child) {
                $msg .= "\n" . print_r($child->getErrors(), true);
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
