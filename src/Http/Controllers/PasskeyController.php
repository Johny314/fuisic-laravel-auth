<?php

namespace Fuisic\Auth\Http\Controllers;

use Fuisic\Auth\Services\AuthTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laragear\WebAuthn\Http\Requests\AssertedRequest;
use Laragear\WebAuthn\Http\Requests\AssertionRequest;
use Laragear\WebAuthn\Http\Requests\AttestationRequest;
use Laragear\WebAuthn\Http\Requests\AttestedRequest;

class PasskeyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $credentials = $request->user()->webAuthnCredentials()
            ->select(['id', 'alias', 'created_at', 'last_used_at'])
            ->get();

        return response()->json(['passkeys' => $credentials]);
    }

    public function registerOptions(AttestationRequest $request): JsonResponse
    {
        return response()->json($request->toVerify($request->user()));
    }

    public function register(AttestedRequest $request): JsonResponse
    {
        $request->save();

        return response()->json(['message' => __('fuisic-auth::auth.passkey_registered')]);
    }

    public function loginOptions(AssertionRequest $request): JsonResponse
    {
        return response()->json($request->toVerify());
    }

    public function login(AssertedRequest $request, AuthTokenService $tokens): JsonResponse
    {
        $user = $request->login();

        return response()->json([
            'token' => $tokens->issue($user),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $request->user()->webAuthnCredentials()->whereKey($id)->delete();

        return response()->json(['message' => __('fuisic-auth::auth.passkey_removed')]);
    }
}
