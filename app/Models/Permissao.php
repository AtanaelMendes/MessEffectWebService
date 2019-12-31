<?php
/**
 * Created by php artisan gerador:model.
 * Date: 31/Dec/2019 15:43:11
 */

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permissao extends BaseModel
{
    use SoftDeletes;

    protected $table = 'permissao';

    protected $fillable = [
        'codigo',
        'descricao',
        'nome'
    ];

    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'integer'
    ];

    public function acaoPermissao()
    {
        return $this->hasMany('App\Models\AcaoPermissao', 'permissao_id', 'id');
    }

}