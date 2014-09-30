<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\SocialBundle\Security\Netizen;

/**
 * NetizenRepositoryInterface is a contract for a NetizenRepository
 */
interface NetizenRepositoryInterface
{

    /**
     * Finds a user by its author's nickname
     *
     * @param string $nick
     *
     * @return \Trismegiste\SocialBundle\Security\Netizen|null if the user is found or not
     */
    public function findByNickname($nick);

    /**
     * Get a batch of users with theirs nicknames
     *
     * @param \Iterator $nick an array of nickname as key and true as value
     *
     * @return \Trismegiste\Yuurei\Persistence\CollectionIterator
     */
    public function findBatchNickname(\Iterator $nick);

    /**
     * Creates a new Netizen from mandatory datas
     *
     * @param string $nick
     * @param string $pwd
     *
     * @return \Trismegiste\SocialBundle\Security\Netizen
     */
    public function create($nick, $pwd);

    /**
     * Persists a Netizen into the db
     *
     * @param \Trismegiste\SocialBundle\Repository\Netizen $obj
     */
    public function persist(Netizen $obj);

    /**
     * Retrieve a Netizen by its pk
     *
     * @param string $id
     *
     * @return \Trismegiste\SocialBundle\Security\Netizen
     */
    public function findByPk($id);

    /**
     * Is a nickname already existing in database ?
     *
     * @param string $nick
     *
     * @return bool true if already exist
     */
    public function isExistingNickname($nick);

    /**
     * Update and persist a Netizen with an image resource
     *
     * @param \Trismegiste\SocialBundle\Security\Netizen $user
     * @param resource $imageResource a GD image resource
     */
    public function updateAvatar(Netizen $user, $imageResource);
}
