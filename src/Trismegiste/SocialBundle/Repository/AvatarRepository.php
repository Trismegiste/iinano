<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Socialist\AuthorInterface;
use Trismegiste\SocialBundle\Utils\ImageRefiner;

/**
 * AvatarRepository is a repository for avatar
 */
class AvatarRepository
{

    protected $storage;
    protected $imageTool;
    protected $avatarSize;

    public function __construct($path, ImageRefiner $imageTool, $dimension)
    {
        $this->storage = realpath($path) . DIRECTORY_SEPARATOR;
        $this->imageTool = $imageTool;
        $this->avatarSize = $dimension;
    }

    public function updateAvatar(AuthorInterface $author, $imageResource)
    {
        if (!is_resource($imageResource)) {
            throw new \InvalidArgumentException('The image is not a valid resource');
        }

        try {
            $avatarName = $this->getAvatarName($author->getNickname()) . '.jpg';
            $destination = $this->getAvatarAbsolutePath($avatarName);
            $this->imageTool->makeSquareThumbnailFrom($imageResource, $destination, $this->avatarSize);
        } catch (\Exception $e) {
            throw new \RuntimeException('Unable to save avatar');
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
