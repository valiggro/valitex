<?php

namespace App\Controller;

use App\Service\AnafOAuth2;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AnafOauth2Controller extends AbstractController
{
    public function __construct(
        private AnafOAuth2 $anafOAuth2,
    ) {}

    #[Route('/anaf-oauth2-authorize')]
    public function authorize(): Response
    {
        return $this->redirect($this->anafOAuth2->getAuthorizationUrl());
    }

    #[Route('/anaf-oauth2-callback')]
    public function callback(Request $request): Response
    {
        $this->anafOAuth2->authorizationCode($request->get('code'));
        $this->addFlash('success', 'Am înnoit autorizarea ANAF!');
        return $this->redirectToRoute('app_setting_index');
    }
}
