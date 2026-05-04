<?php

namespace App\Entity;

use App\Repository\EntretienRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntretienRepository::class)]
class Entretien
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_entretien = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_intervention = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $type_travaux = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_user = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_vehicule = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdEntretien(): ?int
    {
        return $this->id_entretien;
    }

    public function setIdEntretien(int $id_entretien): static
    {
        $this->id_entretien = $id_entretien;

        return $this;
    }

    public function getDateIntervention(): ?\DateTime
    {
        return $this->date_intervention;
    }

    public function setDateIntervention(\DateTime $date_intervention): static
    {
        $this->date_intervention = $date_intervention;

        return $this;
    }

    public function getTypeTravaux(): ?string
    {
        return $this->type_travaux;
    }

    public function setTypeTravaux(?string $type_travaux): static
    {
        $this->type_travaux = $type_travaux;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(?int $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getIdVehicule(): ?int
    {
        return $this->id_vehicule;
    }

    public function setIdVehicule(?int $id_vehicule): static
    {
        $this->id_vehicule = $id_vehicule;

        return $this;
    }
}
