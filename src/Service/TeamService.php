<?php

namespace App\Service;

use App\Entity\Team;
use App\Repository\TeamRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class TeamService
 */
class TeamService
{
    /**
     * Team repository
     *
     * @var TeamRepository
     */
    private $teamRepository;

    /**
     * Paginator
     *
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(TeamRepository $teamRepository, PaginatorInterface $paginator)
    {
        $this->teamRepository = $teamRepository;
        $this->paginator = $paginator;
    }

    public function createPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate($this->teamRepository->findAll(), $page, self::TEAMS_PER_PAGE);
    }

    public function saveTeam(Team $team)
    {
        $this->teamRepository->save($team);
    }

    public function deleteTeam(Team $team)
    {
        $this->teamRepository->delete($team);
    }

    const TEAMS_PER_PAGE = 30;
}
