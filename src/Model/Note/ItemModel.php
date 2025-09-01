<?php

namespace App\Model\Note;

use App\Model\Einvoice\XmlLineModel;

class ItemModel
{
    public function __construct(
        private XmlLineModel $xmlModel,
        private ?string $nameMatch,
        private ?float $sellPrice,
        private ?float $suggestedRetailPrice,
    ) {
        if (empty($this->nameMatch)) {
            $this->nameMatch = $this->xmlModel->getName();
        }
    }

    public function getXmlModel(): XmlLineModel
    {
        return $this->xmlModel;
    }

    public function setNameMatch(string $nameMatch): static
    {
        $this->nameMatch = $nameMatch;

        return $this;
    }

    public function getNameMatch(): string
    {
        return $this->nameMatch;
    }

    public function setSellPrice(float $sellPrice): static
    {
        $this->sellPrice = $sellPrice;

        return $this;
    }

    public function getSellPrice(): ?float
    {
        return $this->sellPrice;
    }

    public function getSuggestedRetailPrice(): ?float
    {
        return $this->suggestedRetailPrice;
    }

    public function getNote(): ?string
    {
        $note = $this->xmlModel->getNote();
        if (trim($this->xmlModel->getName()) == trim($note)) {
            return null;
        }
        if (substr_count($note, 'cod intern SmartBill:')) {
            return null;
        }
        return $note;
    }
}
