<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Socialist\Publishing;
use Trismegiste\Socialist\Follower;

/**
 * PublishingRepositoryInterface is a contract for a repository of published content
 * @todo the concrete class is not in sync => add a test
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
}
