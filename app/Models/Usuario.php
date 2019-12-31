<?php
/**
 * Created by php artisan gerador:model.
 * Date: 31/Dec/2019 15:43:29
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Laravel\Passport\HasApiTokens;

class Usuario extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use SoftDeletes, HasApiTokens, Authenticatable, Authorizable;

    protected $table = 'usuario';

    protected $fillable = [
        'email',
        'imagem_id',
        'nome',
        'password',
        'pessoa_id'
    ];

    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'integer',
        'imagem_id' => 'integer',
        'pessoa_id' => 'integer'
    ];

    public function imagem()
    {
        return $this->belongsTo('App\Models\Imagem', 'imagem_id', 'id')->withTrashed();
    }

    public function pessoa()
    {
        return $this->belongsTo('App\Models\Pessoa', 'pessoa_id', 'id')->withTrashed();
    }

    public function usuarioAcao(){
        return $this->belongsToMany(
            'App\Models\UsuarioAcao', 'usuario_acao', 'usuario_id', 'acao_id'
        )->withTimestamps();
    }

    public function hasPermission($codigoPermissao){
        foreach ($this->usuarioAcao()->get() as $acao){
            foreach ($acao->permissao()->get() as $permissao){
                if($permissao->code == $codigoPermissao){
                    return true;
                }
            }
        }
        return false;
    }

}
