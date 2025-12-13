<?php

namespace SocialiteProviders\XIVAuth;

use SocialiteProviders\Manager\SocialiteWasCalled;

class XIVAuthExtendSocialite
{
    public function handle(SocialiteWasCalled $socialiteWasCalled): void
    {
        $socialiteWasCalled->extendSocialite('xivauth', Provider::class);
    }
}