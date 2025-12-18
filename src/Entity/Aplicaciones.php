<?php

namespace App\Entity;

use App\Repository\AplicacionesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AplicacionesRepository::class)]
class Aplicaciones
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $nombre = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $apikey = null;

    /**
     * @var Collection<int, Juegos>
     */
    #[ORM\OneToMany(targetEntity: Juegos::class, mappedBy: 'aplicacion')]
    private Collection $jugando;

    public function __construct()
    {
        $this->jugando = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function isActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(?bool $activo): static
    {
        $this->activo = $activo;

        return $this;
    }

    public function getApikey(): ?string
    {
        return $this->apikey;
    }

    public function setApikey(?string $apikey): static
    {
        $this->apikey = $apikey;

        return $this;
    }

    /**
     * @return Collection<int, Juegos>
     */
    public function getJugando(): Collection
    {
        return $this->jugando;
    }

    public function addJugando(Juegos $jugando): static
    {
        if (!$this->jugando->contains($jugando)) {
            $this->jugando->add($jugando);
            $jugando->setAplicacion($this);
        }

        return $this;
    }

    public function removeJugando(Juegos $jugando): static
    {
        if ($this->jugando->removeElement($jugando)) {
            // set the owning side to null (unless already changed)
            if ($jugando->getAplicacion() === $this) {
                $jugando->setAplicacion(null);
            }
        }

        return $this;
    }
}
