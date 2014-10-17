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

    const DEFAULT_COMPRESSION = 80;

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

        $mostLarge = max([$width, $height]);
        $newHeight = $height * $maxBoxSize / (float) $mostLarge;
        $newWidth = $width * $maxBoxSize / (float) $mostLarge;

        $destination = \imagecreatetruecolor($newWidth, $newHeight);
        \imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        \imagedestroy($source);
        \imagejpeg($destination, $target, self::DEFAULT_COMPRESSION);
        \imagedestroy($destination);
    }

    /**
     * Resize and crop an image to make a square thumbnail
     *
     * @param resource $imageResource a GD image resource
     * @param string $target absolute path to destination jpg
     * @param int $maxBoxSize the square box size which the thumbnail must fit in
     */
    public function makeSquareThumbnailFrom($imageResource, $target, $maxBoxSize)
    {
        $width = \imagesx($imageResource);
        $height = \imagesy($imageResource);

        if (min([$width, $height]) < $maxBoxSize) {
            throw new \InvalidArgumentException("Image is too small to fit as an avatar");
        }

        $needResize = (($width !== $maxBoxSize) || ($height !== $maxBoxSize));

        if ($needResize) {
            $lessLarge = min([$width, $height]);
            $deltaX = ($width - $lessLarge) / 2.0;
            $deltaY = ($height - $lessLarge) / 2.0;

            $destination = \imagecreatetruecolor($maxBoxSize, $maxBoxSize);
            \imagecopyresampled($destination, $imageResource, 0, 0, $deltaX, $deltaY, $maxBoxSize, $maxBoxSize, $lessLarge, $lessLarge);
            \imagedestroy($imageResource);
        } else {
            $destination = $imageResource;
        }

        \imagejpeg($destination, $target, self::DEFAULT_COMPRESSION);
        \imagedestroy($destination);
    }

    public function resizeToFit($source, $target, $maxBoxSize)
    {
        $this->transform($source, $target, function($input) use ($maxBoxSize) {
            $width = \imagesx($input);
            $height = \imagesy($input);

            $mostLarge = max([$width, $height]);
            $newHeight = $height * $maxBoxSize / (float) $mostLarge;
            $newWidth = $width * $maxBoxSize / (float) $mostLarge;

            $destination = \imagecreatetruecolor($newWidth, $newHeight);
            \imagecopyresampled($destination, $input, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            return $destination;
        });
    }

    protected function transform($source, $target, callable $filter)
    {
        $info = \getimagesize($source);
        $type = $info[2];

        // open
        switch ($type) {
            case IMAGETYPE_GIF:
                $input = \imagecreatefromgif($source);
                break;
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
                $input = \imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $input = \imagecreatefrompng($source);
                break;
            default:
                throw new \InvalidArgumentException("Image format not supported");
        }

        // apply filter
        $output = call_user_func($filter, $input);
        \imagedestroy($input);

        // save
        switch ($type) {
            case IMAGETYPE_GIF:
                \imagegif($output, $target);
                break;
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
                \imagejpeg($output, $target, self::DEFAULT_COMPRESSION);
                break;
            case IMAGETYPE_PNG:
                \imagepng($output, $target);
                break;
        }

        \imagedestroy($output);
    }

}
