<?php

namespace App\Model\Note;

use App\Model\Einvoice\XmlLineModel;

class ItemModel
{
    private string $nameMatch;
    private ?float $sellPrice = null;
    private array $sellPriceSuggestions = [];

    public function __construct(
        private XmlLineModel $xmlModel,
    ) {
        $this->nameMatch = $this->xmlModel->getName();
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

    public function addSellPriceSuggestion(?float $sellPriceSuggestion): static
    {
        $this->sellPriceSuggestions[] = $sellPriceSuggestion;

        return $this;
    }

    public function getSellPriceSuggestions(): array
    {
        return $this->sellPriceSuggestions;
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
