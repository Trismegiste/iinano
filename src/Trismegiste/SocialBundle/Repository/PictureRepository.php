<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Trismegiste\Socialist\Picture;
use Gregwar\Image\Image;

/**
 * PictureRepository is a storage repository for managing picture and thumbnail
 */
class PictureRepository
{

    const MIMETYPE_REGEX = '#^image/(jpg|jpeg|gif|png)$#';

    protected $storage;

    public function __construct($storageDir)
    {
        $this->storage = realpath($storageDir);

        if (!$this->storage) {
            throw new \InvalidArgumentException("$storageDir is not a valid directory");
        }
        $this->storage .= DIRECTORY_SEPARATOR;
    }

    /**
     * Stores an uploaded file to the storage and returns a Picture document for this picture
     *
     * @param UploadedFile $picFile
     *
     * @return \Trismegiste\Socialist\Picture
     *
     * @throws \InvalidArgumentException Bad mimetype
     */
    public function store(Picture $pub, UploadedFile $picFile)
    {
        if (!$picFile->isValid()) {
            throw new \RuntimeException('Upload was incomplete');
        }
        $serverMimeType = $picFile->getMimeType();

        $nick = $pub->getAuthor()->getNickname();
        $extension = [];
        if (!preg_match(self::MIMETYPE_REGEX, $serverMimeType, $extension)) {
            throw new \InvalidArgumentException($serverMimeType . ' is not a valid mime type');
        }

        $syntheticName = sha1($nick . microtime(false) . rand()) . '.' . $extension[1];
        $pub->setMimeType($serverMimeType);
        $pub->setStorageKey($syntheticName);

        $path = $this->getAbsolutePath($syntheticName);

        Image::open($picFile->getPathname())
                ->cropResize(1000, 1000)
                ->save($path);

        //$picFile->move($this->storage, $syntheticName);
    }

    public function getAbsolutePath($filename)
    {
        return $this->storage . implode('/', str_split(substr($filename, 0, 4))) . '/' . $filename;
    }

}
