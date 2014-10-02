<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * PrivateMessageTypeTest tests PrivateMessageType
 */
class PrivateMessageTypeTest extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{

    /** @var \Symfony\Component\Form\FormInterface */
    protected $sut;

    /** @var \Trismegiste\SocialBundle\Security\Netizen */
    protected $netizen;

    /** @var \MongoCollection */
    protected $collection;

    /** @var \Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface */
    protected $repository;

    /** @var \Symfony\Component\Form\FormFactoryInterface */
    protected $factory;

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();
        $this->factory = $container->get('form.factory');
        $this->collection = $container->get('dokudoki.collection');
        $this->repository = $container->get('social.netizen.repository');

        $this->netizen = $this->repository->create('kirk', 'aaaa');
        $token = new UsernamePasswordToken($this->netizen, null, 'secured_area', array('ROLE_USER'));
        $container->get('security.context')->setToken($token);
    }

    /**
     * @test
     */
    public function initialize()
    {
        $this->collection->drop();
    }

    public function testValidSubmit()
    {
        $follower = $this->repository->create('spock', 'aaaa');
        $follower->follow($this->netizen);
        $this->repository->persist($follower);
        $this->sut = $this->factory->create('social_private_message', null, ['csrf_protection' => false]);

        $this->sut->submit([
            'target' => 'spock',
            'message' => 'lol'
        ]);

        $this->assertTrue($this->sut->isValid());
        $message = $this->sut->getData();
        $this->assertInstanceOf('Trismegiste\Socialist\PrivateMessage', $message);
        $this->assertEquals('spock', $message->getTarget()->getNickname());
        $this->assertEquals('kirk', $message->getSender()->getNickname());
        $this->assertEquals('lol', $message->getMessage());
    }

    public function testInvalidSubmit()
    {
        $follower = $this->repository->findByNickname('spock');
        $follower->follow($this->netizen);
        $this->sut = $this->factory->create('social_private_message', null, ['csrf_protection' => false]);

        $this->sut->submit([
            'target' => 'spock',
            'message' => 'gg'
        ]);

        $this->assertFalse($this->sut->isValid());
        $this->assertCount(1, $this->sut->get('message')->getErrors());
    }

}
