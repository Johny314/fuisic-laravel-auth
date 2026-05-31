<?php

namespace Fuisic\Auth\Support;

class UserModel
{
    public static function class(): string
    {
        $model = config('fuisic-auth.user_model') ?? config('auth.providers.users.model');

        if (! is_string($model) || $model === '') {
            throw new \RuntimeException(
                'User model is not configured. Set FUISIC_AUTH_USER_MODEL or auth.providers.users.model.'
            );
        }

        return $model;
    }
}
