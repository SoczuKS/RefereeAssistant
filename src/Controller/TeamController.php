<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\City;
use App\Entity\Team;
use App\Form\AddressFormType;
use App\Form\CityFormType;
use App\Form\TeamFormType;
use App\Service\AddressService;
use App\Service\CityService;
use App\Service\TeamService;
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
     * Team service
     *
     * @var TeamService
     */
    private $teamService;

    private $addressService;

    private $cityService;

    private $logger;

    public function __construct(TeamService $teamService, AddressService $addressService, CityService $cityService, LoggerInterface $logger)
    {
        $this->teamService = $teamService;
        $this->addressService = $addressService;
        $this->cityService = $cityService;
        $this->logger = $logger;
    }

    /**
     * @Route(
     *     "/teams",
     *     name="teams",
     *     methods={"GET"}
     * )
     */
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $pagination = $this->teamService->createPaginatedList($page);

        return $this->render('team/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route(
     *     "/teams/add",
     *     name="teams_add",
     *     methods={"GET", "POST"}
     * )
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function add(Request $request): Response
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
                $this->cityService->saveCity($city);
                $this->addFlash('success', 'city_added');
            } catch (Exception $exception) {
                $this->logger->critical('Cannot add city', [
                    'exception' => $exception,
                ]);
                $this->addFlash('error', 'add_failed');
            }

            return $this->redirectToRoute('teams_add');
        }

        if ($addressAddForm->isSubmitted() && $addressAddForm->isValid()) {
            $address = $addressAddForm->getData();

            try {
                $this->addressService->saveAddress($address);
                $this->addFlash('success', 'address_added');
            } catch (Exception $exception) {
                $this->logger->critical('Cannot add address', [
                    'exception' => $exception,
                ]);
                $this->addFlash('error', 'add_failed');
            }

            return $this->redirectToRoute('teams_add');
        }

        if ($teamAddForm->isSubmitted() && $teamAddForm->isValid()) {
            $team = $teamAddForm->getData();

            try {
                $this->teamService->saveTeam($team);
                $this->addFlash('success', 'team_added');
            } catch (Exception $exception) {
                $this->logger->critical('Cannot add team', [
                    'exception' => $exception,
                ]);
                $this->addFlash('error', 'add_failed');
            }

            return $this->redirectToRoute('teams_add');
        }

        return $this->render('team/add.html.twig', [
            'team_add_form' => $teamAddForm->createView(),
            'team_add_new_address_form' => $addressAddForm->createView(),
            'team_add_new_address_new_city_form' => $cityAddForm->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/team/{id}",
     *     name="team",
     *     methods={"GET"},
     *     requirements={"id": "\d+"}
     * )
     */
    public function singleTeam(Request $request, Team $team): Response
    {
        return $this->render('team/team.html.twig', [
            'team' => $team,
        ]);
    }

    /**
     * @Route(
     *     "/team/{id}/edit",
     *     name="team_edit",
     *     methods={"GET","PUT"},
     *     requirements={"id": "\d+"}
     * )
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Team $team)
    {
        $address = new Address();
        $city = new City();

        $teamAddForm = $this->createForm(TeamFormType::class, $team, ['method' => 'PUT']);
        // TODO: Fix 'duplicated lines' warning
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
                $this->cityService->saveCity($city);
                $this->addFlash('success', 'city_saved');
            } catch (Exception $exception) {
                $this->logger->critical('Cannot save city', [
                    'exception' => $exception,
                ]);
                $this->addFlash('error', 'save_failed');
            }

            return $this->redirectToRoute('teams_add');
        }

        if ($addressAddForm->isSubmitted() && $addressAddForm->isValid()) {
            $address = $addressAddForm->getData();

            try {
                $this->addressService->saveAddress($address);
                $this->addFlash('success', 'address_saved');
            } catch (Exception $exception) {
                $this->logger->critical('Cannot save address', [
                    'exception' => $exception,
                ]);
                $this->addFlash('error', 'save_failed');
            }

            return $this->redirectToRoute('teams_add');
        }

        if ($teamAddForm->isSubmitted() && $teamAddForm->isValid()) {
            $team = $teamAddForm->getData();

            try {
                $this->teamService->saveTeam($team);
                $this->addFlash('success', 'team_saved');
            } catch (Exception $exception) {
                $this->logger->critical('Cannot add team', [
                    'exception' => $exception,
                ]);
                $this->addFlash('error', 'save_failed');
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
     * @Route(
     *     "/team/{id}/delete/{action?none}",
     *     name="team_delete",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "\d+"}
     * )
     */
    public function delete(Request $request, Team $team, string $action): Response
    {
        if ('confirm' === $action) {
            try {
                $this->teamService->deleteTeam($team);
                $this->addFlash('success', 'team_deleted');
            } catch(Exception $exception) {
                $this->addFlash('error', $exception->getMessage());
            }

            return $this->redirectToRoute('teams');
        }

        return $this->render('team/delete.html.twig', [
            'team' => $team,
        ]);
    }
}
