<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
class Vehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_vehicule = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $etat = null;

    #[ORM\Column(nullable: true)]
    private ?int $masse = null;

    #[ORM\Column]
    private ?int $indice_maintenance = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_entrepot = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdVehicule(): ?int
    {
        return $this->id_vehicule;
    }

    public function setIdVehicule(int $id_vehicule): static
    {
        $this->id_vehicule = $id_vehicule;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getMasse(): ?int
    {
        return $this->masse;
    }

    public function setMasse(?int $masse): static
    {
        $this->masse = $masse;

        return $this;
    }

    public function getIndiceMaintenance(): ?int
    {
        return $this->indice_maintenance;
    }

    public function setIndiceMaintenance(int $indice_maintenance): static
    {
        $this->indice_maintenance = $indice_maintenance;

        return $this;
    }

    public function getIdEntrepot(): ?int
    {
        return $this->id_entrepot;
    }

    public function setIdEntrepot(?int $id_entrepot): static
    {
        $this->id_entrepot = $id_entrepot;

        return $this;
    }
}
