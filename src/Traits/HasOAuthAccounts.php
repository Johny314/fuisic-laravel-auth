<?php

namespace Fuisic\Auth\Traits;

use Fuisic\Auth\Models\OAuthAccount;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasOAuthAccounts
{
    public function oauthAccounts(): HasMany
    {
        return $this->hasMany(OAuthAccount::class, 'user_id');
    }

    public function findOAuthAccount(string $provider, string $providerId): ?OAuthAccount
    {
        return $this->oauthAccounts()
            ->where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();
    }

    public function hasOAuthProvider(string $provider): bool
    {
        return $this->oauthAccounts()->where('provider', $provider)->exists();
    }
}
