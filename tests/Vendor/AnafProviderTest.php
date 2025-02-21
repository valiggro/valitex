<?php

namespace App\Tests\Vendor;

use Anaf\OAuth2\Client\Provider\AnafProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AnafProviderTest extends KernelTestCase
{
    public function test_construct(): void
    {
        $_ENV['ANAF_OAUTH2_CLIENT_ID'] = 'ANAF_OAUTH2_CLIENT_ID' . uniqid();
        $_ENV['ANAF_OAUTH2_CLIENT_SECRET'] = 'ANAF_OAUTH2_CLIENT_SECRET' . uniqid();
        $_ENV['ANAF_OAUTH2_REDIRECT_URL'] = 'ANAF_OAUTH2_REDIRECT_URL' . uniqid();

        $anafProvider = static::getContainer()->get(AnafProvider::class);

        static::assertSame(
            (new \ReflectionProperty(AnafProvider::class, 'clientId'))->getValue($anafProvider),
            $_ENV['ANAF_OAUTH2_CLIENT_ID']
        );
        static::assertSame(
            (new \ReflectionProperty(AnafProvider::class, 'clientSecret'))->getValue($anafProvider),
            $_ENV['ANAF_OAUTH2_CLIENT_SECRET']
        );
        static::assertSame(
            (new \ReflectionProperty(AnafProvider::class, 'redirectUri'))->getValue($anafProvider),
            $_ENV['ANAF_OAUTH2_REDIRECT_URL']
        );
    }
}
