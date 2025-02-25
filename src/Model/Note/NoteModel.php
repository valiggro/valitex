<?php

namespace App\Model\Note;

use App\Entity\Einvoice;
use App\Model\Einvoice\XmlModel;

class NoteModel
{
    private array $itemModels = [];

    public function __construct(
        private Einvoice $einvoice,
        private XmlModel $xmlModel,
    ) {}

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
        $this->itemModels[$itemModel->getXmlModel()->getId()] = $itemModel;

        return $this;
    }

    public function getItemModel(int $id): ItemModel
    {
        return $this->itemModels[$id];
    }

    public function getItemModels(): array
    {
        return $this->itemModels;
    }
}
