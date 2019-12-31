<?php
/**
 * Created by php artisan gerador:model.
 * Date: 31/Dec/2019 15:36:47
 */

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conferencia extends BaseModel
{
    use SoftDeletes;

    protected $table = 'conferencia';

    protected $fillable = [
        'estoque_local_id',
        'material_id',
        'pessoa_id',
        'quantidade_anterior',
        'quantidade_informada',
        'valor_anterior',
        'valor_informado'
    ];

    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at'
    ];

    protected $casts = [
        'estoque_local_id' => 'integer',
        'id' => 'integer',
        'material_id' => 'integer',
        'pessoa_id' => 'integer',
        'quantidade_anterior' => 'integer',
        'quantidade_informada' => 'integer',
        'valor_anterior' => 'float',
        'valor_informado' => 'float'
    ];

    public function estoqueLocal()
    {
        return $this->belongsTo('App\Models\EstoqueLocal', 'estoque_local_id', 'id')->withTrashed();
    }

    public function material()
    {
        return $this->belongsTo('App\Models\Material', 'material_id', 'id')->withTrashed();
    }

    public function pessoa()
    {
        return $this->belongsTo('App\Models\Pessoa', 'pessoa_id', 'id')->withTrashed();
    }

}