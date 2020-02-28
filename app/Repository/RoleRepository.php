<?php

namespace App\Repositories;

use App\Models\Acao;
use Illuminate\Database\Eloquent\Model;

class RoleRepository extends BaseRepository
{
    protected $model;

    public function __construct(Acao $role)
    {
        $this->model = $role;
    }
}
