<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Yuurei\Persistence\RepositoryInterface;
use Trismegiste\DokudokiBundle\Transform\Mediator\Colleague\MapAlias;
use Trismegiste\SocialBundle\Security\Netizen;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * NetizenRepository is a repository for Netizen (and also Author)
 *
 * @todo Subclass of SecuredContentProvider ?
 * @todo optimization: remove all filter on alias, relying solely on author.nickname
 */
class NetizenRepository implements NetizenRepositoryInterface
{

    protected $repository;
    protected $classAlias;
    protected $storage;

    /**
     * Ctor
     *
     * @param RepositoryInterface $repo the repository for MongoCollection
     * @param string $alias the class key alias for the Netizen objects stored with Dokudoki
     * @param \Trismegiste\SocialBundle\Repository\AvatarRepository $storage a repository for storing avatar pictures
     *
     */
    public function __construct(RepositoryInterface $repo, $alias, AvatarRepository $storage)
    {
        if (!is_string($alias)) {
            throw new \InvalidArgumentException('Alias for Netizen is not a string');
        }
        $this->repository = $repo;
        $this->classAlias = $alias;
        $this->storage = $storage;
    }

    /**
     * @inheritdoc
     */
    public function findByNickname($nick)
    {
        $obj = $this->repository->findOne([
            MapAlias::CLASS_KEY => $this->classAlias,
            'author.nickname' => $nick
        ]);

        return $obj;
    }

    /**
     * @inheritdoc
     */
    public function findBatchNickname(\Iterator $nick)
    {
        $cursor = $this->repository->find([
            MapAlias::CLASS_KEY => $this->classAlias,
            'author.nickname' => ['$in' => array_keys(iterator_to_array($nick))]
        ]);

        return $cursor;
    }

    /**
     * @inheritdoc
     */
    public function persist(Netizen $obj)
    {
        $this->repository->persist($obj);

        // post-persist : manage avatar, so any possible problem with image doesn't mess a new netizen creation
        if (is_null($obj->getAuthor()->getAvatar())) {
            $img = $this->storage->getIcon($obj->getProfile()->gender);
            $this->updateAvatar($obj, $img);
        }
    }

    /**
     * @inheritdoc
     */
    public function findByPk($id)
    {
        $doc = $this->repository->findByPk($id);
        if (!$doc instanceof Netizen) {
            throw new \LogicException("$id is type of " . get_class($doc));
        }

        return $doc;
    }

    /**
     * @inheritdoc
     */
    public function updateAvatar(Netizen $user, $imageResource)
    {
        try {
            $this->storage->updateAvatar($user->getAuthor(), $imageResource);
            $this->repository->persist($user);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function isExistingNickname($nick)
    {
        return !is_null($this->findByNickname($nick));
    }

    /**
     * @inheritdoc
     */
    public function search($filter = null)
    {
        $query = [ MapAlias::CLASS_KEY => $this->classAlias];

        if (!is_null($filter)) {
            $query['author.nickname'] = new \MongoRegex("/^$filter/");
        }

        return $this->repository->find($query);
    }

    public function countAllUser()
    {
        $query = [MapAlias::CLASS_KEY => $this->classAlias];

        return $this->repository->getCursor($query)->count();
    }

}
