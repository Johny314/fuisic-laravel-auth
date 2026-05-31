<?php

namespace Fuisic\Auth\Http\Controllers;

use Fuisic\Auth\Services\AuthTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LogoutController extends Controller
{
    public function __invoke(Request $request, AuthTokenService $tokens): JsonResponse
    {
        $tokens->revokeCurrent($request->user());

        return response()->json([
            'message' => __('fuisic-auth::auth.logged_out'),
        ]);
    }
}
