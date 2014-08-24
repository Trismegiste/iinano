<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

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
}