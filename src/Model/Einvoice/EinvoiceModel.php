<?php

namespace App\Model\Einvoice;

use App\Entity\Einvoice;

class EinvoiceModel
{
    public function __construct(
        private Einvoice $einvoice,
        private string $varDir,
    ) {}

    public function getEinvoice(): Einvoice
    {
        return $this->einvoice;
    }

    public function getZipName(): string
    {
        return "{$this->einvoice->getMessage()->id}.zip";
    }

    public function getZipPath(): string
    {
        return "{$this->varDir}/{$this->getZipName()}";
    }

    public function getZipExtractPath(): string
    {
        return "{$this->varDir}/{$this->einvoice->getMessage()->id}";
    }

    public function getXmlPath(): string
    {
        return "{$this->getZipExtractPath()}/{$this->einvoice->getMessage()->solicitationId}.xml";
    }

    public function getPdfName(): string
    {
        return "{$this->einvoice->getMessage()->id}.pdf";
    }

    public function getPdfPath(): string
    {
        return "{$this->varDir}/{$this->getPdfName()}";
    }
}
