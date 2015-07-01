<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Security;

use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\Socialist\Author;
use Trismegiste\SocialBundle\Security\Credential\Internal;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Trismegiste\SocialBundle\Security\Profile;

/**
 * NetizenFactory is a factory for Netizen
 */
class NetizenFactory
{

    /** @var EncoderFactoryInterface */
    protected $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * Creates a new Netizen from mandatory datas
     *
     * @param string $nick
     * @param string $OauthProviderKey the key of the OAuth provider (github,facebook,twitter...)
     * @param string $uniqueUserId the unique id of the user given by the OAuth provider
     *
     * @return \Trismegiste\SocialBundle\Security\Netizen
     */
    public function create($nick, $OauthProviderKey, $uniqueUserId)
    {
        $author = new Author($nick);
        $user = new Netizen($author);

        $strat = new Credential\OAuth($uniqueUserId, $OauthProviderKey);
        $user->setCredential($strat);
        $user->setProfile(new Profile());
        $user->setGroup('ROLE_USER');

        return $user;
    }

    /**
     * Set a new password to the given user (no persistence of whatsoever)
     *
     * @param Netizen $user
     * @param string $plainPassword
     */
    public function setNewCredential(Netizen $user, $plainPassword)
    {
        $salt = \rand(100, 999);
        $encoded = $this->encoderFactory
                ->getEncoder($user) // @todo Demeter's law violation : inject encoder as a service with a factory ?
                ->encodePassword($plainPassword, $salt);
        $user->setCredential(new Internal($encoded, $salt));
    }

}
