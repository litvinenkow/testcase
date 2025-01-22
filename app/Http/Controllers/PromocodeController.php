<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromocodeRequest;
use App\Http\Resources\PromocodeResource;
use App\Models\Promocode;
use App\Services\PromocodeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromocodeController extends Controller
{

    public function __construct(private PromocodeService $promocodeService)
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
                throw new Exception('Promocode "'.$promocode.'" not found!');
            }

            $useCount = $this->promocodeService->getPromocodeUseCountOrFail($promocode, $request->user());

            DB::beginTransaction();

            $request->user->promocodes()->sync([$promocode->id => ['use_count' => $useCount]], false);
            $balance = $request->incrementBalance($promocode->amount, 'attach promocode '.$promocode->promocode);

            DB::commit();

            return response()->json(['message' => 'Promocode succesfully activated', 'new_balance' => $balance->amount]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
