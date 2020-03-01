<?php

namespace App\Repository;

use App\Models\Acao;

class RoleRepository extends BaseRepository
{
    protected $model;

    public function __construct(Acao $role)
    {
        $this->model = $role;
    }
}
