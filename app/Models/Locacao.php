<?php
/**
 * Created by php artisan gerador:model.
 * Date: 31/Dec/2019 15:41:49
 */

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Locacao extends BaseModel
{
    use SoftDeletes;

    protected $table = 'locacao';

    protected $fillable = [
        'data_devolucao',
        'data_locacao',
        'estoque_local_id',
        'is_diaria',
        'is_mensal',
        'is_semanal',
        'nome',
        'pessoa_id',
        'quantidade',
        'total_aluguel',
        'valor_aluguel'
    ];

    protected $dates = [
        'created_at',
        'data_devolucao',
        'data_locacao',
        'deleted_at',
        'updated_at'
    ];

    protected $casts = [
        'estoque_local_id' => 'integer',
        'id' => 'integer',
        'is_diaria' => 'boolean',
        'is_mensal' => 'boolean',
        'is_semanal' => 'boolean',
        'pessoa_id' => 'integer',
        'quantidade' => 'integer',
        'total_aluguel' => 'float',
        'valor_aluguel' => 'float'
    ];

    public function estoqueLocal()
    {
        return $this->belongsTo('App\Models\EstoqueLocal', 'estoque_local_id', 'id')->withTrashed();
    }

    public function pessoa()
    {
        return $this->belongsTo('App\Models\Pessoa', 'pessoa_id', 'id')->withTrashed();
    }

    public function imagem()
    {
        return $this->hasMany('App\Models\Imagem', 'locacao_id', 'id');
    }

}