<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
class Vehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(length: 1024)]
    private ?string $image = null;

    #[ORM\Column(length: 50)]
    private ?string $etat = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $masse = null;

    #[ORM\Column]
    private ?int $indice_maintenance = null;

    #[ORM\ManyToOne(inversedBy: 'vehicules')]
    private ?Entrepot $entrepot = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'id_vehicule')]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMasse(): ?int
    {
        return $this->masse;
    }

    public function setMasse(int $masse): static
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

    public function getEntrepot(): ?Entrepot
    {
        return $this->entrepot;
    }

    public function setEntrepot(?Entrepot $entrepot): static
    {
        $this->entrepot = $entrepot;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setIdVehicule($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getIdVehicule() === $this) {
                $reservation->setIdVehicule(null);
            }
        }

        return $this;
    }

    public function findTanksEnAlerte(): array
{
    
    $conn = $this->getEntityManager()->getConnection();

    $sql = '
        SELECT v.type, v.indice_maintenance, e.adresse 
        FROM vehicule v
        INNER JOIN entrepot e ON v.id_entrepot = e.id
        WHERE v.indice_maintenance < 50
        ORDER BY v.indice_maintenance ASC
    ';

    $resultSet = $conn->executeQuery($sql);
    return $resultSet->fetchAllAssociative();
}
}
