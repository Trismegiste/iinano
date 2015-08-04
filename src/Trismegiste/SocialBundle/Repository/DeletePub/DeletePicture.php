<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Repository\DeletePub;

use Trismegiste\SocialBundle\Repository\PictureRepository;
use Trismegiste\Socialist\Publishing;

/**
 * DeletePicture is a stategy for removing image file when deleting entity Picture in DB
 */
class DeletePicture implements DeleteStrategyInterface
{

    protected $storage;

    public function __construct(PictureRepository $store)
    {
        $this->storage = $store;
    }

    public function remove(Publishing $pub)
    {
        if (!$pub instanceof \Trismegiste\Socialist\Picture) {
            throw new \InvalidArgumentException(get_class($pub) . ' is not a Picture');
        }

        $this->storage->remove($pub->getStorageKey());
    }

}
