<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Socialist\Publishing;
use Trismegiste\Yuurei\Persistence\RepositoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Trismegiste\DokudokiBundle\Transform\Mediator\Colleague\MapAlias;
use Trismegiste\Socialist\Follower;

/**
 * PublishingRepository is a business repository for subclasses of Publishing
 */
class NewPublishingRepository extends SecuredContentProvider implements PublishingRepositoryInterface, PublishingFactory
{

    protected $aliasFilter;
    protected $classAlias;

    /**
     * Ctor
     *
     * @param \Trismegiste\Yuurei\Persistence\RepositoryInterface $repo
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $ctx
     * @param array $aliases a list a class key => FQCN for each document
     */
    public function __construct(RepositoryInterface $repo, SecurityContextInterface $ctx, array $aliases)
    {
        parent::__construct($repo, $ctx);
        $this->aliasFilter = [MapAlias::CLASS_KEY => ['$in' => array_keys($aliases)]];
        $this->classAlias = $aliases;
    }

    public function create($alias)
    {
        if (!array_key_exists($alias, $this->classAlias)) {
            throw new \DomainException("$alias is not a valid alias");
        }

        $refl = new \ReflectionClass($this->classAlias[$alias]);

        return $refl->newInstance($this->getAuthor());
    }

    public function persist(Publishing $pub)
    {
        $this->assertOwningRight($pub);
        $pub->setLastEdited(new \DateTime());

        $this->repository->persist($pub);
    }

    protected function assertPublishing($doc)
    {
        if (!$doc instanceof Publishing) {
            throw new \DomainException("$pk must be a Publishing subclass, " . get_class($doc) . ' instead');
        }
    }

    public function findByPk($pk)
    {
        $doc = $this->repository->findByPk($pk);
        $this->assertPublishing($doc);

        return $doc;
    }

    public function findLastEntries($offset = 0, $limit = 20, \ArrayIterator $author = null)
    {
        $docFilter = $this->aliasFilter;
        if (!is_null($author)) {
            $filter = array_keys(iterator_to_array($author));
            $docFilter['owner.nickname'] = ['$in' => $filter];
        }

        return $this->repository
                        ->find($docFilter)
                        ->limit($limit)
                        ->offset($offset)
                        ->sort(['createdAt' => -1]);
    }

    public function findWallEntries(Follower $wallUser, $wallFilter, $offset = 0, $limit = 20)
    {
        switch ($wallFilter) {

            case 'self':
                $filterAuthor = new \ArrayIterator([$wallUser->getUniqueId() => true]);
                break;

            case 'following':
                $filterAuthor = $wallUser->getFollowingIterator();
                break;

            case 'follower':
                $filterAuthor = $wallUser->getFollowerIterator();
                break;

            case 'friend':
                $filterAuthor = $wallUser->getFriendIterator();
                break;

            case 'all':
                $filterAuthor = null;
                break;

            default:
                throw new \InvalidArgumentException("$wallFilter is not valid filter");
        }

        return $this->findLastEntries($offset, $limit, $filterAuthor);
    }

    public function delete($pk, \MongoCollection $coll)
    {
        $pub = $this->findByPk($pk);
        $this->assertOwningRight($pub);
        $coll->remove(['_id' => new \MongoId($pk)]);
    }

    public function getClassAlias(Publishing $pub)
    {
        return array_search(get_class($pub), $this->classAlias);
    }

    public function iLikeThat($id)
    {
        $pub = $this->findByPk($id);
        $pub->addFan($this->getAuthor());

        $this->repository->persist($pub);
    }

    public function iUnlikeThat($id)
    {
        $pub = $this->findByPk($id);
        $pub->removeFan($this->getAuthor());

        $this->repository->persist($pub);
    }

    public function iReportThat($id)
    {
        $pub = $this->findByPk($id);
        $pub->report($this->getAuthor());

        $this->repository->persist($pub);
    }

}
