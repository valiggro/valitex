<?php

namespace App\Tests\Vendor;

use Aws\S3\S3Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class S3ClientTest extends KernelTestCase
{
    public function test_construct(): void
    {
        $s3Client = static::getContainer()->get(S3Client::class);
        static::assertSame($s3Client->getEndpoint()->__toString(), 'TEST_AWS_S3_ENDPOINT');

        $credentials = $s3Client->getCredentials()->wait();
        static::assertSame($credentials->getAccessKeyId(), 'TEST_AWS_KEY');
        static::assertSame($credentials->getSecretKey(), 'TEST_AWS_SECRET');
    }
}
