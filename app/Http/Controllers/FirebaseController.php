<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use Illuminate\Http\Request;

class FirebaseController extends Controller
{
    protected $auth;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(storage_path(env('FIREBASE_CREDENTIALS')));
        $this->auth = $factory->createAuth();
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        try {
            $user = $this->auth->createUserWithEmailAndPassword($email,$password);

            return response()->json([
                'status' => 'success',
                'user' => $user,
            ]);
        } catch (AuthException | FirebaseException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        try {
            $signInResult = $this->auth->signInWithEmailAndPassword($email, $password);
            $user = $signInResult->data();

            $idToken = $signInResult->idToken();

            return response()->json([
                'status' => 'success',
                'user' => $user,
                'idToken' => $idToken,
            ]);
        } catch (AuthException | FirebaseException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    public function me(Request $request)
    {
        $idToken = $request->bearerToken();

        try {
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            $uid = $verifiedIdToken->claims()->get('sub');
            $user = $this->auth->getUser($uid);

            return response()->json([
                'status' => 'success',
                'user' => $user,
            ]);
        } catch (AuthException | FirebaseException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 401);
        }
    }
}
