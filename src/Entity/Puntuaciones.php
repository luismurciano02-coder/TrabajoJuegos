<?php

namespace App\Entity;

use App\Repository\PuntuacionesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PuntuacionesRepository::class)]
class Puntuaciones
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $puntuacion = null;

    #[ORM\Column]
    private ?\DateTime $fecha = null;

    #[ORM\ManyToOne(inversedBy: 'puntos')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'puntuacion')]
    private ?Juegos $juego = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPuntuacion(): ?int
    {
        return $this->puntuacion;
    }

    public function setPuntuacion(int $puntuacion): static
    {
        $this->puntuacion = $puntuacion;

        return $this;
    }

    public function getFecha(): ?\DateTime
    {
        return $this->fecha;
    }

    public function setFecha(\DateTime $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getJuego(): ?Juegos
    {
        return $this->juego;
    }

    public function setJuego(?Juegos $juego): static
    {
        $this->juego = $juego;

        return $this;
    }
}
