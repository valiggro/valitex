<?php

namespace App\Tests\Service;

use App\Service\S3;
use Aws\Result;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class S3Test extends KernelTestCase
{
    private string $bucket = 'TEST_AWS_S3_BUCKET';

    private string $fileName;
    private string $filePath;

    protected function setUp(): void
    {
        $this->fileName = 'fileName' . uniqid();
        $this->filePath = 'filePath' . uniqid();
    }

    private function _result(int $statusCode = 200): Result
    {
        return new Result([
            'Body' => 'Body' . uniqid(),
            '@metadata' => [
                'statusCode' => $statusCode,
            ],
        ]);
    }

    public function test_construct(): void
    {
        $containerBag = static::getContainer()->get(ContainerBagInterface::class);
        static::assertSame(
            $containerBag->get('aws.s3_bucket'),
            $this->bucket
        );

        $s3 = static::getContainer()->get(S3::class);
        static::assertSame(
            (new \ReflectionProperty(S3::class, 'bucket'))->getValue($s3),
            $this->bucket
        );
    }

    public function test_uploadFile_exists(): void
    {
        $result = $this->_result();

        $s3Client = $this->getMockBuilder(S3Client::class)
            ->disableOriginalConstructor()
            ->addMethods(['getObject', 'putObject'])
            ->getMock();
        $s3Client->expects(static::once())
            ->method('getObject')
            ->with([
                'Bucket' => $this->bucket,
                'Key' => $this->fileName,
            ])
            ->willReturn($result);
        $s3Client->expects(static::never())
            ->method('putObject');
        static::getContainer()->set(S3Client::class, $s3Client);

        $s3 = static::getContainer()->get(S3::class);
        $s3->uploadFile($this->fileName, $this->filePath);
    }

    public function test_uploadFile_statusCode_wrong(): void
    {
        $result = $this->_result(-1);

        $s3Client = $this->getMockBuilder(S3Client::class)
            ->disableOriginalConstructor()
            ->addMethods(['getObject', 'putObject'])
            ->getMock();
        $s3Client->expects(static::once())
            ->method('getObject')
            ->with([
                'Bucket' => $this->bucket,
                'Key' => $this->fileName,
            ])
            ->willReturn($result);
        $s3Client->expects(static::never())
            ->method('putObject');
        static::getContainer()->set(S3Client::class, $s3Client);

        $s3 = static::getContainer()->get(S3::class);
        $this->expectException(\Exception::class);
        $s3->uploadFile($this->fileName, $this->filePath);
    }

    public function test_uploadFile(): void
    {
        $result = $this->_result();

        $s3Client = $this->getMockBuilder(S3Client::class)
            ->disableOriginalConstructor()
            ->addMethods(['getObject', 'putObject'])
            ->getMock();
        $s3Exception = $this->getMockBuilder(S3Exception::class)
            ->disableOriginalConstructor()
            ->getMock();
        $s3Client->expects(static::once())
            ->method('getObject')
            ->with([
                'Bucket' => $this->bucket,
                'Key' => $this->fileName,
            ])
            ->willThrowException($s3Exception);
        $s3Client->expects(static::once())
            ->method('putObject')
            ->with([
                'Bucket' => $this->bucket,
                'Key' => $this->fileName,
                'SourceFile' => $this->filePath,
            ])
            ->willReturn($result);
        static::getContainer()->set(S3Client::class, $s3Client);

        $s3 = static::getContainer()->get(S3::class);
        static::assertSame(
            $s3->uploadFile($this->fileName, $this->filePath),
            $result
        );
    }

    public function test_downloadFile_exists(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects(static::once())
            ->method('exists')
            ->with($this->filePath)
            ->willReturn(true);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $s3Client = $this->getMockBuilder(S3Client::class)
            ->disableOriginalConstructor()
            ->addMethods(['getObject'])
            ->getMock();
        $s3Client->expects(static::never())
            ->method('getObject');
        static::getContainer()->set(S3Client::class, $s3Client);

        $s3 = static::getContainer()->get(S3::class);
        $s3->downloadFile($this->fileName, $this->filePath);
    }

    public function test_downloadFile_statusCode_wrong(): void
    {
        $result = $this->_result(-1);

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects(static::once())
            ->method('exists')
            ->with($this->filePath)
            ->willReturn(false);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $s3Client = $this->getMockBuilder(S3Client::class)
            ->disableOriginalConstructor()
            ->addMethods(['getObject'])
            ->getMock();
        $s3Client->expects(static::once())
            ->method('getObject')
            ->with([
                'Bucket' => $this->bucket,
                'Key' => $this->fileName,
            ])
            ->willReturn($result);
        static::getContainer()->set(S3Client::class, $s3Client);

        $s3 = static::getContainer()->get(S3::class);
        $this->expectException(\Exception::class);
        $s3->downloadFile($this->fileName, $this->filePath);
    }

    public function test_downloadFile(): void
    {
        $result = $this->_result();

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects(static::once())
            ->method('exists')
            ->with($this->filePath)
            ->willReturn(false);
        $filesystem->expects(static::once())
            ->method('dumpFile')
            ->with($this->filePath, $result['Body']);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $s3Client = $this->getMockBuilder(S3Client::class)
            ->disableOriginalConstructor()
            ->addMethods(['getObject'])
            ->getMock();
        $s3Client->expects(static::once())
            ->method('getObject')
            ->with([
                'Bucket' => $this->bucket,
                'Key' => $this->fileName,
            ])
            ->willReturn($result);
        static::getContainer()->set(S3Client::class, $s3Client);

        $s3 = static::getContainer()->get(S3::class);
        $s3->downloadFile($this->fileName, $this->filePath);
    }

    public function test_getPresignedUrl(): void
    {
        $this->markTestIncomplete();
    }
}
