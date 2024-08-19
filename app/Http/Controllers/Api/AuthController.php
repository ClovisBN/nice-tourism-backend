<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role_id' => 2,  // Attribue le rôle "user" par défaut (ID 2)
        ]);

        $token = JWTAuth::fromUser($user);

        $accessToken = $user->createToken('access_token', ['access-api'], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
        $refreshToken = $user->createToken('refresh_token', ['issue-access-token'], Carbon::now()->addMinutes(config('sanctum.rt_expiration')));

        return response()->json([
            'jwt_token' => $token,
            'access_token' => $accessToken->plainTextToken,
            'access_token_expiration' => config('sanctum.ac_expiration'), 
            'refresh_token' => $refreshToken->plainTextToken,
            'refresh_token_expiration' => config('sanctum.rt_expiration'),
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        
        Log::info('Attempting login', ['email' => $credentials['email']]);
    
        if (!$token = JWTAuth::attempt($credentials)) {
            Log::warning('Login failed: Invalid credentials', ['email' => $credentials['email']]);
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    
        // Récupérer l'utilisateur authentifié
        $user = auth()->user();
    
        if (!$user) {
            Log::error('Authenticated user not found');
            return response()->json(['message' => 'User not found after authentication'], 500);
        }
    
        $accessToken = $user->createToken('access_token', ['access-api'], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
        $refreshToken = $user->createToken('refresh_token', ['issue-access-token'], Carbon::now()->addMinutes(config('sanctum.rt_expiration')));
    
        return response()->json([
            'jwt_token' => $token,
            'access_token' => $accessToken->plainTextToken,
            'access_token_expiration' => config('sanctum.ac_expiration'),
            'refresh_token' => $refreshToken->plainTextToken,
            'refresh_token_expiration' => config('sanctum.rt_expiration'),
        ]);
        
    }     
    
    public function refreshToken(Request $request)
    {
        $user = $request->user();
        $accessToken = $user->createToken('access_token', ['access-api'], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));

        $cookieAccessToken = cookie('access_token', $accessToken->plainTextToken, config('sanctum.ac_expiration')); // In minutes

        return response()->json([
            'access_token' => $accessToken->plainTextToken,
            'access_token_expiration' => config('sanctum.ac_expiration'),
        ])->withCookie($cookieAccessToken);
    }

    public function listUsers()
    {
        $users = User::all();
        return response()->json($users);
    }
}
