<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Trismegiste\Socialist\Picture;
use Gregwar\Image\Image;

/**
 * PictureRepository is a storage repository for managing pictures and thumbnails
 */
class PictureRepository
{

    const MIMETYPE_REGEX = '#^image/(jpg|jpeg|gif|png)$#';
    const SLOW_FS_CHUNK = 4; // the directory chunk for slow filesystem
    const MAX_RES = 'full';

    /**
     * Absolute path to directory for max resolution pictures
     *
     * @var string
     */
    protected $storageDir;

    /**
     * Absolute path to directory for cached & resized pictures
     *
     * @var string
     */
    protected $cacheDir;

    /**
     * Size configuration for pictures
     */
    protected $sizeConfig;

    public function __construct($storageDir, $cacheDir, array $sizeCfg)
    {
        $this->storageDir = realpath($storageDir);
        if (!$this->storageDir) {
            throw new \InvalidArgumentException("$storageDir is not a valid directory for storage");
        }
        $this->storageDir .= DIRECTORY_SEPARATOR;

        $this->cacheDir = realpath($cacheDir);
        if (!$this->cacheDir) {
            throw new \InvalidArgumentException("$cacheDir is not a valid directory for cache");
        }
        $this->cacheDir .= DIRECTORY_SEPARATOR;

        if (!array_key_exists(self::MAX_RES, $sizeCfg)) {
            throw new \InvalidArgumentException("The size configuration for Picture is invalid");
        }
        $this->sizeConfig = $sizeCfg;
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

        $syntheticName = $this->hashForNick($nick) . '.' . $extension[1];
        $pub->setMimeType($serverMimeType);
        $pub->setStorageKey($syntheticName);

        $path = $this->getAbsolutePath($syntheticName);

        Image::open($picFile->getPathname())
                ->cropResize($this->sizeConfig[self::MAX_RES], $this->sizeConfig[self::MAX_RES])
                ->save($path);

        //$picFile->move($this->storageDir, $syntheticName);
    }

    public function getAbsolutePath($filename, $size = self::MAX_RES)
    {
        $sourceImg = $this->storageDir
                . implode('/', str_split(substr($filename, 0, self::SLOW_FS_CHUNK)))
                . '/'
                . $filename;

        if ($size !== self::MAX_RES) {
            $sourceImg = Image::open($sourceImg)
                    ->setCacheDir($this->cacheDir)
                    ->resize($size)
                    ->guess();
        }

        return $sourceImg;
    }

    protected function hashForNick($nick)
    {
        return sha1($nick . microtime(false) . rand());
    }

}
