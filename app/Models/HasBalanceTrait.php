<?php

namespace App\Models;

trait HasBalanceTrait {

    public function balance()
    {
        return $this->hasOne(Balance::class)->firstOrCreate([
            'user_id' => $this->id,
            'currency' => 'RUB',
            'amount' => 0,
        ]);
    }

    public function incrementBalance($amount, $reason = '') {
        $userBalance = $this->balance();
        $userBalance->update([
            'amount' => $amount,
            'last_action' => $reason,
        ]);
        return $userBalance;
    }
}
