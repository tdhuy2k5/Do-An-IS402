<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\RefreshTokenRepository;
use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\Google;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class GoogleController extends Controller
{
    private $refreshTokenRepository;

    public function __construct(RefreshTokenRepository $refreshTokenRepository)
    {
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    public function exchange(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string',
            ]);
            $provider = new Google([
                'clientId' => env('GOOGLE_CLIENT_ID'),
                'clientSecret' => env('GOOGLE_CLIENT_SECRET', ''),
                'redirectUri' => env('GOOGLE_REDIRECT_URI'),
            ]);


            $token = $provider->getAccessToken('authorization_code', [
                'code' => $validated['code'],
            ]);


            $gg_user = $provider->getResourceOwner($token)->toArray();

            if (empty($gg_user['email'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Email not provided by Google',
                ], 400);
            }


            $user = User::firstOrCreate([
                'email' => $gg_user['email'],
            ], [
                'email_verified' => true,
                'full_name' => $gg_user['name'] ?? $gg_user['email'],
            ]);


            if (! $user->roles()->count()) {
                $user->roles()->attach('member');
            }


            $jwtToken = JWTAuth::fromUser($user);
            $refreshToken = $this->refreshTokenRepository->generateRefreshTokenForUser($user);


            $user->load('roles');

            return response()->json([
                'success' => true,
                'access_token' => $jwtToken,
                'refresh_token' => $refreshToken,
                'user' => $user,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'details' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'An error occurred during Google authentication',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
