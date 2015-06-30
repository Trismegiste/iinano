<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Form;

/**
 * RegisterTypeTest tests RegisterType
 */
class RegisterTypeTest extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{

    /** @var \Symfony\Component\Form\FormInterface */
    protected $sut;

    /** @var \Trismegiste\SocialBundle\Security\NetizenFactory */
    protected $factory;

    /** @var \MongoCollection */
    protected $collection;

    /** @var \Trismegiste\SocialBundle\Repository\NetizenRepositoryInterface */
    protected $repository;

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $formFactory = $kernel->getContainer()->get('form.factory');
        $this->collection = $kernel->getContainer()->get('dokudoki.collection');
        $this->factory = $kernel->getContainer()->get('security.netizen.factory');
        $this->repository = $kernel->getContainer()->get('social.netizen.repository');
        $this->sut = $formFactory->create('netizen_register', null, ['csrf_protection' => false]);
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
            'password' => 'aaaa',
            'fullName' => 'Daenerys Stormborn',
            'gender' => 'xx',
            'email' => 'mother@dragon.org',
            'dateOfBirth' => ['year' => 1986, 'month' => 11, 'day' => 13]
        ];
        $this->sut->submit($submitted);
        $this->assertTrue($this->sut->isValid());
        $user = $this->sut->getData();
        $this->assertInstanceOf('Trismegiste\SocialBundle\Security\Netizen', $user);
        $this->assertEquals('daenerys-targa-7', $user->getUsername());
        $this->assertEquals('xx', $user->getProfile()->gender);
        $this->assertNotEmpty($user->getPassword());
        $this->assertEquals('Daenerys Stormborn', $user->getProfile()->fullName);
    }

    public function testNickTooShort()
    {
        $submitted = ['nickname' => 'dany', 'password' => 'aaaa', 'fullName' => 'Daenerys Stormborn', 'gender' => 'xx'];
        $this->sut->submit($submitted);
        $this->assertFalse($this->sut->isValid());
        $this->assertRegexp('#too short#', $this->sut->get('nickname')->getErrors()[0]->getMessage());
    }

    public function getNicknameExample()
    {
        return [
            ['illùvatar', 'Ainu Illùvatar'],
            ['john snow', 'John Targaryen'],
            ['john_snow', 'John Targaryen'],
            ['Spock', 'Mr Spock']
        ];
    }

    /**
     * @dataProvider getNicknameExample
     */
    public function testNickBadChar($nick, $full)
    {
        $submitted = ['nickname' => $nick, 'fullName' => $full, 'password' => 'aaaa', 'gender' => 'xy'];
        $this->sut->submit($submitted);
        $this->assertFalse($this->sut->isValid());
        $this->assertRegexp('#not valid#', $this->sut->get('nickname')->getErrors()[0]->getMessage());
    }

    public function testAlreadyExisting()
    {
        $obj = $this->factory->create('mcleod', 'aaaa');
        $this->repository->persist($obj);

        $submitted = ['nickname' => 'mcleod', 'fullName' => 'McLeod', 'gender' => 'xy', 'password' => 'aaaa'];
        $this->sut->submit($submitted);
        $this->assertRegexp('#already used#', $this->sut->get('nickname')->getErrors()[0]->getMessage());
    }

}
