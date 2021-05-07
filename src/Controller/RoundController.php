<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoundController extends AbstractController
{
    /**
     * @Route("/rounds", name="rounds")
     */
    public function index(): Response
    {
        return $this->render('round/index.html.twig', [
            'controller_name' => 'RoundController',
        ]);
    }
}
