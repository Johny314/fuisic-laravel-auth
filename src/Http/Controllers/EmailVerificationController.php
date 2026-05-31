<?php

namespace Fuisic\Auth\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\URL;

class EmailVerificationController extends Controller
{
    public function verify(Request $request, int $id, string $hash): JsonResponse
    {
        if (! URL::hasValidSignature($request)) {
            return response()->json(['message' => __('fuisic-auth::auth.verification_invalid')], 403);
        }

        $userModel = \Fuisic\Auth\Support\UserModel::class();
        $user = $userModel::query()->findOrFail($id);

        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->json(['message' => __('fuisic-auth::auth.verification_invalid')], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => __('fuisic-auth::auth.email_already_verified')]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['message' => __('fuisic-auth::auth.email_verified')]);
    }

    public function resend(Request $request, \Fuisic\Auth\Services\EmailVerificationService $verification): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => __('fuisic-auth::auth.email_already_verified')]);
        }

        $verification->send($request->user());

        return response()->json(['message' => __('fuisic-auth::auth.verification_sent')]);
    }
}
