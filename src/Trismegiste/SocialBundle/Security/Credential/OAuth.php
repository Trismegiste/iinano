<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Security\Credential;

/**
 * OAuth is a credential strategy for OAuth-identified user
 */
class OAuth implements Strategy
{

    protected $uid;
    protected $provider;

    public function __construct($uid, $providerKey)
    {
        $this->uid = $uid;
        $this->provider = $providerKey;
    }

    public function getProviderKey()
    {
        return $this->provider;
    }

    public function getUid()
    {
        return $this->uid;
    }

}
