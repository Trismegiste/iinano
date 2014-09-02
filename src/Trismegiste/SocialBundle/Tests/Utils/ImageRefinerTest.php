<?php

/*
 * Iinano
 */

namespace Trismegiste\SocialBundle\Tests\Utils;

use Trismegiste\SocialBundle\Utils\ImageRefiner;

/**
 * ImageRefinerTest tests ImageRefiner
 */
class ImageRefinerTest extends \PHPUnit_Framework_TestCase
{

    protected $sut;

    protected function createTmpImage($w, $h)
    {
        $file = tempnam(sys_get_temp_dir(), 'img');
        $image = \imagecreatetruecolor($w, $h);
        \imagejpeg($image, $file);

        return $file;
    }

    protected function setUp()
    {
        $this->sut = new ImageRefiner();
    }

    public function getDimension()
    {
        return [
            [500, 500, 100, 100],
            [500, 250, 100, 50],
            [250, 500, 50, 100],
        ];
    }

    /**
     * @dataProvider getDimension
     */
    public function testResizeSquare($ow, $oh, $tw, $th)
    {
        $source = $this->createTmpImage($ow, $oh);
        $this->sut->makeThumbnailFrom($source, $source, 100);
        $info = getimagesize($source);

        $this->assertEquals($tw, $info[0]);
        $this->assertEquals($th, $info[1]);
    }

}