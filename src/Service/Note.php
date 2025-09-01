<?php

namespace App\Service;

use App\Entity\Einvoice;
use App\Entity\EinvoiceItem;
use App\Model\Einvoice\XmlLineModel;
use App\Model\Note\ItemModel;
use App\Model\Note\NoteModel;
use App\Repository\EinvoiceItemRepository;
use Doctrine\ORM\EntityManagerInterface;

class Note
{
    public function __construct(
        private EinvoiceItemRepository $einvoiceItemRepository,
        private EinvoiceService $einvoiceService,
        private EntityManagerInterface $entityManager,
        private Setting $setting,
    ) {}

    private function getItemModel(Einvoice $einvoice, XmlLineModel $xmlLineModel): ItemModel
    {
        $byMatch = $this->einvoiceItemRepository->findOneByNameMatch(
            supplierId: $einvoice->getSupplierId(),
            price: $xmlLineModel->getPriceAmount(),
            nameMatch: $xmlLineModel->getName(),
        );
        $byPrice = $byMatch ?: $this->einvoiceItemRepository->findOneByPrice(
            supplierId: $einvoice->getSupplierId(),
            price: $xmlLineModel->getPriceAmount(),
        );
        return new ItemModel(
            xmlModel: $xmlLineModel,
            nameMatch: $byMatch ? $byMatch->getNameMatch() : null,
            sellPrice: $byMatch ? $byMatch->getSellPrice() : null,
            suggestedRetailPrice: $byPrice ? $byPrice->getSellPrice() : null,
        );
    }

    public function getModel(Einvoice $einvoice): NoteModel
    {
        $xmlModel = $this->einvoiceService->getXmlModel($einvoice);
        return new NoteModel(
            number: $this->setting->get('note_last_number') + 1,
            einvoice: $einvoice,
            xmlModel: $xmlModel,
            itemModels: array_map(fn($xmlLineModel) => $this->getItemModel($einvoice, $xmlLineModel), $xmlModel->getLines()),
        );
    }

    public function createNote(NoteModel $noteModel): void
    {
        $einvoice = $noteModel->getEinvoice();

        # einvoiceItem
        foreach ($noteModel->getItemModels() as $itemModel) {
            $xmlModel = $itemModel->getXmlModel();
            if (!$einvoiceItem = $this->einvoiceItemRepository->findOneBy([
                'supplierId' => $einvoice->getSupplierId(),
                'nameMatch' => $itemModel->getNameMatch(),
                'price' => $xmlModel->getPriceAmount(),
            ])) {
                $einvoiceItem = (new EinvoiceItem)
                    ->setSupplierId($einvoice->getSupplierId())
                    ->setNameMatch($itemModel->getNameMatch())
                    ->setPrice($xmlModel->getPriceAmount());
            }
            $einvoiceItem->setSellPrice($itemModel->getSellPrice());
            $this->entityManager->persist($einvoiceItem);
            $this->entityManager->flush();
        }

        # einvoice
        $einvoice->setNoteNumber($noteModel->getNumber());
        $sellPrice = [];
        foreach ($noteModel->getItemModels() as $itemModel) {
            $xmlModel = $itemModel->getXmlModel();
            $sellPrice[$xmlModel->getId()] = $itemModel->getSellPrice();
        }
        $einvoice->setSellPrice($sellPrice);
        $this->entityManager->persist($einvoice);
        $this->entityManager->flush();

        # setting
        $this->setting->set('note_last_number', $einvoice->getNoteNumber());
    }

    public function removeNote(Einvoice $einvoice): void
    {
        $einvoice->setNoteNumber(0);
        $this->entityManager->persist($einvoice);
        $this->entityManager->flush();
    }
}
