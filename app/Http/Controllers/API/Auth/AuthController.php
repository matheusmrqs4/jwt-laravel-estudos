<?php

namespace App\Http\Controllers\API\Auth;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            abort(403, "Invalid Login");
        }

        return response()
                ->json([
                   'data' => [
                        'msg' => 'Login Successful',
                        'token' => $token
                   ]
                ]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        Auth::guard('api')->logout();

        return response()
                ->json([
                    'message' => 'Logout Successful'
                ]);
    }

    public function refresh()
    {
        $token = auth('api')->refresh();

        return response()
                ->json([
                    'data' => [
                        'msg' => 'Successful',
                        'token' => $token
                    ]
                ]);
    }

    public function me()
    {
        return response()
                ->json(auth()
                ->user());
    }
}
