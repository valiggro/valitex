<?php

namespace App\Model\Einvoice;

class XmlModel
{
    public function __construct(
        private \SimpleXMLElement $simpleXml,
    ) {}

    public function getSupplierName(): string
    {
        return $this->simpleXml->AccountingSupplierParty->Party->PartyLegalEntity->RegistrationName;
    }

    public function getNumber(): string
    {
        return $this->simpleXml->ID;
    }

    public function getIssueDate(): \DateTime
    {
        return new \DateTime($this->simpleXml->IssueDate);
    }

    public function getPayableAmount(): float
    {
        return (float) $this->simpleXml->LegalMonetaryTotal->PayableAmount;
    }
}
