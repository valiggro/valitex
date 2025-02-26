<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private Security $security,
    ) {}

    #[Route('/')]
    public function index(): Response
    {
        if ($this->security->getUser()) {
            return $this->redirectToRoute('app_setting_index');
        }
        return $this->render('home/index.html.twig');
    }
}
