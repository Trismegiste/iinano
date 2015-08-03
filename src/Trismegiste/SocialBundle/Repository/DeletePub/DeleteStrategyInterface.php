<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository\DeletePub;

use Trismegiste\Socialist\Publishing;

/**
 * DeleteStrategyInterface is a contract for special processing when deleting a Publishing
 */
interface DeleteStrategyInterface
{

    /**
     * Pre-delete operations before removing the entity in the db
     *
     * @param Publishing $pub
     */
    public function remove(Publishing $pub);
}
