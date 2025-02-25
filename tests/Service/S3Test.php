<?php

namespace App\Tests\Service;

use App\Model\Einvoice\EinvoiceModel;
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
        $fileName = 'fileName' . uniqid();
        $filePath = 'filePath' . uniqid();
        $result = new Result([
            '@metadata' => [
                'statusCode' => 200,
            ],
        ]);

        $s3Client = $this->getMockBuilder(S3Client::class)
            ->disableOriginalConstructor()
            ->addMethods(['getObject', 'putObject'])
            ->getMock();
        $s3Client->expects(static::once())
            ->method('getObject')
            ->with([
                'Bucket' => $this->bucket,
                'Key' => $fileName,
            ])
            ->willReturn($result);
        $s3Client->expects(static::never())
            ->method('putObject');
        static::getContainer()->set(S3Client::class, $s3Client);

        $s3 = static::getContainer()->get(S3::class);
        $s3->uploadFile($fileName, $filePath);
    }

    public function test_uploadFile_statusCode_wrong(): void
    {
        $fileName = 'fileName' . uniqid();
        $filePath = 'filePath' . uniqid();
        $result = new Result([
            '@metadata' => [
                'statusCode' => -1,
            ],
        ]);

        $s3Client = $this->getMockBuilder(S3Client::class)
            ->disableOriginalConstructor()
            ->addMethods(['getObject', 'putObject'])
            ->getMock();
        $s3Client->expects(static::once())
            ->method('getObject')
            ->with([
                'Bucket' => $this->bucket,
                'Key' => $fileName,
            ])
            ->willReturn($result);
        $s3Client->expects(static::never())
            ->method('putObject');
        static::getContainer()->set(S3Client::class, $s3Client);

        $s3 = static::getContainer()->get(S3::class);
        $this->expectException(\Exception::class);
        $s3->uploadFile($fileName, $filePath);
    }

    public function test_uploadFile(): void
    {
        $fileName = 'fileName' . uniqid();
        $filePath = 'filePath' . uniqid();
        $result = new Result([
            '@metadata' => [
                'statusCode' => 200,
            ],
        ]);

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
                'Key' => $fileName,
            ])
            ->willThrowException($s3Exception);
        $s3Client->expects(static::once())
            ->method('putObject')
            ->with([
                'Bucket' => $this->bucket,
                'Key' => $fileName,
                'SourceFile' => $filePath,
            ])
            ->willReturn($result);
        static::getContainer()->set(S3Client::class, $s3Client);

        $s3 = static::getContainer()->get(S3::class);
        static::assertSame(
            $s3->uploadFile($fileName, $filePath),
            $result
        );
    }

    public function test_uploadZip(): void
    {
        $zipName = 'zipName' . uniqid();
        $zipPath = 'zipPath' . uniqid();
        $result = new Result([
            '@metadata' => [
                'statusCode' => 200,
            ],
        ]);

        $s3Client = $this->getMockBuilder(S3Client::class)
            ->disableOriginalConstructor()
            ->addMethods(['getObject'])
            ->getMock();
        $s3Client->expects(static::once())
            ->method('getObject')
            ->with([
                'Bucket' => $this->bucket,
                'Key' => $zipName,
            ])
            ->willReturn($result);
        static::getContainer()->set(S3Client::class, $s3Client);

        $einvoiceModel = $this->createMock(EinvoiceModel::class);
        $einvoiceModel->expects(static::once())
            ->method('getZipName')
            ->willReturn($zipName);
        $einvoiceModel->expects(static::once())
            ->method('getZipPath')
            ->willReturn($zipPath);

        $s3 = static::getContainer()->get(S3::class);
        static::assertSame($s3->uploadZip($einvoiceModel), $result);
    }

    public function test_uploadPdf(): void
    {
        $pdfName = 'pdfName' . uniqid();
        $pdfPath = 'pdfPath' . uniqid();
        $result = new Result([
            '@metadata' => [
                'statusCode' => 200,
            ],
        ]);

        $s3Client = $this->getMockBuilder(S3Client::class)
            ->disableOriginalConstructor()
            ->addMethods(['getObject'])
            ->getMock();
        $s3Client->expects(static::once())
            ->method('getObject')
            ->with([
                'Bucket' => $this->bucket,
                'Key' => $pdfName,
            ])
            ->willReturn($result);
        static::getContainer()->set(S3Client::class, $s3Client);

        $einvoiceModel = $this->createMock(EinvoiceModel::class);
        $einvoiceModel->expects(static::once())
            ->method('getPdfName')
            ->willReturn($pdfName);
        $einvoiceModel->expects(static::once())
            ->method('getPdfPath')
            ->willReturn($pdfPath);

        $s3 = static::getContainer()->get(S3::class);
        static::assertSame($s3->uploadPdf($einvoiceModel), $result);
    }

    public function test_downloadFile_exists(): void
    {
        $fileName = 'fileName' . uniqid();
        $filePath = 'filePath' . uniqid();

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects(static::once())
            ->method('exists')
            ->with($filePath)
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
        $s3->downloadFile($fileName, $filePath);
    }

    public function test_downloadFile_statusCode_wrong(): void
    {
        $fileName = 'fileName' . uniqid();
        $filePath = 'filePath' . uniqid();
        $result = new Result([
            '@metadata' => [
                'statusCode' => -1,
            ],
        ]);

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects(static::once())
            ->method('exists')
            ->with($filePath)
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
                'Key' => $fileName,
            ])
            ->willReturn($result);
        static::getContainer()->set(S3Client::class, $s3Client);

        $s3 = static::getContainer()->get(S3::class);
        $this->expectException(\Exception::class);
        $s3->downloadFile($fileName, $filePath);
    }

    public function test_downloadFile(): void
    {
        $fileName = 'fileName' . uniqid();
        $filePath = 'filePath' . uniqid();
        $result = new Result([
            'Body' => 'Body' . uniqid(),
            '@metadata' => [
                'statusCode' => 200,
            ],
        ]);

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects(static::once())
            ->method('exists')
            ->with($filePath)
            ->willReturn(false);
        $filesystem->expects(static::once())
            ->method('dumpFile')
            ->with($filePath, $result['Body']);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $s3Client = $this->getMockBuilder(S3Client::class)
            ->disableOriginalConstructor()
            ->addMethods(['getObject'])
            ->getMock();
        $s3Client->expects(static::once())
            ->method('getObject')
            ->with([
                'Bucket' => $this->bucket,
                'Key' => $fileName,
            ])
            ->willReturn($result);
        static::getContainer()->set(S3Client::class, $s3Client);

        $s3 = static::getContainer()->get(S3::class);
        $s3->downloadFile($fileName, $filePath);
    }

    public function test_downloadZip(): void
    {
        $zipName = 'zipName' . uniqid();
        $zipPath = 'zipPath' . uniqid();
        $result = new Result([
            'Body' => 'Body' . uniqid(),
            '@metadata' => [
                'statusCode' => 200,
            ],
        ]);

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects(static::once())
            ->method('exists')
            ->with($zipPath)
            ->willReturn(false);
        $filesystem->expects(static::once())
            ->method('dumpFile')
            ->with($zipPath, $result['Body']);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $s3Client = $this->getMockBuilder(S3Client::class)
            ->disableOriginalConstructor()
            ->addMethods(['getObject'])
            ->getMock();
        $s3Client->expects(static::once())
            ->method('getObject')
            ->with([
                'Bucket' => $this->bucket,
                'Key' => $zipName,
            ])
            ->willReturn($result);
        static::getContainer()->set(S3Client::class, $s3Client);

        $einvoiceModel = $this->createMock(EinvoiceModel::class);
        $einvoiceModel->expects(static::once())
            ->method('getZipName')
            ->willReturn($zipName);
        $einvoiceModel->expects(static::once())
            ->method('getZipPath')
            ->willReturn($zipPath);

        $s3 = static::getContainer()->get(S3::class);
        $s3->downloadZip($einvoiceModel);
    }

    public function test_getPresignedUrl(): void
    {
        $this->markTestIncomplete();
    }
}
