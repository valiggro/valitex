<?php

namespace App\Tests\Vendor;

use Aws\S3\S3Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class S3ClientTest extends KernelTestCase
{
    public function test_construct(): void
    {
        $_ENV['AWS_KEY'] = 'AWS_KEY' . uniqid();
        $_ENV['AWS_SECRET'] = 'AWS_SECRET' . uniqid();
        $_ENV['AWS_S3_ENDPOINT'] = 'AWS_S3_ENDPOINT' . uniqid();

        $s3Client = static::getContainer()->get(S3Client::class);
        static::assertSame($s3Client->getEndpoint()->__toString(), $_ENV['AWS_S3_ENDPOINT']);

        $credentials = $s3Client->getCredentials()->wait();
        static::assertSame($credentials->getAccessKeyId(), $_ENV['AWS_KEY']);
        static::assertSame($credentials->getSecretKey(), $_ENV['AWS_SECRET']);
    }
}
