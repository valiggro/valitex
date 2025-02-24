<?php

namespace App\Tests\Service;

use Anaf\Resources\Efactura;
use Anaf\ValueObjects\Transporter\FileHandler;
use App\Entity\Einvoice;
use App\Model\Einvoice\EinvoiceModel;
use App\Service\AnafEfactura;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

class AnafEfacturaTest extends KernelTestCase
{
    private EinvoiceModel $einvoiceModel;

    protected function setUp(): void
    {
        $message = (object) [
            'id' => random_int(1, 9999),
            'solicitationId' => random_int(1, 9999),
        ];
        $einvoice = (new Einvoice)
            ->setMessage($message);
        $this->einvoiceModel = new EinvoiceModel(
            einvoice: $einvoice,
            varDir: '/tmp',
        );
    }

    public function test_getMessages(): void
    {
        $this->markTestIncomplete();
    }

    public function test_downloadZip_exists(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects(static::once())
            ->method('exists')
            ->with($this->einvoiceModel->getZipPath())
            ->willReturn(true);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $efactura = $this->createMock(Efactura::class);
        $efactura->expects(static::never())
            ->method('download');
        static::getContainer()->set(Efactura::class, $efactura);

        $anafEfactura = static::getContainer()->get(AnafEfactura::class);
        $anafEfactura->downloadZip($this->einvoiceModel);
    }

    public function test_downloadZip_noContent(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects(static::once())
            ->method('exists')
            ->with($this->einvoiceModel->getZipPath())
            ->willReturn(false);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $efactura = $this->createMock(Efactura::class);
        $efactura->expects(static::once())
            ->method('download')
            ->with([
                'id' => $this->einvoiceModel->getEinvoice()->getMessageId(),
            ])
            ->willReturn(new FileHandler(fileContent: ''));
        static::getContainer()->set(Efactura::class, $efactura);

        $anafEfactura = static::getContainer()->get(AnafEfactura::class);
        $this->expectException(\Exception::class);
        $anafEfactura->downloadZip($this->einvoiceModel);
    }

    public function test_downloadZip(): void
    {
        $content = uniqid();

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects(static::once())
            ->method('exists')
            ->with($this->einvoiceModel->getZipPath())
            ->willReturn(false);
        $filesystem->expects(static::once())
            ->method('dumpFile')
            ->with($this->einvoiceModel->getZipPath(), $content);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $efactura = $this->createMock(Efactura::class);
        $efactura->expects(static::once())
            ->method('download')
            ->with([
                'id' => $this->einvoiceModel->getEinvoice()->getMessageId(),
            ])
            ->willReturn(new FileHandler(fileContent: $content));
        static::getContainer()->set(Efactura::class, $efactura);

        $anafEfactura = static::getContainer()->get(AnafEfactura::class);
        $anafEfactura->downloadZip($this->einvoiceModel);
    }

    public function test_downloadPdf_exists(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects(static::once())
            ->method('exists')
            ->with($this->einvoiceModel->getPdfPath())
            ->willReturn(true);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $efactura = $this->createMock(Efactura::class);
        $efactura->expects(static::never())
            ->method('xmlToPdf');
        static::getContainer()->set(Efactura::class, $efactura);

        $anafEfactura = static::getContainer()->get(AnafEfactura::class);
        $anafEfactura->downloadPdf($this->einvoiceModel);
    }

    public function test_downloadPdf_noContent(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects(static::once())
            ->method('exists')
            ->with($this->einvoiceModel->getPdfPath())
            ->willReturn(false);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $efactura = $this->createMock(Efactura::class);
        $efactura->expects(static::once())
            ->method('xmlToPdf')
            ->with($this->einvoiceModel->getXmlPath())
            ->willReturn(new FileHandler(fileContent: ''));
        static::getContainer()->set(Efactura::class, $efactura);

        $anafEfactura = static::getContainer()->get(AnafEfactura::class);
        $this->expectException(\Exception::class);
        $anafEfactura->downloadPdf($this->einvoiceModel);
    }

    public function test_downloadPdf(): void
    {
        $content = uniqid();

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects(static::once())
            ->method('exists')
            ->with($this->einvoiceModel->getPdfPath())
            ->willReturn(false);
        $filesystem->expects(static::once())
            ->method('dumpFile')
            ->with($this->einvoiceModel->getPdfPath(), $content);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $efactura = $this->createMock(Efactura::class);
        $efactura->expects(static::once())
            ->method('xmlToPdf')
            ->with($this->einvoiceModel->getXmlPath())
            ->willReturn(new FileHandler(fileContent: $content));
        static::getContainer()->set(Efactura::class, $efactura);

        $anafEfactura = static::getContainer()->get(AnafEfactura::class);
        $anafEfactura->downloadPdf($this->einvoiceModel);
    }
}
