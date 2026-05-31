<?php

namespace Fuisic\Auth\Http\Controllers;

use Fuisic\Auth\Enums\OAuthProvider;
use Fuisic\Auth\Services\OAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OAuthController extends Controller
{
    public function redirect(string $provider, Request $request, OAuthService $oauth): JsonResponse
    {
        $providerEnum = OAuthProvider::tryFromEnabled($provider);

        if ($providerEnum === null) {
            return response()->json(['message' => __('fuisic-auth::auth.oauth_provider_disabled')], 404);
        }

        $isLink = $request->route()?->named('fuisic-auth.oauth.link') ?? $request->boolean('link');
        $linkUserId = $isLink ? $request->user()?->id : null;

        if ($isLink && $linkUserId === null) {
            return response()->json(['message' => __('fuisic-auth::auth.unauthenticated')], 401);
        }

        return response()->json([
            'url' => $oauth->redirectUrl($providerEnum, $linkUserId),
        ]);
    }

    public function callback(string $provider, Request $request, OAuthService $oauth): JsonResponse
    {
        $validated = $request->validate([
            'state' => ['required', 'string'],
        ]);

        $result = $oauth->handleCallback($provider, $validated['state']);

        return response()->json($result);
    }

    public function unlink(string $provider, Request $request, OAuthService $oauth): JsonResponse
    {
        $providerEnum = OAuthProvider::tryFromEnabled($provider);

        if ($providerEnum === null) {
            return response()->json(['message' => __('fuisic-auth::auth.oauth_provider_disabled')], 404);
        }

        $oauth->unlinkAccount($request->user(), $providerEnum);

        return response()->json(['message' => __('fuisic-auth::auth.oauth_unlinked')]);
    }

    public function linked(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'providers' => method_exists($user, 'oauthAccounts')
                ? $user->oauthAccounts()->select(['provider', 'provider_email', 'avatar', 'created_at'])->get()
                : [],
        ]);
    }
}
