<?php


namespace App\DTOs;

use Illuminate\Support\Carbon;
use JsonSerializable;

class BaseDTO implements JsonSerializable
{

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @param Carbon|null $date
     * @return null|string
     */
    public function formatDateTime(Carbon $date = null){
        if($date){
            return $date->toDateTimeString();
        }
        return null;
    }
}
