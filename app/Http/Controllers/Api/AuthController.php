<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Mobile API authentication using Laravel Sanctum personal access tokens.
 *
 * Mapping notes:
 * - The web login route (routes/web.php) uses session auth via Auth::attempt()
 *   and writes a login_history row. We keep that same intent here but issue a
 *   Sanctum token instead of a session, since mobile apps are stateless.
 * - Riders and admin/staff both authenticate through this single endpoint;
 *   the response tells the client which "area" (rider vs admin/staff) the
 *   user belongs to, mirroring the isRider redirect branch in web.php.
 */
class AuthController extends Controller
{
    /**
     * POST /api/v1/auth/login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ])) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        $user = Auth::user();

        // Same login-history bookkeeping the web scaffold performs.
        $user->loginHistory()->create([
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'logged_in_at' => now(),
        ]);

        $isRider = $user->hasRole('rider') || $user->rider()->exists();

        $token = $user->createToken(
            $request->input('device_name', 'mobile-app')
        )->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'area' => $isRider ? 'rider' : 'admin',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'branch_id' => $user->branch_id,
                    'roles' => $user->getRoleNames(),
                    'is_rider' => $isRider,
                ],
            ],
        ], 200);
    }


    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
        ]);

        $user->loginHistory()->create([
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'logged_in_at' => now(),
        ]);

        $token = $user->createToken(
            $request->input('device_name', 'mobile-app')
        )->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registered successfully',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'area' => 'customer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ],
        ], 201);
    }


    /**
     * GET /api/v1/auth/me
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Current user fetched successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'branch_id' => $user->branch_id,
                'roles' => $user->getRoleNames(),
                'is_rider' => $user->hasRole('rider') || $user->rider()->exists(),
            ],
        ], 200);
    }

    /**
     * POST /api/v1/auth/logout
     * Revokes only the token used for this request (keeps other devices logged in).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
            'data' => null,
        ], 200);
    }

    /**
     * POST /api/v1/auth/logout-all
     */
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices',
            'data' => null,
        ], 200);
    }
}
