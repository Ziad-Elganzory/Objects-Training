<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SanctumAuthController extends Controller
{
    use ApiResponse;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['login','register']]);
    }

    /**
     * Create a new user
     */
    public function register(Request $request)
    {
        try {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password'))
            ]);

            $user->token = $user->createToken('Personal access Token')->plainTextToken;

            return $this->successResponse($user, 'User Created Successfully');
        } catch (Exception $e) {
            return $this->errorResponse('User Registration Failed', 500, $e->getMessage());
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!$token = auth()->attempt($credentials)) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $user = User::where('email',$request->email)->first();
            $user->token = $user->createToken('Personal access Token')->plainTextToken;

            return $this->successResponse($user,'Successfull Login');
        } catch (Exception $e) {
            return $this->errorResponse('Login Failed', 500, $e->getMessage());
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function me()
    {
        try {
            return $this->successResponse(auth()->user(), 'User Data Retrieved Successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to Retrieve User Data', 500, $e->getMessage());
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $user = auth()->user();
            $request->user()->currentAccessToken()->delete();
            return $this->successResponse($user, 'Successfully logged out');
        } catch (Exception $e) {
            return $this->errorResponse('Logout Failed', 500, $e->getMessage());
        }
    }



}
