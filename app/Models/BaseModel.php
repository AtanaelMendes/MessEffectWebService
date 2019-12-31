<?php
/**
 * Created by PhpStorm.
 * User: Danilo O. Lima <danilo__oliveira@hotmail.com>
 * Date: 01/12/18
 * Time: 09:37
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    public static function boot()
    {
        parent::boot();
    }

    /**
     * Converte Datas para Carbon usando o Carbon::parse
     */
    public function setAttribute($name, $value)
    {
        if (in_array($name, $this->dates) && is_string($value)) {
            $value = \Carbon\Carbon::parse($value)->timezone(date_default_timezone_get());
        }
        parent::setAttribute($name, $value);
    }

}
