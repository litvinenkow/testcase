<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPromocodeTrait {

    public function promocodes(): BelongsToMany
    {
        return $this->belongsToMany(Promocode::class)->withPivot('use_count');
    }
}
