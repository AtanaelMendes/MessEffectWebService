<?php
/**
 * Created by php artisan gerador:model.
 * Date: 31/Dec/2019 15:29:11
 */

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pessoa extends BaseModel
{
    use SoftDeletes;

    protected $table = 'pessoa';

    protected $fillable = [
        'cnpj',
        'contato_id',
        'cpf',
        'endereco_id',
        'nome',
        'usuario_id'
    ];

    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at'
    ];

    protected $casts = [
        'contato_id' => 'integer',
        'endereco_id' => 'integer',
        'id' => 'integer',
        'usuario_id' => 'integer'
    ];

    public function conferencia()
    {
        return $this->hasMany('App\Models\Conferencia', 'pessoa_id', 'id');
    }

    public function estoqueLocal()
    {
        return $this->hasMany('App\Models\EstoqueLocal', 'pessoa_id', 'id');
    }

    public function historicoCompra()
    {
        return $this->hasMany('App\Models\HistoricoCompra', 'pessoa_id', 'id');
    }

    public function locacoes()
    {
        return $this->hasMany('App\Models\Locacoes', 'pessoa_id', 'id');
    }

    public function materialUtilizado()
    {
        return $this->hasMany('App\Models\MaterialUtilizado', 'pessoa_id', 'id');
    }

    public function ordemCompra()
    {
        return $this->hasMany('App\Models\OrdemCompra', 'pessoa_id', 'id');
    }

    public function usuario()
    {
        return $this->hasMany('App\Models\Usuario', 'pessoa_id', 'id');
    }

}