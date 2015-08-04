<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\SocialBundle\Repository\DeletePub\DeleteStrategyInterface;
use Trismegiste\Socialist\Follower;
use Trismegiste\Socialist\Publishing;

/**
 * PublishingRepositoryInterface is a contract for a repository of published content
 */
interface PublishingRepositoryInterface
{

    /**
     * Retrieves an iterator on the last published entries
     *
     * @param int $offset
     * @param int $limit
     * @param \ArrayIterator $author an iterator over a list of AuthorInterface
     *
     * @return \Trismegiste\Yuurei\Persistence\CollectionIterator
     */
    public function findLastEntries($offset = 0, $limit = 20, \ArrayIterator $author = null);

    /**
     * Persists a published content
     *
     * @param \Trismegiste\Socialist\Publishing $doc
     */
    public function persist(Publishing $doc);

    /**
     * Returns a published content by its PK
     *
     * @param string $pk
     *
     * @return \Trismegiste\Socialist\Publishing
     */
    public function findByPk($pk);

    /**
     * Retrieves an iterator on the last published entries for a given wall & filter
     * @see PublishingRepositoryInterface::findLastEntries()
     *
     * @param Follower $wallUser
     * @param string $wallFilter a filter key from ['all', 'self' 'following', 'follower', 'friend']
     * @param int $offset @see MongoCursor::skip()
     * @param int $limit @see MongoCursor::limit()
     *
     * @return \Trismegiste\Yuurei\Persistence\CollectionIterator
     */
    public function findWallEntries(Follower $wallUser, $wallFilter, $offset = 0, $limit = 20);

    /**
     * Delete a published content (with the help of the underliying mongo collection)
     *
     * @param string $pk
     */
    public function delete($pk);

    /**
     * Get the class alias key for a Publishing subclass managed by this repository
     *
     * @return string
     */
    public function getClassAlias(Publishing $pub);

    /**
     * Add the current logged user to the list of fan of Publishing given by its id
     * and persistig it
     *
     * @param string $id primary key
     *
     * @return Publishing
     */
    public function iLikeThat($id);

    /**
     * Remove the current logged user to the list of fan of Publishing given by its id
     * and persistig it
     *
     * @param string $id primary key
     *
     * @return Publishing
     */
    public function iUnlikeThat($id);

    /**
     * The current user is reporting a Publishing as abuse or spam and persitence
     *
     * @param string $id the PK of the Publishing subclass
     */
    public function iReportThat($id);

    /**
     * Repeat a Publishing
     *
     * @param string $id the PK of the Publishing subclass
     *
     * @return \Trismegiste\Socialist\Repeat the repeated message
     */
    public function repeatPublishing($id);

    /**
     * The current user is canceling this report as abuse or spam and persitence
     *
     * @param string $id the PK of the Publishing subclass
     */
    public function iCancelReport($id);

    /**
     * Count all Published contents from all users
     *
     * @return int
     */
    public function countAllPublishing();

    /**
     * Add a strategy for performing additional tasks when deleting a Publishing entity
     *
     * @param string $type class alias
     * @param DeleteStrategyInterface $strat
     */
    public function addDeleteStrategy($type, DeleteStrategyInterface $strat);
}
