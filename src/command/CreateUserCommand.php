<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-user', description: 'Crea un usuario con password hasheada')]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email del usuario')
            ->addArgument('password', InputArgument::REQUIRED, 'Password en texto plano')
            ->addOption('role', null, InputOption::VALUE_OPTIONAL, 'Rol a asignar', 'ROLE_ADMIN')
            ->addOption('nombre', null, InputOption::VALUE_OPTIONAL, 'Nombre visible', 'Admin')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (string) $input->getArgument('email');
        $plainPassword = (string) $input->getArgument('password');
        $role = (string) $input->getOption('role');
        $nombre = (string) $input->getOption('nombre');

        $user = new User();
        $user->setEmail($email);
        $user->setNombre($nombre);
        $user->setRoles([$role]);
        $user->setActivo(true);
        $user->setToken(bin2hex(random_bytes(20)));

        $hashed = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashed);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('Usuario creado: '.$email.' con rol '.$role);

        return Command::SUCCESS;
    }
}
