<?php


namespace App\Repository;

use Carbon\Carbon;
use App\Models\Imagem;

class ImageRepository extends BaseRepository
{
    protected $model;

    public function __construct(Imagem $image)
    {
        $this->model = $image;
    }

    public function getAllByUSerId($userId)
    {
        return parent::findBy(['user_id' => $userId], true);
    }

    public function getResourceAfter(Carbon $after, int $userId)
    {
        return Imagem::where(['user_id', '=', $userId, 'updated_at', '>', $after])->get();
    }

    public function hasResourceAfter(Carbon $after, int $userId)
    {
        return Imagem::where(['user_id', '=', $userId], 'updated_at', '>', $after)->get()->isNotEmpty();
    }
}
