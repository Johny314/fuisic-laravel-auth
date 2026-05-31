<?php

namespace Fuisic\Auth\Listeners;

use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\VKontakte\Provider as VkontakteProvider;
use SocialiteProviders\Yandex\Provider as YandexProvider;

class SocialiteWasCalledListener
{
    public function handle(SocialiteWasCalled $event): void
    {
        $event->extendSocialite('vkontakte', VkontakteProvider::class);
        $event->extendSocialite('yandex', YandexProvider::class);
    }
}
