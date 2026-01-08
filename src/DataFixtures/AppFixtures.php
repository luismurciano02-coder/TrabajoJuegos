<?php

namespace App\DataFixtures;

use App\Entity\Aplicaciones;
use App\Entity\Juegos;
use App\Entity\Puntuaciones;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        // Crear aplicación
        $app = new Aplicaciones();
        $app->setNombre('GameCenter');
        $app->setActivo(true);
        $app->setApikey(bin2hex(random_bytes(16)));
        $manager->persist($app);

        // Crear 4 juegos
        $juego1 = new Juegos();
        $juego1->setNombre('❌⭕ Tres en Raya');
        $juego1->setActivo(true);
        $juego1->setToken(bin2hex(random_bytes(16)));
        $juego1->setAplicacion($app);
        $manager->persist($juego1);

        $juego2 = new Juegos();
        $juego2->setNombre('🏓 Pong');
        $juego2->setActivo(true);
        $juego2->setToken(bin2hex(random_bytes(16)));
        $juego2->setAplicacion($app);
        $manager->persist($juego2);

        $juego3 = new Juegos();
        $juego3->setNombre('🐍 Snake');
        $juego3->setActivo(true);
        $juego3->setToken(bin2hex(random_bytes(16)));
        $juego3->setAplicacion($app);
        $manager->persist($juego3);

        $juego4 = new Juegos();
        $juego4->setNombre('🧱 Tetris');
        $juego4->setActivo(true);
        $juego4->setToken(bin2hex(random_bytes(16)));
        $juego4->setAplicacion($app);
        $manager->persist($juego4);

        // Crear usuarios de prueba
        $usuarios = [];
        $nombres = ['Carlos', 'María', 'Juan', 'Andrea', 'Luis', 'Sofia', 'Pedro', 'Laura'];
        
        foreach ($nombres as $nombre) {
            $user = new User();
            $user->setEmail(strtolower($nombre) . '@gamecenter.com');
            $user->setNombre($nombre);
            $user->setRoles(['ROLE_USER']);
            $user->setActivo(true);
            $user->setToken(bin2hex(random_bytes(16)));
            
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password123');
            $user->setPassword($hashedPassword);
            
            $manager->persist($user);
            $usuarios[] = $user;
        }

        $manager->flush();

        // Crear puntuaciones de prueba
        $juegos = [$juego1, $juego2, $juego3, $juego4];
        $puntuaciones = [150, 200, 180, 220, 190, 210, 170, 240, 160, 195, 215, 185];

        foreach ($usuarios as $user) {
            foreach ($juegos as $juego) {
                for ($i = 0; $i < 2; $i++) {
                    $puntuacion = new Puntuaciones();
                    $puntuacion->setUser($user);
                    $puntuacion->setJuego($juego);
                    $puntuacion->setPuntuacion($puntuaciones[array_rand($puntuaciones)]);
                    $puntuacion->setFecha(new \DateTime('-' . rand(1, 30) . ' days'));
                    $manager->persist($puntuacion);
                }
            }
        }

        $manager->flush();
    }
}
