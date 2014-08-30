<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Trismegiste\Socialist\AuthorInterface;

/**
 * AvatarRepository is a repository for user's avatar
 */
class AvatarRepository
{

    protected $storage;

    public function __construct($path)
    {
        $this->storage = $path;
    }

    public function persist(AuthorInterface $user, UploadedFile $fch)
    {
        $abstracted = $this->getAvatarName($user->getNickname()) . '.' . $fch->getClientOriginalExtension();
        $user->setAvatar($abstracted);
        $fch->move($this->storage, $abstracted);
    }

    protected function getAvatarName($nick)
    {
        return bin2hex($nick);
    }

    public function getAvatarAbsolutePath($filename)
    {
        return $this->storage . $filename;
    }

}