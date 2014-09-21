<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Trismegiste\Socialist\AuthorInterface;
use Trismegiste\SocialBundle\Utils\ImageRefiner;

/**
 * AvatarRepository is a repository for avatar
 *
 * @todo need for an interface FFS !
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
        $this->avatarSize = (int) $dimension;
        if ($this->avatarSize <= 0) {
            throw new \OutOfRangeException("'$dimension' is not a valid dimension for a picture");
        }
    }

    /**
     * Update the author's avatar with a given GD image resource
     * Persists the image file and update the property of Author
     * !! No persistence on Author, only edge effect !!
     *
     * @param AuthorInterface $author the author to update
     * @param resource $imageResource a GD image resource
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
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

    /**
     * Returns a cleaned name for the avatar file. This is a bijection
     *
     * @param string $nick a nickname
     *
     * @return string the basename of the avatar file
     */
    protected function getAvatarName($nick)
    {
        // it is not a way to "crypt" or whatsoever, it's a way to avoid two things:
        // * strange characters in filesystem without collision when nicknames are utf-8
        //   (for example: "kurogané" & "kurogane")
        // * loose validation for parameters in routes (only hexadec digit)
        return bin2hex($nick);
    }

    /**
     * Returns the full pathname to an avatar with a given basename
     *
     * @param string $filename the basename + extension
     *
     * @return string the full path to avatar
     */
    public function getAvatarAbsolutePath($filename)
    {
        return $this->storage . $filename;
    }

}
