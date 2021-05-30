<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamAddFormType;
use App\Repository\TeamRepository;
use Exception;
use Psr\Log\LoggerInterface;
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
    public function add(Request $request, TeamRepository $teamRepository, LoggerInterface $logger): Response
    {
        $team = new Team();

        $form = $this->createForm(TeamAddFormType::class, $team);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $team = $form->getData();

            try {
                $teamRepository->add($team);
            } catch (Exception $exception) {
                $logger->critical('Cannot add team', [
                    'exception' => $exception,
                ]);
            }

            return $this->redirectToRoute('teams');
        }

        return $this->render('team/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/team/{id}", methods={"GET"}, name="team", requirements={"id": "[1-9]\d*"})
     */
    public function team(Request $request, Team $team) {

        return $this->render('team/team.html.twig', [
            'team' => $team,
        ]);
    }
}
