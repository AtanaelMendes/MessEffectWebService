<?php

namespace App;

use App\Models\BaseModel;
use Laravel\Passport\HasApiTokens;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use HasApiTokens, Authenticatable, Authorizable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'imagem_id',
    ];

    protected $hidden = [
        'password', 'pivot',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function image()
    {
        return $this->belongsTo('App\Models\Imagem');
    }

    public function roles(){
        return $this->belongsToMany(
            'App\Models\Acao', 'usuario_acao', 'usuario_id', 'acao_id'
        )->withTimestamps();
    }

    public function hasPermission($permissionCode){
        foreach ($this->roles()->get() as $role){
            foreach ($role->permissions()->get() as $permission){
                if($permission->code == $permissionCode){
                    return true;
                }
            }
        }
        return false;
    }
}
