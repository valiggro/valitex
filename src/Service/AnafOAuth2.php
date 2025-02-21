<?php

namespace App\Service;

use Anaf\OAuth2\Client\Provider\AnafProvider;
use App\Service\Setting;

class AnafOAuth2
{
    public function __construct(
        private AnafProvider $anafProvider,
        private Setting $setting,
    ) {}

    public function getAuthorizationUrl(): string
    {
        return $this->anafProvider->getAuthorizationUrl();
    }

    public function authorizationCode(string $code): void
    {
        $accessToken = $this->anafProvider->getAccessToken('authorization_code', [
            'code' => $code,
        ]);
        $this->setting->setMultiple([
            'anaf_oauth2_access_token' => $accessToken->getToken(),
            'anaf_oauth2_access_token_expires' => $accessToken->getExpires(), # 90 days
            'anaf_oauth2_refresh_token' => $accessToken->getRefreshToken(),
            'anaf_oauth2_refresh_token_expires' => $accessToken->getExpires() + 275 * 24 * 3600, # 90 + 275 = 365 days
        ]);
    }

    public function refreshToken(): void
    {
        if (time() < ($this->setting->get('anaf_oauth2_access_token_expires') - 24 * 3600)) {
            # only refresh in the last 24h
            return;
        }
        if (time() > $this->setting->get('anaf_oauth2_refresh_token_expires')) {
            # refresh token expired
            throw new \Exception;
        }
        $accessToken = $this->anafProvider->getAccessToken('refresh_token', [
            'refresh_token' => $this->setting->get('anaf_oauth2_refresh_token'),
        ]);
        $this->setting->setMultiple([
            'anaf_oauth2_access_token' => $accessToken->getToken(),
            'anaf_oauth2_access_token_expires' => $accessToken->getExpires(), # 90 days
        ]);
    }
}
