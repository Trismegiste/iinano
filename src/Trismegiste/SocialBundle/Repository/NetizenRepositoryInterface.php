<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\Yuurei\Persistence\CollectionIterator;

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
     * @return Netizen|null if the user is found or not
     */
    public function findByNickname($nick);

    /**
     * Get a batch of users with theirs nicknames
     *
     * @param \Iterator $nick an array of nickname as key and true as value
     *
     * @return CollectionIterator
     */
    public function findBatchNickname(\Iterator $nick);

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
     * @return Netizen
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
     * @param Netizen $user
     * @param resource $imageResource a GD image resource
     */
    public function updateAvatar(Netizen $user, $imageResource);

    /**
     * Search for a user
     *
     * @param array $filter values of NetizenFilterType
     */
    public function search(array $filter);

    /**
     * Return the count of all registered users
     *
     * @return int
     */
    public function countAllUser();

    /**
     * Change the group of a given Netizen
     *
     * @param Netizen $user
     * @param SecurityContextInterface $ctx
     *
     * @throws AccessDeniedException if the current logged user has no right to do this action
     */
    public function promote(Netizen $user, SecurityContextInterface $ctx);

    /**
     * Gets a cursor on the last registered netizen
     *
     * @param int $limit
     */
    public function findLastRegistered($limit = 12);
}
