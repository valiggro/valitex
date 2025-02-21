<?php

namespace App\Controller;

use App\Entity\Einvoice as EinvoiceEntity;
use App\Entity\EinvoiceItem;
use App\Repository\EinvoiceItemRepository;
use App\Service\Einvoice;
use App\Service\Setting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/note')]
final class NoteController extends AbstractController
{
    public function __construct(
        private EinvoiceItemRepository $einvoiceItemRepository,
        private Einvoice $einvoice,
        private EntityManagerInterface $entityManager,
        private Setting $setting,
    ) {}

    #[Route('/print/{id}')]
    public function print(EinvoiceEntity $einvoice, Request $request): Response
    {
        if (!$einvoice->getNoteNumber()) {
            return $this->redirectToRoute('app_note_form', ['id' => $einvoice->getId(), 'redirect' => $request->query->get('redirect')]);
        }
        return $this->render('note/print.html.twig', [
            'einvoice' => $einvoice,
            'simpleXml' => $this->einvoice->getXml($einvoice),
        ]);
    }

    #[Route('/form/{id}', methods: ['GET'])]
    public function form(EinvoiceEntity $einvoice, Request $request): Response
    {
        if ($einvoice->getNoteNumber()) {
            return $this->redirectToRoute('app_note_print', ['id' => $einvoice->getId(), 'redirect' => $request->query->get('redirect')]);
        }
        $einvoice->setNoteNumber($this->setting->get('note_last_number') + 1);
        $simpleXml = $this->einvoice->getXml($einvoice);

        $lines = [];
        foreach ($simpleXml->InvoiceLine as $InvoiceLine) {
            $items = $this->einvoiceItemRepository->findBy([
                'supplierId' => $einvoice->getSupplierId(),
                'price' => $InvoiceLine->Price->PriceAmount,
            ], [
                'sellPrice' => 'DESC'
            ]);
            $items = array_filter($items, function ($item) {
                return $item->getPrice() != $item->getSellPrice();
            });
            $nameMatch = $InvoiceLine->Item->Name;
            $sellPrice = null;
            foreach ($items as $item) {
                if (substr_count($nameMatch, $item->getNameMatch())) {
                    $nameMatch = $item->getNameMatch();
                    $sellPrice = $item->getSellPrice();
                    break;
                }
            }
            $s1 = null;
            foreach ($InvoiceLine->Item->AdditionalItemProperty as $p) {
                if ('Suggested Retail Price' == $p->Name) {
                    $s1 = $p->Value;
                    if ('XCS' == $InvoiceLine->InvoicedQuantity['unitCode']) {
                        $s1 *= 10;
                    }
                    break;
                }
            }
            $s2 = $items ? $items[0]->getSellPrice() : null;
            $lines[(string) $InvoiceLine->ID] = [
                'nameMatch' => $nameMatch,
                'sellPrice' => $sellPrice,
                'sellPriceSuggestions' => [$s1, $s2],
            ];
        }
        return $this->render('note/form.html.twig', [
            'einvoice' => $einvoice,
            'simpleXml' => $simpleXml,
            'lines' => $lines,
        ]);
    }

    #[Route('/form/{id}', methods: ['POST'])]
    public function form_post(EinvoiceEntity $einvoice, Request $request): Response
    {
        if ($einvoice->getNoteNumber()) {
            return $this->redirectToRoute('app_note_print', ['id' => $einvoice->getId(), 'redirect' => $request->query->get('redirect')]);
        }
        $simpleXml = $this->einvoice->getXml($einvoice);
        # einvoiceItem
        foreach ($simpleXml->InvoiceLine as $InvoiceLine) {
            $line = $request->get('line')[(string) $InvoiceLine->ID];
            $attributes = [
                'supplierId' => $einvoice->getSupplierId(),
                'nameMatch' => $line['nameMatch'],
                'price' => (float) $InvoiceLine->Price->PriceAmount,
            ];
            if (!$einvoiceItem = $this->einvoiceItemRepository->findOneBy($attributes)) {
                $einvoiceItem = (new EinvoiceItem)
                    ->setSupplierId($attributes['supplierId'])
                    ->setNameMatch($attributes['nameMatch'])
                    ->setPrice($attributes['price']);
            }
            $einvoiceItem->setSellPrice($line['sellPrice']);
            $this->entityManager->persist($einvoiceItem);
            $this->entityManager->flush();
        }
        # einvoice
        $einvoice->setNoteNumber($this->setting->get('note_last_number') + 1);
        $sellPrice = [];
        foreach ($simpleXml->InvoiceLine as $InvoiceLine) {
            $sellPrice[(int) $InvoiceLine->ID] = $request->get('line')[(string) $InvoiceLine->ID]['sellPrice'];
        }
        $einvoice->setSellPrice($sellPrice);
        $this->entityManager->persist($einvoice);
        $this->entityManager->flush();

        $this->setting->set('note_last_number', $einvoice->getNoteNumber());
        return $this->redirectToRoute('app_note_print', ['id' => $einvoice->getId(), 'redirect' => $request->query->get('redirect')]);
    }

    #[Route('/remove/{id}')]
    public function remove(EinvoiceEntity $einvoice, Request $request): Response
    {
        $einvoice->setNoteNumber(0);
        $this->entityManager->persist($einvoice);
        $this->entityManager->flush();
        $this->addFlash('success', 'Am șters nota!');
        if ($redirect = $request->query->get('redirect')) {
            return $this->redirect($redirect);
        }
        return $this->redirectToRoute('app_einvoice_index', ['date' => $einvoice->getIssueDate()]);
    }
}
