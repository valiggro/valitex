<?php

namespace App\Service;

use App\Entity\Einvoice;
use App\Entity\EinvoiceItem;
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

    public function getModel(Einvoice $einvoice): NoteModel
    {
        $einvoice->setNoteNumber($this->setting->get('note_last_number') + 1);

        $xmlModel = $this->einvoiceService->getXmlModel($einvoice);
        $noteModel = new NoteModel(
            einvoice: $einvoice,
            xmlModel: $xmlModel,
        );
        foreach ($xmlModel->getLines() as $xmlLineModel) {
            $itemModel = new ItemModel(
                xmlModel: $xmlLineModel,
            );

            $items = $this->einvoiceItemRepository->findBy([
                'supplierId' => $einvoice->getSupplierId(),
                'price' => $xmlLineModel->getPriceAmount(),
            ], [
                'sellPrice' => 'DESC'
            ]);
            $items = array_values(array_filter($items, function ($item) {
                return $item->getPrice() != $item->getSellPrice();
            }));
            foreach ($items as $item) {
                if (substr_count($itemModel->getNameMatch(), $item->getNameMatch())) {
                    $itemModel->setNameMatch($item->getNameMatch());
                    $itemModel->setSellPrice($item->getSellPrice());
                    break;
                }
            }

            $s1 = null;
            $InvoiceLine = $xmlLineModel->getSimpleXml();
            foreach ($InvoiceLine->Item->AdditionalItemProperty as $p) {
                if ('Suggested Retail Price' == $p->Name) {
                    $s1 = (float) $p->Value;
                    if ('XCS' == $InvoiceLine->InvoicedQuantity['unitCode']) {
                        $s1 *= 10;
                    }
                    break;
                }
            }
            $itemModel->addSellPriceSuggestion($s1);

            $s2 = $items ? $items[0]->getSellPrice() : null;
            $itemModel->addSellPriceSuggestion($s2);

            $noteModel->setItemModel($itemModel);
        }
        return $noteModel;
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
        # Einvoice::setNoteNumber() is set in static::getModel()
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
