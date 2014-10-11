<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Socialist\Commentary;
use Trismegiste\Socialist\Publishing;

/**
 * CommentaryRepository is a repository for Commentary
 */
class CommentaryRepository extends SecuredContentProvider
{

    public function create()
    {
        return new Commentary($this->getAuthor());
    }

    public function findByUuid(Publishing $pub, $uuid)
    {
        return $pub->getCommentaryByUuid($uuid);
    }

    public function persist(Publishing $pub, Commentary $comm)
    {
        $this->assertOwningRight($comm);
        $comm->setLastEdited(new \DateTime());

        $this->repository->persist($pub);
    }

    public function attachAndPersist(Publishing $pub, Commentary $comm)
    {
        $this->assertOwningRight($comm);
        $comm->setLastEdited(new \DateTime());
        $pub->attachCommentary($comm);

        $this->repository->persist($pub);
    }

    public function detachAndPersist(Publishing $pub, $uuid)
    {
        $comm = $pub->getCommentaryByUuid($uuid);
        $this->assertOwningRight($comm);
        $pub->detachCommentary($comm);

        $this->repository->persist($pub);
    }

    public function iLikeThat($id, $uuid)
    {
        $pub = $this->repository->findByPk($id);
        $comm = $pub->getCommentaryByUuid($uuid);
        $comm->addFan($this->getAuthor());

        $this->repository->persist($pub);
    }

    public function iUnlikeThat($id, $uuid)
    {
        $pub = $this->repository->findByPk($id);
        $comm = $pub->getCommentaryByUuid($uuid);
        $comm->removeFan($this->getAuthor());

        $this->repository->persist($pub);
    }

    public function IReportThat($id, $uuid)
    {
        $pub = $this->repository->findByPk($id);
        $commentary = $pub->getCommentaryByUuid($uuid);
        $commentary->report($this->getAuthor());

        $this->repository->persist($pub);
    }

}
