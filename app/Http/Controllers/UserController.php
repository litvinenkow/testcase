<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct()
    {
    }

    public function info(Request $request) {
        try {
            $user = $request->user();
            return response()->json(compact('user'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function balance(Request $request) {
        try {
            $balance = $request->user()->balance;
            return response()->json(compact('balance'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
