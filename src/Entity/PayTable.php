<?php

namespace App\Entity;

use App\Repository\PayTableRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PayTableRepository::class)
 * @ORM\Table(name="pay_tables")
 */
class PayTable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date_immutable", name="start_date")
     */
    private $startDate;

    /**
     * @ORM\Column(type="date_immutable", name="end_date", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(type="json", name="pay_table")
     */
    private $payTable = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getPayTable(): ?array
    {
        return $this->payTable;
    }

    public function setPayTable(array $payTable): self
    {
        $this->payTable = $payTable;

        return $this;
    }
}
