<?php

namespace App\Controller;

use App\Entity\Einvoice;
use App\Service\EinvoiceService;
use App\Service\Note;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/note')]
final class NoteController extends AbstractController
{
    public function __construct(
        private EinvoiceService $einvoiceService,
        private Note $note,
    ) {}

    #[Route('/print/{id}')]
    public function print(Einvoice $einvoice, Request $request): Response
    {
        if (!$einvoice->getNoteNumber()) {
            return $this->redirectToRoute('app_note_form', ['id' => $einvoice->getId(), 'redirect' => $request->query->get('redirect')]);
        }
        return $this->render('note/print.html.twig', [
            'einvoice' => $einvoice,
            'simpleXml' => $this->einvoiceService->getXmlModel($einvoice)->getSimpleXml(),
        ]);
    }

    #[Route('/form/{id}')]
    public function form(Einvoice $einvoice, Request $request): Response
    {
        if ($einvoice->getNoteNumber()) {
            return $this->redirectToRoute('app_note_print', ['id' => $einvoice->getId(), 'redirect' => $request->query->get('redirect')]);
        }
        $noteModel = $this->note->getModel($einvoice);

        if ($request->isMethod('POST')) {
            $noteModel->setNumber($request->get('note')['number']);
            foreach ($request->get('item') as $id => $item) {
                $noteModel->getItemModel((string) $id)
                    ->setNameMatch($item['nameMatch'])
                    ->setSellPrice($item['sellPrice']);
            }
            $this->note->createNote($noteModel);
            return $this->redirectToRoute('app_note_print', ['id' => $einvoice->getId(), 'redirect' => $request->query->get('redirect')]);
        }

        return $this->render('note/form.html.twig', [
            'noteModel' => $noteModel,
        ]);
    }

    #[Route('/remove/{id}')]
    public function remove(Einvoice $einvoice, Request $request): Response
    {
        $this->note->removeNote($einvoice);
        $this->addFlash('success', 'Am șters nota!');
        if ($redirect = $request->query->get('redirect')) {
            return $this->redirect($redirect);
        }
        return $this->redirectToRoute('app_einvoice_index', ['date' => $einvoice->getIssueDate()]);
    }
}
