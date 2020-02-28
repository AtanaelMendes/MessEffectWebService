<?php


namespace App\Services;

use Illuminate\Http\Request;
use App\Utils\ImageUploadUtils;
use App\Repository\ImageRepository;
use Intervention\Image\Facades\Image;


class ImageService
{
    private $imageRepository;

    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function save(Request $request, $userId = null)
    {
        ImageUploadUtils::createDirectories();

        $image = $request->file('image');
        $image_name = time().'.jpeg';

        //ORIGINAL
        $original_img = Image::make($image->getRealPath());
        $original_img->encode('jpg');
        $original_img->save(ImageUploadUtils::dir_originals.$image_name, 80);

        //800x500
        $image = Image::make($original_img->basePath())->widen(800);
        $image->save(ImageUploadUtils::dir_800x500.$image_name);

        //500x500
        $image = Image::make($original_img->basePath())->heighten(500)->crop(500, 500);
        $image->save(ImageUploadUtils::dir_500x500.$image_name);

        //400x250
        $image = Image::make($original_img->basePath())->widen(400);
        $image->save(ImageUploadUtils::dir_400x250.$image_name);

        //250x250
        $image = Image::make($original_img->basePath())->heighten(250)->crop(250, 250);
        $image->save(ImageUploadUtils::dir_250x250.$image_name);

        //200x125
        $image = Image::make($original_img->basePath())->widen(200);
        $image->save(ImageUploadUtils::dir_200x125.$image_name);

        //125x125
        $image = Image::make($original_img->basePath())->heighten(125)->crop(125, 125);
        $image->save(ImageUploadUtils::dir_125x125.$image_name);


        return $this->imageRepository->create(['file_name' => $image_name, 'user_id' => $userId]);

    }
}
