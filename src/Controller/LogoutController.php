<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LogoutController extends AbstractController
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    #[Route('/logout', name: 'app_logout')]
    public function index(Security $security): Response
    {
        $security->logout(false);

        return new RedirectResponse(
            $this->urlGenerator->generate('app_login'),
            Response::HTTP_SEE_OTHER
        );
    }
}
