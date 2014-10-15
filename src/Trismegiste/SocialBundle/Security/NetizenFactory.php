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

    protected $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * Creates a new Netizen from mandatory datas
     *
     * @param string $nick
     * @param string $pwd
     *
     * @return \Trismegiste\SocialBundle\Security\Netizen
     */
    public function create($nick, $password)
    {
        $author = new Author($nick);
        $user = new Netizen($author);

        $salt = \rand(100, 999);
        $encoded = $this->encoderFactory
                ->getEncoder($user) // @todo Demeter's law violation : inject encoder as a service with a factory ?
                ->encodePassword($password, $salt);
        $user->setCredential(new Internal($encoded, $salt));
        $user->setProfile(new Profile());
        $user->setGroup('ROLE_USER');

        return $user;
    }

}
