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
        $this->storage = realpath($path) . DIRECTORY_SEPARATOR;
        $this->imageTool = $imageTool;
    }

    public function updateAvatar(AuthorInterface $author, Profile $profile, UploadedFile $fch = null)
    {
        // @todo need to injected from config as a map : (with default)
        // @todo this line below is the job of the Netizen repo, not Avatar repo
        // therefore remove the default "= null" for $fch
        $avatarName = $profile->gender == 'xx' ? "00.jpg" : '01.jpg';

        if (!is_null($fch) && ($fch->getMimeType() == 'image/jpeg')) {
            try {
                $avatarName = $this->getAvatarName($author->getNickname()) . '.jpg';
                $fch->move($this->storage, $avatarName);
                $source = $this->storage . $avatarName;
                $this->imageTool->makeThumbnailFrom($source, $source, 300);
            } catch (\Exception $e) {
                // @todo throw something (what ?)
            }
        }

        $author->setAvatar($avatarName);
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