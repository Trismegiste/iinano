<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Socialist\AuthorInterface;
use Trismegiste\SocialBundle\Security\Profile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Trismegiste\SocialBundle\Utils\ImageRefiner;

/**
 * AvatarRepository is a repository for avatar
 */
class AvatarRepository
{

    protected $storage;
    protected $imageTool;

    public function __construct($path, ImageRefiner $imageTool)
    {
        $this->storage = $path;
        $this->imageTool = $imageTool;
    }

    public function updateAvatar(AuthorInterface $author, Profile $profile, UploadedFile $fch = null)
    {
        // @todo need to injected from config as a map : (with default)
        $abstracted = $profile->gender == 'xx' ? "00.jpg" : '01.jpg';

        if (!is_null($fch) && ($fch->getMimeType() == 'image/jpeg')) {
            $abstracted = $this->getAvatarName($author->getNickname()) . '.jpg';
            $fch->move($this->storage, $abstracted);
            $source = $this->storage . '/' . $abstracted;
            $this->imageTool->makeThumbnailFrom($source, $source, 300);
        }

        $author->setAvatar($abstracted);
    }

    protected function getAvatarName($nick)
    {
        // it is not a way to "crypt" or whatsoever, it's a way to avoid two things: 
        // * strange characters in filesystem without collision when nicknames are utf-8
        //   (for example: "kurogané" & "kurogane")
        // * loose validation for parameters in routes (only hexadec digit)
        return bin2hex($nick);
    }

    public function getAvatarAbsolutePath($filename)
    {
        return $this->storage . $filename;
    }

}