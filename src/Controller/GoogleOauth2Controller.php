<?php

namespace App\Controller;

use League\OAuth2\Client\Provider\Google as GoogleProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleOauth2Controller extends AbstractController
{
    public function __construct(
        private GoogleProvider $googleProvider,
    ) {}

    #[Route('/google-oauth2-authorize')]
    public function authorize(): Response
    {
        return $this->redirect($this->googleProvider->getAuthorizationUrl());
    }

    #[Route('/google-oauth2-callback')]
    public function callback(): Response
    {
        throw new \Exception('Not implemented');
    }
}
