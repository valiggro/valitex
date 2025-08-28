<?php

namespace App\Controller;

use App\Form\Setting\NoteLastNumberType;
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

    #[Route('/')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(NoteLastNumberType::class, [
            'note_last_number' => $this->setting->get('note_last_number'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->setting->set('note_last_number', $data['note_last_number']);
            $this->addFlash('success', 'Am salvat setările!');
            return $this->redirectToRoute('app_setting_index');
        }

        return $this->render('setting/index.html.twig', [
            'form' => $form,
            'settings' => $this->setting->getAll(),
        ]);
    }
}
