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
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * NetizenRepository is a repository for Netizen (and also Author)
 * 
 * @todo Is this a decorator ( ie implementing RepositoryInterface ) ?
 */
class NetizenRepository implements NetizenRepositoryInterface
{

    protected $repository;
    protected $classAlias;
    protected $encoderFactory;
    protected $storage;

    public function __construct(RepositoryInterface $repo, EncoderFactoryInterface $encoderFactory, $alias, AvatarRepository $storage)
    {
        $this->repository = $repo;
        $this->classAlias = $alias;
        $this->encoderFactory = $encoderFactory;
        $this->storage = $storage;
    }

    public function findByNickname($nick)
    {
        $obj = $this->repository->findOne([
            MapAlias::CLASS_KEY => $this->classAlias,
            'author.nickname' => $nick
        ]);

        return $obj;
    }

    public function create($nick, $password)
    {
        //  db.dokudoki.ensureIndex({"author.nickname":1},{sparse:true, unique:true});
        $author = new Author($nick);
        $user = new Netizen($author);

        $salt = \rand(100, 999);
        $password = $this->encoderFactory
                ->getEncoder($user)
                ->encodePassword($password, $salt);
        $user->setCredential(new Internal($password, $salt));

        return $user;
    }

    public function persist(Netizen $obj)
    {
        $this->repository->persist($obj);
    }

    public function findByPk($id)
    {
        return $this->repository->findByPk($id);
    }

    public function updateAvatar(Netizen $user, UploadedFile $fch = null)
    {
        $this->storage->updateAvatar($user->getAuthor(), $user->getProfile(), $fch);
        $this->persist($user);
    }

    public function isExistingNickname($nick)
    {
        return !is_null($this->findByNickname($nick));
    }

}