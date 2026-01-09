<?php

namespace App\DataFixtures;

use App\Entity\Aplicaciones;
use App\Entity\Juegos;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Aplicaciones demo
        $apps = [];
        foreach ([
            ['nombre' => 'App Juegos A', 'apikey' => 'app-token-1', 'activo' => true],
            ['nombre' => 'App Juegos B', 'apikey' => 'app-token-2', 'activo' => true],
        ] as $data) {
            $app = (new Aplicaciones())
                ->setNombre($data['nombre'])
                ->setApikey($data['apikey'])
                ->setActivo($data['activo']);

            $manager->persist($app);
            $apps[] = $app;
        }

        // Juegos demo asociados a las aplicaciones anteriores
        $games = [
            ['nombre' => 'Tetris', 'token' => 'game-token-2', 'activo' => true, 'appIndex' => 0],
            ['nombre' => 'Pacman', 'token' => 'game-token-3', 'activo' => true, 'appIndex' => 1],
            ['nombre' => 'Space Invaders', 'token' => 'game-token-4', 'activo' => true, 'appIndex' => 1],
            ['nombre' => 'Snake', 'token' => 'game-token-5', 'activo' => true, 'appIndex' => 0],
        ];

        foreach ($games as $data) {
            $game = (new Juegos())
                ->setNombre($data['nombre'])
                ->setToken($data['token'])
                ->setActivo($data['activo'])
                ->setAplicacion($apps[$data['appIndex']]);

            $manager->persist($game);
        }

        $manager->flush();
    }
}
