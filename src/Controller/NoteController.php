<?php

namespace App\Controller;

use App\Entity\Einvoice as EinvoiceEntity;
use App\Service\Einvoice;
use App\Service\Note;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/note')]
final class NoteController extends AbstractController
{
    public function __construct(
        private Einvoice $einvoice,
        private Note $note,
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

    #[Route('/form/{id}')]
    public function form(EinvoiceEntity $einvoice, Request $request): Response
    {
        if ($einvoice->getNoteNumber()) {
            return $this->redirectToRoute('app_note_print', ['id' => $einvoice->getId(), 'redirect' => $request->query->get('redirect')]);
        }
        $noteModel = $this->note->getModel($einvoice);

        if ($request->isMethod('POST')) {
            foreach ($request->get('item') as $id => $item) {
                $itemModel = $noteModel->getItemModel((int) $id)
                    ->setNameMatch($item['nameMatch'])
                    ->setSellPrice($item['sellPrice']);
                $noteModel->setItemModel($itemModel);
            }
            $this->note->createNote($noteModel);
            return $this->redirectToRoute('app_note_print', ['id' => $einvoice->getId(), 'redirect' => $request->query->get('redirect')]);
        }

        return $this->render('note/form.html.twig', [
            'noteModel' => $noteModel,
        ]);
    }

    #[Route('/remove/{id}')]
    public function remove(EinvoiceEntity $einvoice, Request $request): Response
    {
        $this->note->removeNote($einvoice);
        $this->addFlash('success', 'Am șters nota!');
        if ($redirect = $request->query->get('redirect')) {
            return $this->redirect($redirect);
        }
        return $this->redirectToRoute('app_einvoice_index', ['date' => $einvoice->getIssueDate()]);
    }
}
