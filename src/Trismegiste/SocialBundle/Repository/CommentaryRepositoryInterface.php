<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Socialist\Commentary;
use Trismegiste\Socialist\Publishing;

/**
 * Contract for a repository of Commentary
 */
interface CommentaryRepositoryInterface
{

    /**
     * Finds a commentary by its uuid in a given Publishing
     *
     * @param \Trismegiste\SocialBundle\Repository\Publishing $pub
     * @param string $uuid a uuid of the commentary to be found
     *
     * @return Commentary|null
     */
    function findByUuid(Publishing $pub, $uuid);

    /**
     * Persists a published content with a Commentary
     * Secured action by the current user.
     * Checks the rights on Commentary. It permits to save a Publishing from another
     * for editing a Commentary for example.
     *
     * @param Publishing $pub
     * @param Commentary $comm
     */
    function persist(Publishing $pub, Commentary $comm);

    /**
     * Attachs and saves a Commentary to a Publishing
     * Secured action by the current user.
     *
     * @param Publishing $pub
     * @param Commentary $comm
     */
    function attachAndPersist(Publishing $pub, Commentary $comm);

    /**
     * Detachs a Commentary and saves the Publishing entity
     *
     * @param Publishing $pub
     * @param string $uuid
     */
    function detachAndPersist(Publishing $pub, $uuid);

    /**
     * Current user likes a Commentary
     *
     * @param string $id
     * @param string $uuid
     */
    function iLikeThat($id, $uuid);

    /**
     * Current user unlikes a Commentary
     *
     * @param string $id
     * @param string $uuid
     */
    function iUnlikeThat($id, $uuid);

    /**
     * Current user makes a report on a Commentary
     *
     * @param string $id
     * @param string $uuid
     */
    function iReportThat($id, $uuid);

    /**
     * Current user cancels a report on a Commentary
     *
     * @param string $id
     * @param string $uuid
     */
    function iCancelReport($id, $uuid);
}
