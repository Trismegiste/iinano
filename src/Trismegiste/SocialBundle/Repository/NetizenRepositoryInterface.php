<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\SocialBundle\Security\Netizen;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * Update a Netizen with an uploaded image
     * 
     * @param \Trismegiste\SocialBundle\Security\Netizen $user
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $fch
     */
    public function updateAvatar(Netizen $user, UploadedFile $fch = null);
}