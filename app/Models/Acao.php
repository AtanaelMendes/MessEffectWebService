<?php
/**
 * Created by php artisan gerador:model.
 * Date: 31/Dec/2019 15:42:37
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Acao extends BaseModel
{
    use SoftDeletes;

    protected $table = 'acao';

    protected $fillable = [
        'codigo',
        'descricao',
        'nome'
    ];

    protected $hidden = [
        'pivot'
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
        return $this->hasMany('App\Models\AcaoPermissao', 'acao_id', 'id');
    }

    public function usuarioAcao()
    {
        return $this->hasMany('App\Models\UsuarioAcao', 'acao_id', 'id');
    }

    public function permissions(){
        return $this->belongsToMany(
            'App\Models\Permissao', 'acao_permissao', 'acao_id', 'permissao_id'
        )->withTimestamps();
    }

}
