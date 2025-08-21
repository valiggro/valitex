<?php

namespace App\Service;

use Anaf\Responses\Efactura\Message;
use App\Entity\Einvoice as EinvoiceEntity;
use App\Model\AnafEfactura\MessageModel;
use App\Model\Einvoice\XmlModel;
use App\Repository\EinvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;

class EinvoiceImport
{
    public function __construct(
        private EinvoiceRepository $einvoiceRepository,
        private Einvoice $einvoice,
        private AnafEfactura $anafEfactura,
        private EntityManagerInterface $entityManager,
        private FileService $fileService,
        private S3 $s3,
    ) {}

    public function importMessage(Message $message): EinvoiceEntity
    {
        if (!$einvoice = $this->einvoiceRepository->findOneBy(['messageId' => $message->id])) {
            $messageModel = new MessageModel($message);
            $einvoice = (new EinvoiceEntity)
                ->setMessage($message)
                ->setSupplierId($messageModel->getSupplierId());
            $this->entityManager->persist($einvoice);
            $this->entityManager->flush();
        }
        return $einvoice;
    }

    public function importZip(EinvoiceEntity $einvoice): void
    {
        if ($einvoice->hasZip()) {
            return;
        }
        $einvoiceModel = $this->einvoice->getModel($einvoice);
        $this->anafEfactura->downloadZip(
            id: $einvoice->getMessageId(),
            zipPath: $einvoiceModel->getZipPath(),
        );
        $result = $this->s3->uploadFile(
            fileName: $einvoiceModel->getZipName(),
            filePath: $einvoiceModel->getZipPath(),
        );
        $einvoice->setS3ZipModifiedAt(
            new \DateTimeImmutable($result['@metadata']['headers']['date'])
        );
        $this->entityManager->persist($einvoice);
        $this->entityManager->flush();
    }

    public function importXml(EinvoiceEntity $einvoice): void
    {
        if ($einvoice->hasXml()) {
            return;
        }
        $einvoiceModel = $this->einvoice->getModel($einvoice);
        $this->anafEfactura->downloadZip(
            id: $einvoice->getMessageId(),
            zipPath: $einvoiceModel->getZipPath(),
        );
        $this->fileService->extractZip(
            zipPath: $einvoiceModel->getZipPath(),
            extractPath: $einvoiceModel->getZipExtractPath(),
        );
        $simpleXml = $this->fileService->parseXml(
            xmlPath: $einvoiceModel->getXmlPath(),
        );
        $xmlModel = new XmlModel($simpleXml);
        $einvoice
            ->setSupplierName($xmlModel->getSupplierName())
            ->setNumber($xmlModel->getId())
            ->setIssueDate($xmlModel->getIssueDate())
            ->setPayableAmount($xmlModel->getPayableAmount());
        $this->entityManager->persist($einvoice);
        $this->entityManager->flush();
    }

    public function importPdf(EinvoiceEntity $einvoice): void
    {
        if ($einvoice->hasPdf()) {
            return;
        }
        $einvoiceModel = $this->einvoice->getModel($einvoice);
        $this->anafEfactura->downloadZip(
            id: $einvoice->getMessageId(),
            zipPath: $einvoiceModel->getZipPath(),
        );
        $this->fileService->extractZip(
            zipPath: $einvoiceModel->getZipPath(),
            extractPath: $einvoiceModel->getZipExtractPath(),
        );
        $this->anafEfactura->downloadPdf(
            xmlPath: $einvoiceModel->getXmlPath(),
            pdfPath: $einvoiceModel->getPdfPath(),
        );
        $result = $this->s3->uploadFile(
            fileName: $einvoiceModel->getPdfName(),
            filePath: $einvoiceModel->getPdfPath(),
        );
        $einvoice->setS3PdfModifiedAt(
            new \DateTimeImmutable($result['@metadata']['headers']['date'])
        );
        $this->entityManager->persist($einvoice);
        $this->entityManager->flush();
    }
}
