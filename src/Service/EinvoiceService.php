<?php

namespace App\Service;

use App\Entity\Einvoice;
use App\Model\Einvoice\EinvoiceModel;
use App\Model\Einvoice\XmlModel;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class EinvoiceService
{
    public function __construct(
        private ContainerBagInterface $containerBag,
        private FileService $fileService,
        private S3 $s3,
    ) {}

    public function getModel(Einvoice $einvoice): EinvoiceModel
    {
        return new EinvoiceModel(
            einvoice: $einvoice,
            varDir: $this->containerBag->get('einvoice.file_dir'),
        );
    }

    public function getXmlModel(Einvoice $einvoice): XmlModel
    {
        $einvoiceModel = $this->getModel($einvoice);
        $this->s3->downloadFile(
            fileName: $einvoiceModel->getZipName(),
            filePath: $einvoiceModel->getZipPath(),
        );
        $this->fileService->extractZip(
            zipPath: $einvoiceModel->getZipPath(),
            extractPath: $einvoiceModel->getZipExtractPath(),
        );
        $simpleXml = $this->fileService->parseXml(
            xmlPath: $einvoiceModel->getXmlPath(),
        );
        return new XmlModel($simpleXml);
    }

    public function getPdfUrl(Einvoice $einvoice): string
    {
        $einvoiceModel = $this->getModel($einvoice);
        return $this->s3->getPresignedUrl(
            fileName: $einvoiceModel->getPdfName()
        );
    }
}
