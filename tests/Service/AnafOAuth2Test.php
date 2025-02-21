<?php

namespace App\Tests\Service;

use Anaf\OAuth2\Client\Provider\AnafProvider;
use App\Service\AnafOAuth2;
use App\Service\Setting;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AnafOAuth2Test extends KernelTestCase
{
    public function test_getAuthorizationUrl(): void
    {
        $this->markTestIncomplete();
    }

    public function test_authorizationCode(): void
    {
        $code = uniqid();
        $accessToken = new AccessToken([
            'access_token' => "access_token::{$code}",
            'expires' => time() + 3600 * 24 * 90,
            'refresh_token' => "refresh_token::{$code}",
            'refresh_token_expires' => time() + 3600 * 24 * 365,
        ]);

        $anafProvider = $this->createMock(AnafProvider::class);
        $anafProvider->expects(static::once())
            ->method('getAccessToken')
            ->with('authorization_code', [
                'code' => $code,
            ])
            ->willReturn($accessToken);
        static::getContainer()->set(AnafProvider::class, $anafProvider);

        $setting = $this->createMock(Setting::class);
        $setting->expects(static::once())
            ->method('setMultiple')
            ->with([
                'anaf_oauth2_access_token' => $accessToken->getToken(),
                'anaf_oauth2_access_token_expires' => $accessToken->getExpires(),
                'anaf_oauth2_refresh_token' => $accessToken->getRefreshToken(),
                'anaf_oauth2_refresh_token_expires' => $accessToken->getValues()['refresh_token_expires'],
            ]);
        static::getContainer()->set(Setting::class, $setting);

        $anafOAuth2 = static::getContainer()->get(AnafOAuth2::class);
        $anafOAuth2->authorizationCode($code);
    }

    public function test_refreshToken_notYet(): void
    {
        $setting = $this->createMock(Setting::class);
        $setting->expects(static::once())
            ->method('get')
            ->with('anaf_oauth2_access_token_expires')
            ->willReturn((string) (time() + 24 * 3600 + 1));
        $setting->expects(static::never())
            ->method('setMultiple');
        static::getContainer()->set(Setting::class, $setting);

        $anafProvider = $this->createMock(AnafProvider::class);
        $anafProvider->expects(static::never())
            ->method('getAccessToken');
        static::getContainer()->set(AnafProvider::class, $anafProvider);

        $anafOAuth2 = static::getContainer()->get(AnafOAuth2::class);
        $anafOAuth2->refreshToken();
    }

    public function test_refreshToken_refreshExpired(): void
    {
        $settings = [
            'anaf_oauth2_access_token_expires' => time() + 24 * 3600 - 1,
            'anaf_oauth2_refresh_token_expires' => time() - 1,
        ];
        $setting = $this->createMock(Setting::class);
        $setting->expects(static::exactly(count($settings)))
            ->method('get')
            ->with(
                self::logicalOr(...array_keys($settings))
            )
            ->willReturnCallback(
                fn($k) => (string) $settings[$k]
            );
        $setting->expects(static::never())
            ->method('setMultiple');
        static::getContainer()->set(Setting::class, $setting);

        $anafProvider = $this->createMock(AnafProvider::class);
        $anafProvider->expects(static::never())
            ->method('getAccessToken');
        static::getContainer()->set(AnafProvider::class, $anafProvider);

        $anafOAuth2 = static::getContainer()->get(AnafOAuth2::class);
        $this->expectException(\Exception::class);
        $anafOAuth2->refreshToken();
    }

    public function test_refreshToken(): void
    {
        $refreshToken = uniqid();
        $accessToken = new AccessToken([
            'access_token' => "access_token::{$refreshToken}",
            'expires' => time() + 3600 * 24 * 90,
        ]);

        $settings = [
            'anaf_oauth2_access_token_expires' => time() + 24 * 3600 - 1,
            'anaf_oauth2_refresh_token_expires' => time() + 1,
            'anaf_oauth2_refresh_token' => $refreshToken,
        ];
        $setting = $this->createMock(Setting::class);
        $setting->expects(static::exactly(count($settings)))
            ->method('get')
            ->with(
                self::logicalOr(...array_keys($settings))
            )
            ->willReturnCallback(
                fn($k) => (string) $settings[$k]
            );
        $setting->expects(static::once())
            ->method('setMultiple')
            ->with([
                'anaf_oauth2_access_token' => $accessToken->getToken(),
                'anaf_oauth2_access_token_expires' => $accessToken->getExpires(),
            ]);
        static::getContainer()->set(Setting::class, $setting);

        $anafProvider = $this->createMock(AnafProvider::class);
        $anafProvider->expects(static::once())
            ->method('getAccessToken')
            ->with('refresh_token', [
                'refresh_token' => $refreshToken,
            ])
            ->willReturn($accessToken);
        static::getContainer()->set(AnafProvider::class, $anafProvider);

        $anafOAuth2 = static::getContainer()->get(AnafOAuth2::class);
        $anafOAuth2->refreshToken();
    }
}
