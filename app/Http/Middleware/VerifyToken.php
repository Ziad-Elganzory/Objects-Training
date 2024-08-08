<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerifyToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            // var_dump($user);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid', 'message' => $e->getMessage()], 401);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token has expired', 'message' => $e->getMessage()], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is missing', 'message' => $e->getMessage()], 401);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong', 'message' => $e->getMessage()], 500);
        }

        return $next($request);
    }
}
