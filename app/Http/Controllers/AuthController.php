<?php

namespace App\Http\Controllers;

use App\Auth\Jwt;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {

    public function __construct(protected Jwt $jwt)
    {
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $token = $this->jwt->fromUser($user);

        return response()->json(compact('user','token'), 201);
    }

    public function login(LoginRequest $request) {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $token = $this->jwt->fromUser($user);

            if (!$token) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            return response()->json(compact('token'));
        } catch (Exception $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
    }

    public function logout(Request $request) {

        $this->jwt->setToken($this->jwt->getTokenFromRequest($request))->invalidate();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
