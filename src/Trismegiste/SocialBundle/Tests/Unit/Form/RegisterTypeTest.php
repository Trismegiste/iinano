<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

use MongoCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\FormInterface;
use Trismegiste\OAuthBundle\Security\Token;
use Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface;
use Trismegiste\SocialBundle\Security\NetizenFactory;
use Trismegiste\SocialBundle\Security\NotRegisteredHandler;

/**
 * RegisterTypeTest tests \Trismegiste\SocialBundle\Form\RegisterType
 */
class RegisterTypeTest extends WebTestCase
{

    /** @var FormInterface */
    protected $sut;

    /** @var NetizenFactory */
    protected $factory;

    /** @var MongoCollection */
    protected $collection;

    /** @var NetizenRepositoryInterface */
    protected $repository;

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $formFactory = $kernel->getContainer()->get('form.factory');
        $this->collection = $kernel->getContainer()->get('dokudoki.collection');
        $this->factory = $kernel->getContainer()->get('security.netizen.factory');
        $this->repository = $kernel->getContainer()->get('social.netizen.repository');

        $session = $kernel->getContainer()->get('session');
        $token = new Token('secured_area', 'dummy', '123456789');
        $token->setAttribute('nickname', 'dummy nickname');
        $session->set(NotRegisteredHandler::IDENTIFIED_TOKEN, $token);

        $this->sut = $formFactory->create('netizen_register', null, [
            'csrf_protection' => false,
            'minimumAge' => 6
        ]);
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
        $submitted = [
            'nickname' => 'daenerys-targa-7',
            'gender' => 'xx',
            'dateOfBirth' => ['year' => 1986, 'month' => 11, 'day' => 13]
        ];
        $this->sut->submit($submitted);
        $this->assertTrue($this->sut->isValid());
        $user = $this->sut->getData();
        $this->assertInstanceOf('Trismegiste\SocialBundle\Security\Netizen', $user);
        $this->assertEquals('daenerys-targa-7', $user->getUsername());
        $this->assertEquals('xx', $user->getProfile()->gender);
        $this->assertNotEmpty($user->getCredential());
        $this->assertEquals('dummy', $user->getCredential()->getProviderKey());
        $this->assertEquals('123456789', $user->getCredential()->getUid());
    }

    public function testNickTooShort()
    {
        $submitted = ['nickname' => 'dany', 'gender' => 'xx'];
        $this->sut->submit($submitted);
        $this->assertFalse($this->sut->isValid());
        $this->assertRegexp('#too short#', $this->sut->get('nickname')->getErrors()[0]->getMessage());
    }

    public function getNicknameExample()
    {
        return [
            ['illùvatar'],
            ['john snow'],
            ['john_snow'],
            ['Spock']
        ];
    }

    /**
     * @dataProvider getNicknameExample
     */
    public function testNickBadChar($nick)
    {
        $submitted = ['nickname' => $nick, 'gender' => 'xy'];
        $this->sut->submit($submitted);
        $this->assertFalse($this->sut->isValid());
        $this->assertRegexp('#not valid#', $this->sut->get('nickname')->getErrors()[0]->getMessage());
    }

    public function testAlreadyExisting()
    {
        $obj = $this->factory->create('mcleod', 'facebook', '456456456'); // registered with another provider
        $this->repository->persist($obj);

        $submitted = ['nickname' => 'mcleod', 'gender' => 'xy'];
        $this->sut->submit($submitted);
        $this->assertRegexp('#already used#', $this->sut->get('nickname')->getErrors()[0]->getMessage());
    }

}
