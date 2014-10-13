<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * PictureRepository is a storage repository for managing picture and thumbnail
 */
class PictureRepository
{

    const MIMETYPE_REGEX = '#^image/(jpg|jpeg|gif|png)$#';

    protected $storage;

    /** @var \Trismegiste\SocialBundle\Repository\PublishingFactory */
    protected $repository;

    public function __construct(PublishingFactory $repo, $storageDir)
    {
        $this->repository = $repo;
        $this->storage = realpath($storageDir);

        if (!$this->storage) {
            throw new \InvalidArgumentException("$storageDir is not a valid directory");
        }
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
    public function store(UploadedFile $picFile)
    {
        $pub = $this->repository->create('picture');
        $nick = $pub->getAuthor()->getNickname();
        $extension = [];
        if (!preg_match(self::MIMETYPE_REGEX, $picFile->getMimeType(), $extension)) {
            throw new \InvalidArgumentException($picFile->getMimeType() . ' is not a valid mime type');
        }

        $syntheticName = sha1($nick . microtime(false) . rand()) . '.' . $extension[1];
        $pub->setMimeType($picFile->getMimeType());
        $pub->setStorageKey($syntheticName);
        copy($picFile->getPathname(), $this->storage . '/' . $syntheticName);

        return $pub;
    }

}
