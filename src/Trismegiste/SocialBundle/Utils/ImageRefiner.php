<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Utils;

/**
 * ImageRefiner is a simple service for resizing, reducing weight of image,
 * thumbnailing...
 * 
 * Require: GD2
 */
class ImageRefiner
{

    /**
     * Resize an image to make a thumbnail
     * 
     * @param string $filename absolute path to image
     * @param string $target absolute path to image
     * @param int $maxBoxSize the square box size which the thumbnail must fit in
     */
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

    /**
     * Resize and crop an image to make a square thumbnail
     * 
     * @param string $filename absolute path to image
     * @param string $target absolute path to image
     * @param int $maxBoxSize the square box size which the thumbnail must fit in
     */
    public function makeSquareThumbnailFrom($filename, $target, $maxBoxSize)
    {
        
    }

}