<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Yuurei\Persistence\RepositoryInterface;
use Trismegiste\DokudokiBundle\Transform\Mediator\Colleague\MapAlias;
use Trismegiste\SocialBundle\Security\Netizen;
use Trismegiste\Socialist\Author;
use Trismegiste\SocialBundle\Security\Credential\Internal;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Trismegiste\SocialBundle\Security\Profile;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * NetizenRepository is a repository for Netizen (and also Author)
 *
 * @todo Is this a decorator ( ie implementing RepositoryInterface ) ?
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
     * @param EncoderFactoryInterface $encoderFactory the Security component factory which manages encoders for password
     * @param string $alias the class key alias for the Netizen objects stored with Dokudoki
     * @param \Trismegiste\SocialBundle\Repository\AvatarRepository $storage a repository for storing avatar pictures
     *
     * @todo this service does too many thing, split into :
     *  - a factory (anonymous user)
     *  - read only access
     *  - social action (follow/like)
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
        // pre-persist :
        if (is_null($obj->getAuthor()->getAvatar())) {
            // @todo parameter for config
            $avatarName = $obj->getProfile()->gender == 'xx' ? "00.jpg" : '01.jpg';
            $obj->getAuthor()->setAvatar($avatarName);
        }

        $this->repository->persist($obj);
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
            $this->persist($user);
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

}
