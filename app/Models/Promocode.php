<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Promocode extends Model
{
    protected $guarded = [];

    public function getIsValidAttribute() {
        return (is_null($this->valid_till)) || (!is_null($this->valid_till) && Carbon::parse($this->valid_till)->gte(now()));
    }

    public function getIsMaxUsedAttribute() {
        if (is_null($this->use_count)) {
            return false;
        }
        $totalUseCount = PromocodeUser::query()->where('promocode_id', $this->id)->sum('use_count');
        return $totalUseCount >= $this->use_count;
    }
}
