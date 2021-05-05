<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="game_referees")
 */
class GameReferee
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="referees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Referee::class, inversedBy="matchesRefereed")
     * @ORM\JoinColumn(nullable=false)
     */
    private $referee;

    /**
     * @ORM\ManyToOne(targetEntity=RefereeRole::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $role;

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function getReferee(): ?Referee
    {
        return $this->referee;
    }

    public function setGame($game): self
    {
        $this->game = $game;
    }

    public function setReferee($referee): self
    {
        $this->referee = $referee;
    }

    public function getRole(): ?RefereeRole
    {
        return $this->role;
    }

    public function setRole(?RefereeRole $role): self
    {
        $this->role = $role;

        return $this;
    }
}
