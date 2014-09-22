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

    /** @var Symfony\Component\Form\FormFactoryInterface */
    protected $factory;

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->factory = $kernel->getContainer()->get('form.factory');
        $this->sut = $this->factory->create('netizen_register', null, ['csrf_protection' => false]);
    }

    public function testValidSubmit()
    {
        $submitted = ['nickname' => 'daenerys', 'password' => 'aaaa', 'fullName' => 'Daenerys Stormborn', 'gender' => 'xx'];
        $this->sut->submit($submitted);
        $this->assertTrue($this->sut->isValid());
        $user = $this->sut->getData();
        $this->assertInstanceOf('Trismegiste\SocialBundle\Security\Netizen', $user);
        $this->assertEquals('daenerys', $user->getUsername());
        $this->assertEquals('xx', $user->getProfile()->gender);
        $this->assertNotEmpty($user->getPassword());
        $this->assertEquals('Daenerys Stormborn', $user->getProfile()->fullName);
    }

    public function testNickTooShort()
    {
        $submitted = ['nickname' => 'daen', 'password' => 'aaaa', 'fullName' => 'Daenerys Stormborn', 'gender' => 'xx'];
        $this->sut->submit($submitted);
        $this->assertFalse($this->sut->isValid());
        $this->assertRegexp('#too short#', $this->sut->get('nickname')->getErrors()[0]->getMessage());
    }

    public function getNicknameExample()
    {
        return [
            ['illùvatar', 'Ainu Illùvatar'],
            ['john snow', 'John Targaryan'],
            ['Spock', 'Mr Spock']
        ];
    }

    /**
     * @dataProvider getNicknameExample
     */
    public function testNickBadChar($nick, $full)
    {
        $submitted = ['nickname' => $nick, 'fullName' => $full, 'gender' => 'xy'];
        $this->sut->submit($submitted);
        $this->assertFalse($this->sut->isValid());
        $this->assertRegexp('#not valid#', $this->sut->get('nickname')->getErrors()[0]->getMessage());
    }

}
