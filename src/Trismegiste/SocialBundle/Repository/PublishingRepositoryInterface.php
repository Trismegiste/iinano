<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Socialist\Publishing;

/**
 * PublishingRepositoryInterface is a contract for a repository of published content
 */
interface PublishingRepositoryInterface
{

    /**
     * Retrieves an iterator on the last published entries
     * 
     * @param int $limit
     * 
     * @return \Trismegiste\Yuurei\Persistence\CollectionIterator
     */
    public function findLastEntries($limit = 20);

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
}