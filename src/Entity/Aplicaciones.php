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

    #[ORM\Column(length: 255, unique: true)]
    private ?string $apikey = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activo = null;

    /**
     * @var Collection<int, Juegos>
     */
    #[ORM\OneToMany(mappedBy: 'aplicacion', targetEntity: Juegos::class, cascade: ['persist'])]
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

    public function getApikey(): ?string
    {
        return $this->apikey;
    }

    public function setApikey(string $apikey): static
    {
        $this->apikey = $apikey;

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

    /**
     * @return Collection<int, Juegos>
     */
    public function getJugando(): Collection
    {
        return $this->jugando;
    }

    public function addJugando(Juegos $juego): static
    {
        if (!$this->jugando->contains($juego)) {
            $this->jugando->add($juego);
            $juego->setAplicacion($this);
        }

        return $this;
    }

    public function removeJugando(Juegos $juego): static
    {
        if ($this->jugando->removeElement($juego) && $juego->getAplicacion() === $this) {
            $juego->setAplicacion(null);
        }

        return $this;
    }
}