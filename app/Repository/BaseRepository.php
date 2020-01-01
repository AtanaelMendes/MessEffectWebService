<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 22/09/18
 * Time: 10:16
 */

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function save(Model $model)
    {
        $model->save();

        return $model;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * @param array $attributes
     * @param int $id
     * @param bool $withTrashed
     * @return bool
     */
    public function update(array $attributes, int $id, bool $withTrashed = false) : bool
    {
        if($withTrashed){
            return $this->model->withTrashed()->find($id)->update($attributes);
        }
        return $this->model->find($id)->update($attributes);
    }

    /**
     * @param bool $withTrashed
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return mixed
     */
    public function all(bool $withTrashed = false, $columns = array('*'), string $orderBy = 'created_at', string $sortBy = 'desc')
    {
        if($withTrashed){
            return $this->model->withTrashed()->orderBy($orderBy, $sortBy)->get($columns);
        }
        return $this->model->orderBy($orderBy, $sortBy)->get($columns);
    }

    /**
     * @param int $id
     * @param bool $withTrashed
     * @return mixed
     */
    public function find(int $id, bool $withTrashed = false)
    {
        if($withTrashed){
            return $this->model->withTrashed()->find($id);
        }
        return $this->model->find($id);
    }

    /**
     * @param int $id
     * @param bool $withTrashed
     * @return mixed
     */
    public function findOneOrFail(int $id, bool $withTrashed = false)
    {
        if($withTrashed){
            return $this->model->withTrashed()->findOrFail($id);
        }
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @param bool $withTrashed
     * @return mixed
     */
    public function findBy(array $data, bool $withTrashed = false)
    {
        if($withTrashed){
            return $this->model->withTrashed()->orderBy('created_at', 'desc')->where($data)->get();
        }
        return $this->model->where($data)->orderBy('created_at', 'desc')->get();
    }

    /**
     * @param array $data
     * @param bool $withTrashed
     * @return mixed
     */
    public function findOneBy(array $data, bool $withTrashed = false)
    {
        if($withTrashed){
            return $this->model->withTrashed()->where($data)->first();
        }
        return $this->model->where($data)->first();
    }

    /**
     * @param array $data
     * @param bool $withTrashed
     * @return mixed
     */
    public function findOneByOrFail(array $data, bool $withTrashed = false)
    {
        if($withTrashed){
            return $this->model->withTrashed()->where($data)->firstOrFail();
        }
        return $this->model->where($data)->firstOrFail();
    }

    /**
     * @param array $data
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateArrayResults(array $data, int $perPage = 50)
    {
        $page = request()->get('page', 1);
        $offset = ($page * $perPage) - $perPage;
        return new LengthAwarePaginator(
            array_slice($data, $offset, $perPage, false),
            count($data),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query()
            ]
        );
    }

    /**
     * @param int $id
     * @param bool $withTrashed
     * @return bool
     */
    public function delete(int $id, bool $withTrashed = false) : bool
    {
        if($withTrashed){
            return $this->model->withTrashed()->findOrFail($id)->delete();
        }
        return $this->model->findOrFail($id)->delete();
    }

    /**
     * @param int $id
     * @param bool $withTrashed
     * @return bool
     */
    public function forceDelete(int $id, bool $withTrashed = false) : bool
    {
        if($withTrashed){
            return $this->model->withTrashed()->findOrFail($id)->forceDelete();
        }
        return $this->model->find($id)->forceDelete();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function restore(int $id) : bool
    {
        return $this->model->withTrashed()->findOrFail($id)->restore();
    }

    /**
     * @param $relations
     * @return \Illuminate\Database\Eloquent\Builder|Model
     */
    public function with($relations)
    {
        return $this->model->with($relations);
    }
}
