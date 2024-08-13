<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Google_Client;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class FirebaseController extends Controller
{
    protected $auth;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(storage_path(env('FIREBASE_CREDENTIALS')));
        $this->auth = $factory->createAuth();
    }

    public function signInWithGoogle(Request $request)
    {
        $idToken = $request->input('idToken');

        try {
            $apiKey = env('FIREBASE_API_KEY'); // Your Firebase API Key
            $url = "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=" . $apiKey;

            // Send request to Firebase API
            $response = Http::post($url, [
                'idToken' => $idToken,
            ]);

            // Check if the request was successful
            if ($response->successful()) {
                $data = $response->json();
                $userFromFirebase = $data['users'][0];

                $userId = $userFromFirebase['localId'];
                $userEmail = $userFromFirebase['email'];
                $userName = $userFromFirebase['displayName'];
                $userAvatar = $userFromFirebase['photoUrl'];

                // Find or create the user in your database
                $user = User::firstOrCreate(
                    ['firebase_uid' => $userId], // Assuming you store Firebase user ID in 'firebase_uid'
                    [
                        'name' => $userName,
                        'email' => $userEmail,
                        'avatar' => $userAvatar,
                        'password' => bcrypt('default_password'), // Use a secure default password
                    ]
                );

                // Log the user in
                auth()->login($user);

                return response()->json([
                    'status' => 'success',
                    'user' => $user,
                ]);
            } else {
                // Token verification failed
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid ID token.',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
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
            $user = $this->auth->createUserWithEmailAndPassword($email, $password);

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
