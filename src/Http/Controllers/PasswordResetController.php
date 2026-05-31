<?php

namespace Fuisic\Auth\Http\Controllers;

use Fuisic\Auth\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    public function forgot(Request $request, PasswordResetService $service): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $message = $service->sendResetLink($validated['email']);

        return response()->json(['message' => $message]);
    }

    public function reset(Request $request, PasswordResetService $service): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $message = $service->reset(
            $validated['email'],
            $validated['token'],
            $validated['password'],
        );

        return response()->json(['message' => $message]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => __('fuisic-auth::auth.current_password_invalid'),
            ], 422);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        return response()->json(['message' => __('fuisic-auth::auth.password_updated')]);
    }
}
