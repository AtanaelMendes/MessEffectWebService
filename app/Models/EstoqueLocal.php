<?php
/**
 * Created by php artisan gerador:model.
 * Date: 31/Dec/2019 15:37:02
 */

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstoqueLocal extends BaseModel
{
    use SoftDeletes;

    protected $table = 'estoque_local';

    protected $fillable = [
        'codigo',
        'nome',
        'pessoa_id'
    ];

    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'integer',
        'pessoa_id' => 'integer'
    ];

    public function pessoa()
    {
        return $this->belongsTo('App\Models\Pessoa', 'pessoa_id', 'id')->withTrashed();
    }

    public function conferencia()
    {
        return $this->hasMany('App\Models\Conferencia', 'estoque_local_id', 'id');
    }

    public function locacoes()
    {
        return $this->hasMany('App\Models\Locacoes', 'estoque_local_id', 'id');
    }

    public function material()
    {
        return $this->hasMany('App\Models\Material', 'estoque_local_id', 'id');
    }

    public function ordemCompra()
    {
        return $this->hasMany('App\Models\OrdemCompra', 'estoque_local_id', 'id');
    }

}