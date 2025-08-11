<?php

namespace App\Service;

use App\Entity\Einvoice as EinvoiceEntity;
use App\Factory\ZipArchiveFactory;
use App\Model\Einvoice\EinvoiceModel;
use App\Model\Einvoice\XmlModel;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class Einvoice
{
    public function __construct(
        private ContainerBagInterface $containerBag,
        private Filesystem $filesystem,
        private S3 $s3,
        private ZipArchiveFactory $zipArchiveFactory,
    ) {}

    public function getModel(EinvoiceEntity $einvoice): EinvoiceModel
    {
        return new EinvoiceModel(
            einvoice: $einvoice,
            varDir: $this->containerBag->get('einvoice.file_dir'),
        );
    }

    public function extractZip(EinvoiceModel $einvoiceModel): void
    {
        if ($this->filesystem->exists($einvoiceModel->getZipExtractPath())) {
            return;
        }
        $zipArchive = $this->zipArchiveFactory->__invoke();
        $zipArchive->open($einvoiceModel->getZipPath());
        $zipArchive->extractTo($einvoiceModel->getZipExtractPath());
        $zipArchive->close();
    }

    public function parseXml(EinvoiceModel $einvoiceModel): \SimpleXMLElement
    {
        $xml = $this->filesystem->readFile(
            filename: $einvoiceModel->getXmlPath()
        );
        $simpleXml = simplexml_load_string($xml);
        foreach ($simpleXml->getNamespaces(true) as $namespace => $url) {
            $xml = str_replace("<{$namespace}:", '<', $xml);
            $xml = str_replace("</{$namespace}:", '</', $xml);
        }
        return simplexml_load_string($xml);
    }

    public function getXml(EinvoiceEntity $einvoice): \SimpleXMLElement
    {
        $einvoiceModel = $this->getModel($einvoice);
        $this->s3->downloadFile(
            fileName: $einvoiceModel->getZipName(),
            filePath: $einvoiceModel->getZipPath(),
        );
        $this->extractZip($einvoiceModel);
        return $this->parseXml($einvoiceModel);
    }

    public function getXmlModel(EinvoiceEntity $einvoice): XmlModel
    {
        return new XmlModel($this->getXml($einvoice));
    }

    public function getPdfUrl(EinvoiceEntity $einvoice): string
    {
        $einvoiceModel = $this->getModel($einvoice);
        return $this->s3->getPresignedUrl(
            fileName: $einvoiceModel->getPdfName()
        );
    }
}
