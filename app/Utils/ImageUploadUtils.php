<?php


namespace App\Utils;


class ImageUploadUtils
{
    public const base_dir = 'images/';
    public const dir_originals = self::base_dir.'originals/';
    public const dir_800x500 = self::base_dir.'800x500/';
    public const dir_500x500 = self::base_dir.'500x500/';
    public const dir_400x250 = self::base_dir.'400x250/';
    public const dir_250x250 = self::base_dir.'250x250/';
    public const dir_200x125 = self::base_dir.'200x125/';
    public const dir_125x125 = self::base_dir.'125x125/';

    public static function createDirectories()
    {

        $dirs = [
            self::base_dir,
            self::dir_originals,
            self::dir_800x500,
            self::dir_500x500,
            self::dir_400x250,
            self::dir_250x250,
            self::dir_200x125,
            self::dir_125x125
        ];

        foreach ($dirs as $dir){
            if (!file_exists(self::publicPath() . $dir)) {
                mkdir(self::publicPath() . $dir, 0775);
            }
        }

    }

    public static function getImagePath($image, $dir){
        if($image){
            return url($dir . $image->file_name);
        }
        return null;
    }

    private static function publicPath()
    {
        return app()->basePath('public') . '/';
    }

}
