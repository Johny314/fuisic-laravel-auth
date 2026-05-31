<?php

namespace Fuisic\Auth\Enums;

enum OAuthProvider: string
{
    case Vkontakte = 'vkontakte';
    case Yandex = 'yandex';

    public function socialiteDriver(): string
    {
        return $this->value;
    }

    public static function tryFromEnabled(string $provider): ?self
    {
        $enum = self::tryFrom($provider);

        if ($enum === null) {
            return null;
        }

        return config("fuisic-auth.oauth.providers.{$enum->value}.enabled") ? $enum : null;
    }
}
