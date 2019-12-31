<?php
/**
 * Created by php artisan gerador:model.
 * Date: 31/Dec/2019 15:42:51
 */

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcaoPermissao extends BaseModel
{
    use SoftDeletes;

    protected $table = 'acao_permissao';

    protected $fillable = [
        'acao_id',
        'permissao_id'
    ];

    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at'
    ];

    protected $casts = [
        'acao_id' => 'integer',
        'id' => 'integer',
        'permissao_id' => 'integer'
    ];

    public function acao()
    {
        return $this->belongsTo('App\Models\Acao', 'acao_id', 'id')->withTrashed();
    }

    public function permissao()
    {
        return $this->belongsTo('App\Models\Permissao', 'permissao_id', 'id')->withTrashed();
    }

}