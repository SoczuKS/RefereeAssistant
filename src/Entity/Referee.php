<?php

namespace App\Entity;

use App\Repository\RefereeRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RefereeRepository::class)
 * @ORM\Table(name="referees")
 */
class Referee
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $surname;

    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private $birthdate;

    /**
     * @ORM\OneToMany(targetEntity=GameReferee::class, mappedBy="referee")
     */
    private $matchesRefereed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getBirthdate(): ?DateTimeImmutable
    {
        return $this->birthdate;
    }

    public function setBirthdate(DateTimeImmutable $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }
}
