<?php
/**
 * Created by php artisan gerador:model.
 * Date: 31/Dec/2019 15:27:53
 */

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Imagem extends BaseModel
{
    use SoftDeletes;

    protected $table = 'imagem';

    protected $fillable = [
        'locacao_id',
        'marca_id',
        'material_id',
        'nome'
    ];

    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'integer',
        'locacao_id' => 'integer',
        'marca_id' => 'integer',
        'material_id' => 'integer'
    ];

    public function locacao()
    {
        return $this->belongsTo('App\Models\Locacoes', 'locacao_id', 'id')->withTrashed();
    }

    public function marca()
    {
        return $this->belongsTo('App\Models\Marca', 'marca_id', 'id')->withTrashed();
    }

    public function material()
    {
        return $this->belongsTo('App\Models\Material', 'material_id', 'id')->withTrashed();
    }

    public function usuario()
    {
        return $this->hasMany('App\Models\User', 'imagem_id', 'id');
    }

}
