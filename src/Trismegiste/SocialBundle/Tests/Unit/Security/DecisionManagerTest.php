<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Tests\Unit\Security;

use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * DecisionManagerTest is an example to show how voter works because it's a mindfuck
 */
class DecisionManagerTest extends \PHPUnit_Framework_TestCase
{

    /** @var AccessDecisionManager */
    protected $sut;

    public function testOneVoterGranted()
    {
        $voters = [
            new AlwaysVoter(VoterInterface::ACCESS_GRANTED)
        ];
        $this->sut = new AccessDecisionManager($voters);

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->assertTrue($this->sut->decide($token, ['DUMMY']));
    }

    public function testOneVoterDenied()
    {
        $voters = [
            new AlwaysVoter(VoterInterface::ACCESS_DENIED)
        ];
        $this->sut = new AccessDecisionManager($voters);

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->assertFalse($this->sut->decide($token, ['DUMMY']));
    }

    public function testTwoOppositeVoter()
    {
        $voters = [
            new AlwaysVoter(VoterInterface::ACCESS_GRANTED),
            new AlwaysVoter(VoterInterface::ACCESS_DENIED)
        ];
        $this->sut = new AccessDecisionManager($voters);

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->assertTrue($this->sut->decide($token, ['DUMMY']));
    }

    public function testOneGrantedIsEnough()
    {
        $voters = [
            new AlwaysVoter(VoterInterface::ACCESS_DENIED),
            new AlwaysVoter(VoterInterface::ACCESS_GRANTED),
            new AlwaysVoter(VoterInterface::ACCESS_DENIED)
        ];
        $this->sut = new AccessDecisionManager($voters);

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->assertTrue($this->sut->decide($token, ['DUMMY']));
    }

    public function testOneAbstainIsFail()
    {
        $voters = [
            new AlwaysVoter(VoterInterface::ACCESS_ABSTAIN),
        ];
        $this->sut = new AccessDecisionManager($voters);

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->assertFalse($this->sut->decide($token, ['DUMMY']));
    }

}

class AlwaysVoter implements VoterInterface
{

    protected $alwaysVote;

    public function __construct($cfg)
    {
        $this->alwaysVote = $cfg;
    }

    public function supportsAttribute($attribute)
    {

    }

    public function supportsClass($class)
    {

    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        return $this->alwaysVote;
    }

}
