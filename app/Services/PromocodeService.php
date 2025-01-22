<?php

namespace App\Services;

use App\Models\Promocode;
use App\Models\User;
use Exception;

class PromocodeService {

    /**
     * @throws Exception
     */
    public function getPromocodeUseCountOrFail(Promocode $promocode, User $user) {
        if (!$promocode->isValid) {
            throw new Exception('Promocode "'.$promocode.'" is expired!');
        }

        if ($promocode->isMaxUsed) {
            throw new Exception('Promocode "'.$promocode.'" cannot be used more!');
        }

        $usedPromocode = $user->promocodes->first(function($item) use ($promocode) {
            return $item->id === $promocode->id;
        });

        if (!is_null($promocode->use_max) && !$usedPromocode && $usedPromocode->pivot->use_count === $promocode->use_max) {
            throw new Exception('Promocode "'.$promocode->promocode.'" cannot be used more for that user!');
        }

        $use_count = 1;
        if ($usedPromocode) {
            $use_count = $usedPromocode->pivot->use_count + 1;
        }

        return $use_count;
    }
}
