<?php

namespace App\Tests\Service;

use App\Factory\ZipArchiveFactory;
use App\Service\FileService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

class FileServiceTest extends KernelTestCase
{
    public function test_extractZip_exists(): void
    {
        $zipPath = uniqid() . '.zip';
        $extractPath = uniqid() . '/';

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())
            ->method('exists')
            ->with($extractPath)
            ->willReturn(true);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $zipArchiveFactory = $this->createMock(ZipArchiveFactory::class);
        $zipArchiveFactory->expects($this->never())
            ->method('__invoke');
        static::getContainer()->set(ZipArchiveFactory::class, $zipArchiveFactory);

        static::getContainer()->get(FileService::class)->extractZip(
            zipPath: $zipPath,
            extractPath: $extractPath
        );
    }

    public function test_extractZip(): void
    {
        $zipPath = uniqid() . '.zip';
        $extractPath = uniqid() . '/';

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())
            ->method('exists')
            ->with($extractPath)
            ->willReturn(false);
        static::getContainer()->set(Filesystem::class, $filesystem);

        $zipArchive = $this->createMock(\ZipArchive::class);
        $zipArchive->expects($this->once())
            ->method('open')
            ->with($zipPath);
        $zipArchive->expects($this->once())
            ->method('extractTo')
            ->with($extractPath);
        $zipArchive->expects($this->once())
            ->method('close')
            ->with();

        $zipArchiveFactory = $this->createMock(ZipArchiveFactory::class);
        $zipArchiveFactory->expects($this->once())
            ->method('__invoke')
            ->with()
            ->willReturn($zipArchive);
        static::getContainer()->set(ZipArchiveFactory::class, $zipArchiveFactory);

        static::getContainer()->get(FileService::class)->extractZip(
            zipPath: $zipPath,
            extractPath: $extractPath
        );
    }

    public function test_parseXml(): void
    {
        $this->markTestIncomplete();
    }
}
