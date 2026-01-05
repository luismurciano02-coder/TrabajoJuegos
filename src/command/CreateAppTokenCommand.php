<?php
// src/Command/CreateAppTokenCommand.php
namespace App\Command;

use App\Entity\AppToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create-token', description: 'Crea un token para un juego nuevo')]
class CreateAppTokenCommand extends Command {
    public function __construct(private EntityManagerInterface $entityManager) {
        parent::__construct();
    }

    protected function configure(): void {
        $this->addArgument('nombre', InputArgument::REQUIRED, 'Nombre del juego');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $nombre = $input->getArgument('nombre');
        
        // Generamos un token aleatorio seguro
        $tokenString = bin2hex(random_bytes(20)); 

        $appToken = new AppToken();
        $appToken->setNombreJuego($nombre);
        $appToken->setToken($tokenString);

        $this->entityManager->persist($appToken);
        $this->entityManager->flush();

        $output->writeln("Token creado para $nombre: <info>$tokenString</info>");
        return Command::SUCCESS;
    }
}