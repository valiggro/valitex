<?php

namespace App\Tests\Vendor;

use League\OAuth2\Client\Provider\Google as GoogleProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GoogleProviderTest extends KernelTestCase
{
    public function test_construct(): void
    {
        $googleProvider = static::getContainer()->get(GoogleProvider::class);

        static::assertSame(
            (new \ReflectionProperty(GoogleProvider::class, 'clientId'))->getValue($googleProvider),
            'TEST_GOOGLE_OAUTH2_CLIENT_ID'
        );
        static::assertSame(
            (new \ReflectionProperty(GoogleProvider::class, 'clientSecret'))->getValue($googleProvider),
            'TEST_GOOGLE_OAUTH2_CLIENT_SECRET'
        );
        static::assertSame(
            (new \ReflectionProperty(GoogleProvider::class, 'redirectUri'))->getValue($googleProvider),
            'TEST_GOOGLE_OAUTH2_REDIRECT_URL'
        );
    }
}
