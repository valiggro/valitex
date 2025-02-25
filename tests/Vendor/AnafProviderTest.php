<?php

namespace App\Tests\Vendor;

use Anaf\OAuth2\Client\Provider\AnafProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AnafProviderTest extends KernelTestCase
{
    public function test_construct(): void
    {
        $anafProvider = static::getContainer()->get(AnafProvider::class);

        static::assertSame(
            (new \ReflectionProperty(AnafProvider::class, 'clientId'))->getValue($anafProvider),
            'TEST_ANAF_OAUTH2_CLIENT_ID'
        );
        static::assertSame(
            (new \ReflectionProperty(AnafProvider::class, 'clientSecret'))->getValue($anafProvider),
            'TEST_ANAF_OAUTH2_CLIENT_SECRET'
        );
        static::assertSame(
            (new \ReflectionProperty(AnafProvider::class, 'redirectUri'))->getValue($anafProvider),
            'TEST_ANAF_OAUTH2_REDIRECT_URL'
        );
    }
}
