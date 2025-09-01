<?php

namespace App\Model\Note;

use App\Entity\Einvoice;
use App\Model\Einvoice\XmlModel;

class NoteModel
{
    private array $itemModels = [];

    public function __construct(
        private int $number,
        private Einvoice $einvoice,
        private XmlModel $xmlModel,
    ) {}

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getEinvoice(): Einvoice
    {
        return $this->einvoice;
    }

    public function getXmlModel(): XmlModel
    {
        return $this->xmlModel;
    }

    public function setItemModel(ItemModel $itemModel): static
    {
        $this->itemModels[(string) $itemModel->getXmlModel()->getId()] = $itemModel;

        return $this;
    }

    public function getItemModel(string $id): ItemModel
    {
        return $this->itemModels[$id];
    }

    public function getItemModels(): array
    {
        return $this->itemModels;
    }
}
