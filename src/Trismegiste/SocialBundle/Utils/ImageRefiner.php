<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Utils;

/**
 * ImageRefiner is a service for resizing, reducing weight of image,
 * thumbnailing...
 */
class ImageRefiner
{

    public function makeThumbnailFrom($filename, $target, $maxBoxSize)
    {
        $source = \imagecreatefromjpeg($filename);
        $width = \imagesx($source);
        $height = \imagesy($source);

        if ($height > $width) {
            $newHeight = $maxBoxSize;
            $newWidth = $width * $maxBoxSize / (float) $height;
        } else {
            $newWidth = $maxBoxSize;
            $newHeight = $height * $maxBoxSize / (float) $width;
        }

        $destination = \imagecreatetruecolor($newWidth, $newHeight);
        \imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        \imagedestroy($source);
        \imagejpeg($destination, $target, 80);
        \imagedestroy($destination);
    }

}