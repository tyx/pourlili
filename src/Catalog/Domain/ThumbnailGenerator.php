<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

/**
 * source: https://gist.github.com/pqina/7a42bf0833d988dd81d3c9438009da21
 */
class ThumbnailGenerator
{
    private const IMAGE_HANDLERS = [
        IMAGETYPE_JPEG => [
            'load' => 'imagecreatefromjpeg',
            'save' => 'imagejpeg',
            'quality' => 100
        ],
        IMAGETYPE_PNG => [
            'load' => 'imagecreatefrompng',
            'save' => 'imagepng',
            'quality' => 0
        ],
        IMAGETYPE_GIF => [
            'load' => 'imagecreatefromgif',
            'save' => 'imagegif'
        ]
    ];

    /**
     * @param $src - a valid file location
     * @param $dest - a valid file target
     * @param $targetHeight - desired output height
     */
    function createThumbnail(string $src, string $dest, int $targetHeight)
    {
        $type = exif_imagetype($src);

        if (!$type || !self::IMAGE_HANDLERS[$type]) {
            return false;
        }

        $image = call_user_func(self::IMAGE_HANDLERS[$type]['load'], $src);

        if (!$image) {
            return false;
        }

        $width = imagesx($image); // 300 // 800
        $height = imagesy($image); // 600 // 600
        $ratio = $width / $height; // 0.5 // 1.33

        if ($height < $targetHeight) {
            return false;
        }

        $targetWidth = (int) floor($targetHeight * $ratio);
        $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

        // set transparency options for GIFs and PNGs
        if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {
            imagecolortransparent(
                $thumbnail,
                imagecolorallocate($thumbnail, 0, 0, 0)
            );

            if ($type == IMAGETYPE_PNG) {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
            }
        }

        imagecopyresampled(
            $thumbnail,
            $image,
            0, 0, 0, 0,
            $targetWidth, $targetHeight,
            $width, $height
        );

        return call_user_func(
            self::IMAGE_HANDLERS[$type]['save'],
            $thumbnail,
            $dest,
            self::IMAGE_HANDLERS[$type]['quality']
        );
    }
}
