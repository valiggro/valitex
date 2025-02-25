<?php

namespace App\Tests\Service;

use App\Entity\Einvoice as EinvoiceEntity;
use App\Factory\ZipArchiveFactory;
use App\Model\Einvoice\EinvoiceModel;
use App\Service\Einvoice;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

class EinvoiceTest extends KernelTestCase
{
    private EinvoiceModel $einvoiceModel;

    protected function setUp(): void
    {
        $message = (object) [
            'id' => random_int(1, 9999),
            'solicitationId' => random_int(1, 9999),
        ];
        $einvoice = (new EinvoiceEntity)
            ->setMessage($message);
        $this->einvoiceModel = new EinvoiceModel(
            einvoice: $einvoice,
            varDir: '/tmp',
        );
    }

    public function test_extractZip_exists(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects(static::once())
            ->method('exists')
            ->with($this->einvoiceModel->getZipExtractPath())
            ->willReturn(true);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $zipArchiveFactory = $this->createMock(ZipArchiveFactory::class);
        $zipArchiveFactory->expects(static::never())
            ->method('__invoke');
        static::getContainer()->set(ZipArchiveFactory::class, $zipArchiveFactory);

        $einvoice = static::getContainer()->get(Einvoice::class);
        $einvoice->extractZip($this->einvoiceModel);
    }

    public function test_extractZip(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects(static::once())
            ->method('exists')
            ->with($this->einvoiceModel->getZipExtractPath())
            ->willReturn(false);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $zipArchive = $this->createMock(\ZipArchive::class);
        $zipArchive->expects(static::once())
            ->method('open')
            ->with($this->einvoiceModel->getZipPath());
        $zipArchive->expects(static::once())
            ->method('extractTo')
            ->with($this->einvoiceModel->getZipExtractPath());
        $zipArchive->expects(static::once())
            ->method('close')
            ->with();

        $zipArchiveFactory = $this->createMock(ZipArchiveFactory::class);
        $zipArchiveFactory->expects(static::once())
            ->method('__invoke')
            ->with()
            ->willReturn($zipArchive);
        static::getContainer()->set(ZipArchiveFactory::class, $zipArchiveFactory);

        $einvoice = static::getContainer()->get(Einvoice::class);
        $einvoice->extractZip($this->einvoiceModel);
    }

    public function test_parseXml(): void
    {
        $this->markTestIncomplete();
    }

    public function test_getXml(): void
    {
        $this->markTestIncomplete();
    }

    public function test_getXmlModel(): void
    {
        $this->markTestIncomplete();
    }

    public function test_getPdfUrl(): void
    {
        $this->markTestIncomplete();
    }
}
