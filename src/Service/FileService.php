<?php

namespace App\Service;

use App\Factory\ZipArchiveFactory;
use Symfony\Component\Filesystem\Filesystem;

class FileService
{
    public function __construct(
        private Filesystem $filesystem,
        private ZipArchiveFactory $zipArchiveFactory,
    ) {}

    public function extractZip(string $zipPath, string $extractPath): void
    {
        if ($this->filesystem->exists($extractPath)) {
            return;
        }
        $zipArchive = $this->zipArchiveFactory->__invoke();
        $zipArchive->open($zipPath);
        $zipArchive->extractTo($extractPath);
        $zipArchive->close();
    }

    public function parseXml(string $xmlPath): \SimpleXMLElement
    {
        $xml = $this->filesystem->readFile(
            filename: $xmlPath
        );
        $simpleXml = simplexml_load_string($xml);
        foreach ($simpleXml->getNamespaces(true) as $namespace => $url) {
            $xml = str_replace("<{$namespace}:", '<', $xml);
            $xml = str_replace("</{$namespace}:", '</', $xml);
        }
        return simplexml_load_string($xml);
    }
}
