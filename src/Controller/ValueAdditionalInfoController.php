<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/value-additional-info')]
class ValueAdditionalInfoController extends AbstractController
{
    #[Route('/value-additional-info', name: 'app_value_additional_info')]
    public function index(): Response
    {
        return $this->render('value_additional_info/index.html.twig', [
            'controller_name' => 'ValueAdditionalInfoController',
        ]);
    }
}
