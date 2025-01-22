<?php

namespace App\Models;

trait HasBalanceTrait {

    public function balance()
    {
        return $this->hasOne(Balance::class)->first();
    }

    public function incrementBalance($amount, $reason = '') {
        $userBalance = $this->balance();
        if (!$userBalance) {
            $userBalance = Balance::create([
                'user_id' => $this->id,
                'currency' => 'RUB',
                'amount' => $amount,
            ]);
        } else {
            $userBalance->increment('amount', $amount);
        }
        if ($reason) {
            $userBalance->update(['last_action' => $reason]);
        }
        return $userBalance;
    }
}
