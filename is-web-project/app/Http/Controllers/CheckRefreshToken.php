<?php

namespace App\Http\Controllers;

use App\Models\RefreshToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class CheckRefreshToken extends Controller
{

    public function handle(Request $request)
    {

        if ($request->header('Authorization')) {
            try {
                $user = JWTAuth::parseToken()->authenticate(false);

                if ($user) {
                    Auth::setUser($user);
                    $user->load('roles');

                    return response()->json([
                        'success' => true,
                        'message' => 'You are already logged in.',
                        'user' => $user,
                        'redirect' => '/',
                    ], 200);
                }
            } catch (JWTException $e) {

            }
        }


        $accessToken = $this->getRefreshToken($request);

        if ($accessToken !== null) {
            $user = JWTAuth::setToken($accessToken)->authenticate();
            if ($user) {
                $user->load('roles');
            }

            return response()->json([
                'success' => true,
                'access_token' => $accessToken,
                'user' => $user,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to refresh token.',
        ], 401);
    }


    private function getRefreshToken(Request $request)
    {
        $refreshToken = RefreshToken::with('user.roles')
            ->where('token', $request->header('refresh-token'))
            ->where('revoked', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$refreshToken || !$refreshToken->user) {
            return null;
        }

        return JWTAuth::fromUser($refreshToken->user);
    }
}
