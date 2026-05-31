<?php

namespace Fuisic\Auth\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Crypt;

class AuthTokenService
{
    public function issue(Authenticatable $user): string
    {
        return $user->createToken(config('fuisic-auth.token_name'))->plainTextToken;
    }

    public function revokeCurrent(Authenticatable $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    public function revokeAll(Authenticatable $user): void
    {
        $user->tokens()->delete();
    }

    public function createOAuthState(array $payload): string
    {
        return Crypt::encryptString(json_encode($payload, JSON_THROW_ON_ERROR));
    }

    public function parseOAuthState(string $state): array
    {
        return json_decode(Crypt::decryptString($state), true, 512, JSON_THROW_ON_ERROR);
    }
}
