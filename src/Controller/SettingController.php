<?php

namespace App\Controller;

use App\Service\Setting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/setting')]
final class SettingController extends AbstractController
{
    public function __construct(
        private Setting $setting,
    ) {}

    #[Route('/', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('setting/index.html.twig', [
            'settings' => $this->setting->getAll(),
        ]);
    }

    #[Route('/', methods: ['POST'])]
    public function update(Request $request): Response
    {
        $this->setting->set('note_last_number', $request->request->get('note_last_number'));
        $this->addFlash('success', 'Am salvat setările!');
        return $this->redirectToRoute('app_setting_index');
    }
}
