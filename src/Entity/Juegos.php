<?php

namespace App\Entity;

use App\Repository\JuegosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JuegosRepository::class)]
class Juegos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $nombre = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activo = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    /**
     * @var Collection<int, Puntuaciones>
     */
    #[ORM\OneToMany(targetEntity: Puntuaciones::class, mappedBy: 'juego')]
    private Collection $puntuacion;

    #[ORM\ManyToOne(inversedBy: 'jugando')]
    private ?Aplicaciones $aplicacion = null;

    public function __construct()
    {
        $this->puntuacion = new ArrayCollection();
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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Collection<int, Puntuaciones>
     */
    public function getPuntuacion(): Collection
    {
        return $this->puntuacion;
    }

    public function addPuntuacion(Puntuaciones $puntuacion): static
    {
        if (!$this->puntuacion->contains($puntuacion)) {
            $this->puntuacion->add($puntuacion);
            $puntuacion->setJuego($this);
        }

        return $this;
    }

    public function removePuntuacion(Puntuaciones $puntuacion): static
    {
        if ($this->puntuacion->removeElement($puntuacion)) {
            // set the owning side to null (unless already changed)
            if ($puntuacion->getJuego() === $this) {
                $puntuacion->setJuego(null);
            }
        }

        return $this;
    }

    public function getAplicacion(): ?Aplicaciones
    {
        return $this->aplicacion;
    }

    public function setAplicacion(?Aplicaciones $aplicacion): static
    {
        $this->aplicacion = $aplicacion;

        return $this;
    }

}
