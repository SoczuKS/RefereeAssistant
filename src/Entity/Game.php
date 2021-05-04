<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 * @ORM\Table(name="matches")
 * @UniqueEntity("matchNumber")
 */
class Game {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="match_number", length=20, nullable=true, unique=true)
     */
    private $matchNumber;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="smallint", name="home_goals", nullable=true, options={"unsigned":true})
     */
    private $homeGoals;

    /**
     * @ORM\Column(type="smallint", name="away_goals", nullable=true, options={"unsigned":true})
     */
    private $awayGoals;

    /**
     * @ORM\Column(type="smallint", name="yellow_cards", nullable=true, options={"unsigned":true})
     */
    private $yellowCards;

    /**
     * @ORM\Column(type="smallint", name="red_cards_from_second_yellow", nullable=true, options={"unsigned":true})
     */
    private $redCardsFromSecondYellow;

    /**
     * @ORM\Column(type="smallint", name="red_cards", nullable=true, options={"unsigned":true})
     */
    private $redCards;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $held;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class)
     * @ORM\JoinColumn(name="home_team", nullable=false)
     */
    private $homeTeam;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class)
     * @ORM\JoinColumn(name="away_team", nullable=false)
     */
    private $awayTeam;

    public function getId(): ?int {
        return $this->id;
    }

    public function getMatchNumber(): ?int {
        return $this->matchNumber;
    }

    public function setMatchNumber(?int $matchNumber): self {
        $this->matchNumber = $matchNumber;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self {
        $this->date = $date;

        return $this;
    }

    public function getHomeGoals(): ?int {
        return $this->homeGoals;
    }

    public function setHomeGoals(?int $homeGoals): self {
        $this->homeGoals = $homeGoals;

        return $this;
    }

    public function getAwayGoals(): ?int {
        return $this->awayGoals;
    }

    public function setAwayGoals(int $awayGoals): self {
        $this->awayGoals = $awayGoals;

        return $this;
    }

    public function getYellowCards(): ?int {
        return $this->yellowCards;
    }

    public function setYellowCards(?int $yellowCards): self {
        $this->yellowCards = $yellowCards;

        return $this;
    }

    public function getRedCardsFromSecondYellow(): ?int {
        return $this->redCardsFromSecondYellow;
    }

    public function setRedCardsFromSecondYellow(?int $redCardsFromSecondYellow): self {
        $this->redCardsFromSecondYellow = $redCardsFromSecondYellow;

        return $this;
    }

    public function getRedCards(): ?int {
        return $this->redCards;
    }

    public function setRedCards(?int $redCards): self {
        $this->redCards = $redCards;

        return $this;
    }

    public function getHeld(): ?bool {
        return $this->held;
    }

    public function setHeld(bool $held): self {
        $this->held = $held;

        return $this;
    }

    public function getHomeTeam(): ?Team {
        return $this->homeTeam;
    }

    public function setHomeTeam(?Team $homeTeam): self {
        $this->homeTeam = $homeTeam;

        return $this;
    }

    public function getAwayTeam(): ?Team {
        return $this->awayTeam;
    }

    public function setAwayTeam(?Team $awayTeam): self {
        $this->awayTeam = $awayTeam;

        return $this;
    }
}
