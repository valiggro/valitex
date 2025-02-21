<?php

namespace App\Model\Einvoice;

use App\Entity\Einvoice;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class EinvoiceModel
{
    private Einvoice $einvoice;
    private string $varDir;

    public function __construct(
        private ContainerBagInterface $containerBag,
    ) {
        $this->varDir = $this->containerBag->get('einvoice.file_dir');
    }

    public function with(Einvoice $einvoice): static
    {
        $new = clone $this;
        $new->einvoice = $einvoice;
        return $new;
    }

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
