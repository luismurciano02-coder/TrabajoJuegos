<?php
namespace App\Entity;

use App\Repository\AppTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppTokenRepository::class)]
class AppToken {
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $nombreJuego = null; 

    #[ORM\Column(length: 64, unique: true)]
    private ?string $token = null; 


    public function getId(): ?int { return $this->id; }
    public function getNombreJuego(): ?string { return $this->nombreJuego; }
    public function setNombreJuego(string $nombreJuego): self { 
        $this->nombreJuego = $nombreJuego; 
        return $this; 
    }
    public function getToken(): ?string { return $this->token; }
    public function setToken(string $token): self { 
        $this->token = $token; 
        return $this; 
    }
}