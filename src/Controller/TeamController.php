<?php

namespace App\Controller;

use App\Form\TeamAddFormType;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{
    /**
     * @Route("/teams", name="teams")
     */
    public function index(Request $request, TeamRepository $teamRepository): Response
    {
        return $this->render('team/index.html.twig', [
            'teams' => $teamRepository->findAll(),
        ]);
    }

    /**
     * @Route("/teams/add", name="teams_add")
     */
    public function add(): Response
    {
        $form = $this->createForm(TeamAddFormType::class);

        return $this->render('team/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
