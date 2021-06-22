<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\City;
use App\Entity\Team;
use App\Form\AddressFormType;
use App\Form\CityFormType;
use App\Form\TeamFormType;
use App\Repository\AddressRepository;
use App\Repository\CityRepository;
use App\Repository\TeamRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function add(Request $request, TeamRepository $teamRepository, AddressRepository $addressRepository, CityRepository $cityRepository, LoggerInterface $logger): Response
    {
        $team = new Team();
        $address = new Address();
        $city = new City();

        $teamAddForm = $this->createForm(TeamFormType::class, $team);
        $addressAddForm = $this->createForm(AddressFormType::class, $address);
        $cityAddForm = $this->createForm(CityFormType::class, $city);

        if ($request->request->has('city_form')) {
            $cityAddForm->handleRequest($request);
        } elseif ($request->request->has('address_form')) {
            $addressAddForm->handleRequest($request);
        } elseif ($request->request->has('team_form')) {
            $teamAddForm->handleRequest($request);
        }

        if ($cityAddForm->isSubmitted() && $cityAddForm->isValid()) {
            $city = $cityAddForm->getData();

            try {
                $cityRepository->add($city);
            } catch (Exception $exception) {
                $logger->critical('Cannot add city', [
                    'exception' => $exception,
                ]);
            }
        } elseif ($addressAddForm->isSubmitted() && $addressAddForm->isValid()) {
            $address = $addressAddForm->getData();

            try {
                $addressRepository->add($address);
            } catch (Exception $exception) {
                $logger->critical('Cannot add address', [
                    'exception' => $exception,
                ]);
            }
        } elseif ($teamAddForm->isSubmitted() && $teamAddForm->isValid()) {
            $team = $teamAddForm->getData();

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
            'team_add_form' => $teamAddForm->createView(),
            'team_add_new_address_form' => $addressAddForm->createView(),
            'team_add_new_address_new_city_form' => $cityAddForm->createView(),
        ]);
    }

    /**
     * @Route("/team/{id}", methods={"GET"}, name="team", requirements={"id": "[1-9]\d*"})
     */
    public function team(Request $request, Team $team)
    {
        return $this->render('team/team.html.twig', [
            'team' => $team,
        ]);
    }
}
