<?php
/**
 * Created by php artisan gerador:model.
 * Date: 31/Dec/2019 15:42:22
 */

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioAcao extends BaseModel
{
    use SoftDeletes;

    protected $table = 'usuario_acao';

    protected $fillable = [
        'acao_id',
        'usuario_id'
    ];

    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at'
    ];

    protected $casts = [
        'acao_id' => 'integer',
        'id' => 'integer',
        'usuario_id' => 'integer'
    ];

    public function acao()
    {
        return $this->belongsTo('App\Models\Acao', 'acao_id', 'id')->withTrashed();
    }

    public function usuario()
    {
        return $this->belongsTo('App\Models\Usuario', 'usuario_id', 'id')->withTrashed();
    }

}