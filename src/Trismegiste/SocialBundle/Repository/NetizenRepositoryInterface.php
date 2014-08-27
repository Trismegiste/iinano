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
     * Creates a new Netizen from mandatory datas
     * 
     * @param string $nick
     * 
     * @return \Trismegiste\SocialBundle\Security\Netizen
     */
    public function create($nick);

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
     */
    public function findByPk($id);
}