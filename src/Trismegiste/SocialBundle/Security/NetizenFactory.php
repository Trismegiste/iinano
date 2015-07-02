<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Security;

use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\Socialist\Author;
use Trismegiste\SocialBundle\Security\Profile;

/**
 * NetizenFactory is a factory for Netizen
 */
class NetizenFactory
{

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
     * Creates a new Admin
     *
     * @param string $nick
     * @param string $OauthProviderKey the key of the OAuth provider (github,facebook,twitter...)
     * @param string $uniqueUserId the unique id of the user given by the OAuth provider
     *
     * @return \Trismegiste\SocialBundle\Security\Netizen
     */
    public function createAdmin($nick, $OauthProviderKey, $uniqueUserId)
    {
        $user = $this->create($nick, $OauthProviderKey, $uniqueUserId);
        $user->setGroup('ROLE_ADMIN');

        return $user;
    }

}
