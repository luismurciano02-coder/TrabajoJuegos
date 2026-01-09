<?php

namespace App\Repository;

use App\Entity\AppToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AppTokenRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, AppToken::class);
    }

    public function findByToken(string $token): ?AppToken {
        return $this->findOneBy(['token' => $token]);
    }
}