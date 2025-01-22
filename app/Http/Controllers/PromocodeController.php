<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromocodeRequest;
use App\Http\Resources\PromocodeResource;
use App\Models\Promocode;
use Exception;
use Illuminate\Http\Request;

class PromocodeController extends Controller
{

    public function __construct()
    {
    }

    public function index() {
        $promocodes = Promocode::all()->filter(function($item) {
            return $item->isValid;
        });
        return PromocodeResource::collection($promocodes);
    }

    public function store(PromocodeRequest $request) {
        $promocode = Promocode::create($request->validated());
        return new PromocodeResource($promocode);
    }

    public function use(Request $request) {
        try {
            if (empty($request->promocode)) {
                throw new Exception('Please enter promocode!');
            }

            $promocode = Promocode::where('promocode', $request->promocode)->first();

            if (!$promocode) {
                throw new Exception('Promocode "'.$request->promocode.'" not found!');
            }

            if (!$promocode->isValid) {
                throw new Exception('Promocode "'.$promocode->promocode.'" is expired!');
            }

            if ($promocode->isMaxUsed) {
                throw new Exception('Promocode "'.$promocode->promocode.'" cannot be used more!');
            }

            $user = $request->user();

            $usedPromocode = $user->promocodes->first(function($item) use ($promocode) {
                return $item->id === $promocode->id;
            });

            if ($usedPromocode && !is_null($promocode->use_max) && $usedPromocode->pivot->use_count === $promocode->use_max) {
                throw new Exception('Promocode "'.$promocode->promocode.'" cannot be used more for that user!');
            }

            $use_count = 1;
            if ($usedPromocode) {
                $use_count = $usedPromocode->pivot->use_count + 1;
            }
            $user->promocodes()->sync([$promocode->id => ['use_count' => $use_count]], false);
            $balance = $user->incrementBalance($promocode->amount, 'attach promocode '.$promocode->promocode);

            return response()->json(['message' => 'Promocode succesfully activated', 'new_balance' => $balance->amount]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

    }


}
