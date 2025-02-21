<?php

namespace App\Model\Einvoice;

class XmlModel
{
    private \SimpleXMLElement $simpleXml;

    public function with(\SimpleXMLElement $simpleXml): static
    {
        $new = clone $this;
        $new->simpleXml = $simpleXml;
        return $new;
    }

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
