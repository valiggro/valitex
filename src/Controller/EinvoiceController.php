<?php

namespace App\Controller;

use App\Entity\Einvoice as EinvoiceEntity;
use App\Repository\EinvoiceRepository;
use App\Service\Einvoice;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/einvoice')]
final class EinvoiceController extends AbstractController
{
    public function __construct(
        private EinvoiceRepository $einvoiceRepository,
        private Einvoice $einvoice,
    ) {}

    #[Route('/')]
    public function index(Request $request): Response
    {
        return $this->render('einvoice/index.html.twig', [
            'suppliers' => $this->einvoiceRepository->getSupplierMap(),
            'einvoices' => $this->einvoiceRepository->getAll($request->query->all()),
        ]);
    }

    #[Route('/pdf/{id}')]
    public function pdf(EinvoiceEntity $einvoice): Response
    {
        if (!$einvoice->hasPdf()) {
            throw $this->createNotFoundException();
        }
        return $this->redirect(
            $this->einvoice->getPdfUrl($einvoice)
        );
    }
}
