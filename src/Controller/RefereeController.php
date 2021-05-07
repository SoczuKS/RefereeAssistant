<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RefereeController extends AbstractController
{
    /**
     * @Route("/referees", name="referees")
     */
    public function index(): Response
    {
        return $this->render('referee/index.html.twig', [
            'controller_name' => 'RefereeController',
        ]);
    }
}
