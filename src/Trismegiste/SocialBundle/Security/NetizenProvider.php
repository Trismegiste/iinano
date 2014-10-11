<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Trismegiste\Yuurei\Persistence\RepositoryInterface;
use Trismegiste\DokudokiBundle\Transform\Mediator\Colleague\MapAlias;

/**
 * NetizenProvider is a provider of symfony user based on Socialist User
 *
 * Note sur la sécurité :
 * 1. un firewall (ListenerInterface) écoute les request et les transforme en token (TokenInterface)
 * 2. il passe ce token à l'authentication manager (qui agrege des authentication provider)
 * 3. l'authentication provider récupère ce token non-authentifié et match avec un UserProvider
 * 4. il construit un token authentifié
 * 5. tous ces services sont créés par une factory SecurityFactoryInterface
 * 6. ne pas oublier d'abonner la factory au service security dans le build du bundle
 */
class NetizenProvider implements UserProviderInterface
{

    protected $userClassAlias;

    /** @var \Trismegiste\Yuurei\Persistence\RepositoryInterface */
    protected $userRepository;

    public function __construct(RepositoryInterface $repo, $alias)
    {
        $this->userClassAlias = $alias;
        $this->userRepository = $repo;
    }

    public function loadUserByUsername($username)
    {
        $found = $this->userRepository->findOne([
            MapAlias::CLASS_KEY => $this->userClassAlias,
            'author.nickname' => $username
        ]);

        if (is_null($found)) {
            throw new UsernameNotFoundException("We don't know $username");
        }

        return $found;
    }

    public function refreshUser(UserInterface $user)
    {
        if ($this->supportsClass(get_class($user))) {
            return $this->userRepository->findByPk((string) $user->getId());
        } else {
            throw new UnsupportedUserException("Don't know to manage a " . get_class($user));
        }
    }

    public function supportsClass($class)
    {
        return $class === __NAMESPACE__ . '\Netizen';
    }

}
