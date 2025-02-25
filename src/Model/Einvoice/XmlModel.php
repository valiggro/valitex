<?php

namespace App\Model\Einvoice;

class XmlModel
{
    private array $lines;

    public function __construct(
        private \SimpleXMLElement $simpleXml,
    ) {
        foreach ($simpleXml->InvoiceLine as $InvoiceLine) {
            $this->lines[] = new XmlLineModel($InvoiceLine);
        }
    }

    public function getSimpleXml(): \SimpleXMLElement
    {
        return $this->simpleXml;
    }

    public function getId(): string
    {
        return $this->simpleXml->ID;
    }

    public function getSupplierName(): string
    {
        return $this->simpleXml->AccountingSupplierParty->Party->PartyLegalEntity->RegistrationName;
    }

    public function getIssueDate(): \DateTime
    {
        return new \DateTime($this->simpleXml->IssueDate);
    }

    public function getPayableAmount(): float
    {
        return (float) $this->simpleXml->LegalMonetaryTotal->PayableAmount;
    }

    public function getLines(): array
    {
        return $this->lines;
    }
}
