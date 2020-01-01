<?php
/**
 * Created by PhpStorm.
 * User: danilo
 * Date: 10/03/19
 * Time: 14:10
 */

namespace App\Resources;


use Carbon\Carbon;

interface SynchronizableResource
{
    public function getResourceAfter(Carbon $after, int $pessoaId);

    public function hasResourceAfter(Carbon $after, int $pessoaId);
}
