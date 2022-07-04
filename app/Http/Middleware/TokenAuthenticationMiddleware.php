<?php

namespace App\Http\Middleware;

use App\Exceptions\WhiteHouse;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class TokenAuthenticationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     *
     * @return JsonResponse|RedirectResponse|Response
     */
    public function handle(Request $request, Closure $next): Response|JsonResponse|RedirectResponse
    {
        $accountId = Route::current()->parameter('account_id');
        $account   = DB::selectOne(DB::raw("SELECT * FROM `accounts` WHERE `id` = {$accountId}"));

        if (is_null($account)) {
            return WhiteHouse::generalResponse(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid User');
        }


        $token   = request()->bearerToken();
        if (is_null($token)){
            return WhiteHouse::generalResponse(Response::HTTP_UNAUTHORIZED, 'Unauthenticated');
        }

        $payload = JWT::decode($token, new Key(config('app.token_secret_key'), 'HS256'));
        if ((!property_exists($payload, 'sub')) or ($payload->account_id != $accountId)) {
           return WhiteHouse::generalResponse(Response::HTTP_UNAUTHORIZED, 'Unauthenticated');
        }

        return $next($request);
    }
}
