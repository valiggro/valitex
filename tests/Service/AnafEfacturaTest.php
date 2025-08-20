<?php

namespace App\Tests\Service;

use Anaf\Resources\Efactura;
use Anaf\ValueObjects\Transporter\FileHandler;
use App\Service\AnafEfactura;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

class AnafEfacturaTest extends KernelTestCase
{
    private int $id;
    private string $zipPath;
    private string $xmlPath;
    private string $pdfPath;

    public function setUp(): void
    {
        $this->id = random_int(1, 9999);
        $this->zipPath = uniqid() . '.zip';
        $this->xmlPath = uniqid() . '.xml';
        $this->pdfPath = uniqid() . '.pdf';
    }

    public function test_getMessages(): void
    {
        $this->markTestIncomplete();
    }

    public function test_downloadZip_exists(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())
            ->method('exists')
            ->with($this->zipPath)
            ->willReturn(true);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $efactura = $this->createMock(Efactura::class);
        $efactura->expects($this->never())
            ->method('download');
        static::getContainer()->set(Efactura::class, $efactura);

        $anafEfactura = static::getContainer()->get(AnafEfactura::class);
        $anafEfactura->downloadZip(id: $this->id, zipPath: $this->zipPath);
    }

    public function test_downloadZip_noContent(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())
            ->method('exists')
            ->with($this->zipPath)
            ->willReturn(false);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $efactura = $this->createMock(Efactura::class);
        $efactura->expects($this->once())
            ->method('download')
            ->with([
                'id' => $this->id,
            ])
            ->willReturn(new FileHandler(fileContent: ''));
        static::getContainer()->set(Efactura::class, $efactura);

        $anafEfactura = static::getContainer()->get(AnafEfactura::class);
        $this->expectException(\Exception::class);
        $anafEfactura->downloadZip(id: $this->id, zipPath: $this->zipPath);
    }

    public function test_downloadZip(): void
    {
        $content = uniqid();

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())
            ->method('exists')
            ->with($this->zipPath)
            ->willReturn(false);
        $filesystem->expects($this->once())
            ->method('dumpFile')
            ->with($this->zipPath, $content);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $efactura = $this->createMock(Efactura::class);
        $efactura->expects($this->once())
            ->method('download')
            ->with([
                'id' => $this->id,
            ])
            ->willReturn(new FileHandler(fileContent: $content));
        static::getContainer()->set(Efactura::class, $efactura);

        $anafEfactura = static::getContainer()->get(AnafEfactura::class);
        $anafEfactura->downloadZip(id: $this->id, zipPath: $this->zipPath);
    }

    public function test_downloadPdf_exists(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())
            ->method('exists')
            ->with($this->pdfPath)
            ->willReturn(true);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $efactura = $this->createMock(Efactura::class);
        $efactura->expects($this->never())
            ->method('xmlToPdf');
        static::getContainer()->set(Efactura::class, $efactura);

        $anafEfactura = static::getContainer()->get(AnafEfactura::class);
        $anafEfactura->downloadPdf(xmlPath: $this->xmlPath, pdfPath: $this->pdfPath);
    }

    public function test_downloadPdf_noContent(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())
            ->method('exists')
            ->with($this->pdfPath)
            ->willReturn(false);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $efactura = $this->createMock(Efactura::class);
        $efactura->expects($this->once())
            ->method('xmlToPdf')
            ->with($this->xmlPath)
            ->willReturn(new FileHandler(fileContent: ''));
        static::getContainer()->set(Efactura::class, $efactura);

        $anafEfactura = static::getContainer()->get(AnafEfactura::class);
        $this->expectException(\Exception::class);
        $anafEfactura->downloadPdf(xmlPath: $this->xmlPath, pdfPath: $this->pdfPath);
    }

    public function test_downloadPdf(): void
    {
        $content = uniqid();

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())
            ->method('exists')
            ->with($this->pdfPath)
            ->willReturn(false);
        $filesystem->expects($this->once())
            ->method('dumpFile')
            ->with($this->pdfPath, $content);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $efactura = $this->createMock(Efactura::class);
        $efactura->expects($this->once())
            ->method('xmlToPdf')
            ->with($this->xmlPath)
            ->willReturn(new FileHandler(fileContent: $content));
        static::getContainer()->set(Efactura::class, $efactura);

        $anafEfactura = static::getContainer()->get(AnafEfactura::class);
        $anafEfactura->downloadPdf(xmlPath: $this->xmlPath, pdfPath: $this->pdfPath);
    }
}
