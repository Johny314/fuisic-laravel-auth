# Конфигурация

Файл конфигурации: `config/fuisic-auth.php` (публикуется командой `vendor:publish --tag=fuisic-auth-config`).

## Переменные окружения

| Переменная | По умолчанию | Описание |
|------------|--------------|----------|
| `FUISIC_AUTH_ROUTE_PREFIX` | `''` | Префикс маршрутов (пустой = `/register`, `/login`) |
| `FUISIC_AUTH_USER_MODEL` | — | Eloquent-модель пользователя (fallback: `auth.providers.users.model`) |
| `FUISIC_AUTH_TOKEN_NAME` | `api-token` | Имя Sanctum-токена |
| `FUISIC_AUTH_REQUIRE_EMAIL_VERIFICATION` | `true` | Блокировать login без подтверждённого email |
| `FRONTEND_URL` | `APP_URL` | URL фронтенда для ссылок в письмах |
| `FUISIC_AUTH_QUEUE_CONNECTION` | `QUEUE_CONNECTION` | Очередь для писем |
| `FUISIC_AUTH_VERIFICATION_QUEUE` | `auth.notifications` | Очередь verification |
| `FUISIC_AUTH_PASSWORD_RESET_QUEUE` | `auth.notifications` | Очередь password reset |
| `FUISIC_AUTH_VK_ENABLED` | `false` | Включить OAuth ВКонтакте |
| `FUISIC_AUTH_YANDEX_ENABLED` | `false` | Включить OAuth Яндекс |
| `FUISIC_AUTH_PASSKEYS_ENABLED` | `true` | Включить passkeys |
| `FUISIC_AUTH_PASSKEY_RP_NAME` | `APP_NAME` | Имя relying party |
| `FUISIC_AUTH_PASSKEY_RP_ID` | — | Домен для WebAuthn (например `localhost`) |

### OAuth credentials

| Переменная | Описание |
|------------|----------|
| `VKONTAKTE_CLIENT_ID` | ID приложения VK |
| `VKONTAKTE_CLIENT_SECRET` | Secret VK |
| `VKONTAKTE_REDIRECT_URI` | Callback URL |
| `YANDEX_CLIENT_ID` | ID приложения Yandex |
| `YANDEX_CLIENT_SECRET` | Secret Yandex |
| `YANDEX_REDIRECT_URI` | Callback URL |

Провайдеры OAuth: `vkontakte`, `yandex`.

## RabbitMQ в Laravel

Фрагмент для `config/queue.php`:

```php
'rabbitmq' => [
    'driver' => 'rabbitmq',
    'queue' => env('RABBITMQ_QUEUE', 'default'),
    'connection' => PhpAmqpLib\Connection\AMQPLazyConnection::class,
    'hosts' => [
        [
            'host' => env('RABBITMQ_HOST', '127.0.0.1'),
            'port' => env('RABBITMQ_PORT', 5672),
            'user' => env('RABBITMQ_USER', 'guest'),
            'password' => env('RABBITMQ_PASSWORD', 'guest'),
            'vhost' => env('RABBITMQ_VHOST', '/'),
        ],
    ],
    'options' => ['ssl_options' => []],
    'worker' => env('RABBITMQ_WORKER', 'default'),
    'lazy' => true,
    'after_commit' => false,
],
```

## Почта

Пакет отправляет:

- письмо подтверждения email;
- письмо сброса пароля.

Настройте `MAIL_*` в `.env`. Для локальной разработки можно использовать `MAIL_MAILER=log`.

## Passkeys (WebAuthn)

- `FUISIC_AUTH_PASSKEY_RP_ID` должен совпадать с доменом, с которого идут запросы (без порта для production).
- На localhost passkeys работают в Chrome/Safari при `RP_ID=localhost`.
- Для Apple Face ID / Touch ID используется стандарт WebAuthn — отдельный Apple OAuth не требуется.

## Middleware

| Ключ config | Значение по умолчанию | Назначение |
|-------------|----------------------|------------|
| `middleware` | `[]` | Middleware группы auth-маршрутов |
| `auth_middleware` | `['auth:sanctum']` | Защищённые эндпоинты |

## Очереди писем

Jobs:

- `Fuisic\Auth\Jobs\SendVerificationEmailJob`
- `Fuisic\Auth\Jobs\SendPasswordResetEmailJob`

Обе используют connection и queue из `config/fuisic-auth.php` → `queue.*`.

## Миграции пакета

| Таблица | Назначение |
|---------|------------|
| `oauth_accounts` | Привязка VK/Yandex к user |
| `password_reset_tokens` | Токены сброса пароля |
| `webauthn_credentials` | Passkeys (Laragear WebAuthn) |

Sanctum: `personal_access_tokens` — в приложении-хосте.
