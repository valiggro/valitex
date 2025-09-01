<?php

namespace App\Model\Einvoice;

class XmlLineModel
{
    public function __construct(
        private \SimpleXMLElement $InvoiceLine,
    ) {}

    public function getSimpleXml(): \SimpleXMLElement
    {
        return $this->InvoiceLine;
    }

    public function getId(): int
    {
        return (int) $this->InvoiceLine->ID;
    }

    public function getName(): string
    {
        return $this->InvoiceLine->Item->Name;
    }

    public function getDescription(): string
    {
        return @$this->InvoiceLine->Item->Description;
    }

    public function getNote(): ?string
    {
        return @$this->InvoiceLine->Note;
    }

    public function getPriceAmount(): float
    {
        return (float) $this->InvoiceLine->Price->PriceAmount;
    }

    public function getSuggestedRetailPrice(): ?float
    {
        foreach ($this->InvoiceLine->Item->AdditionalItemProperty as $property) {
            if ((string) $property->Name === 'Suggested Retail Price') {
                $price = (float) $property->Value;
                if ((string) $this->InvoiceLine->InvoicedQuantity['unitCode'] === 'XCS') {
                    $price *= 10;
                }
                return $price;
            }
        }
        return null;
    }
}
