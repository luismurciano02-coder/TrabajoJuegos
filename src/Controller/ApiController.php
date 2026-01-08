<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Juegos;
use App\Entity\Puntuaciones;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api')]
class ApiController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    // Función auxiliar para generar token
    private function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    // ==================== AUTENTICACIÓN ====================

    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password']) || !isset($data['nombre'])) {
            return new JsonResponse(['error' => 'Email, password y nombre son requeridos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Verificar si el usuario ya existe
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'El usuario ya existe'], JsonResponse::HTTP_CONFLICT);
        }

        // Crear nuevo usuario
        $user = new User();
        $user->setEmail($data['email']);
        $user->setNombre($data['nombre']);
        $user->setRoles(['ROLE_USER']);
        $user->setActivo(true);
        $user->setToken($this->generateToken());

        // Hash de la contraseña
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'nombre' => $user->getNombre(),
            'token' => $user->getToken()
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email y password son requeridos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Buscar usuario por email
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (!$user) {
            return new JsonResponse(['error' => 'Credenciales inválidas'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Verificar la contraseña
        if (!$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['error' => 'Credenciales inválidas'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Verificar que el usuario esté activo
        if (!$user->isActivo()) {
            return new JsonResponse(['error' => 'Usuario inactivo'], JsonResponse::HTTP_FORBIDDEN);
        }

        // Generar nuevo token si no existe
        if (!$user->getToken()) {
            $user->setToken($this->generateToken());
        }

        // Guardar cambios si se creó o actualizó
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'nombre' => $user->getNombre(),
            'token' => $user->getToken()
        ]);
    }

    // ==================== JUEGOS ====================

    #[Route('/juegos', name: 'api_juegos_list', methods: ['GET'])]
    public function getJuegos(): JsonResponse
    {
        $juegos = $this->entityManager->getRepository(Juegos::class)->findBy(['activo' => true]);

        $data = array_map(fn($juego) => [
            'id' => $juego->getId(),
            'nombre' => $juego->getNombre(),
            'token' => $juego->getToken(),
            'activo' => $juego->isActivo()
        ], $juegos);

        return new JsonResponse($data);
    }

    // ==================== PUNTUACIONES ====================

    #[Route('/puntuaciones', name: 'api_puntuaciones_save', methods: ['POST'])]
    public function savePuntuacion(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['token']) || !isset($data['juego_id']) || !isset($data['puntuacion'])) {
            return new JsonResponse(['error' => 'Token, juego_id y puntuacion son requeridos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Validar token del usuario
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $data['token']]);
        if (!$user) {
            return new JsonResponse(['error' => 'Token inválido'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Verificar que el juego existe
        $juego = $this->entityManager->getRepository(Juegos::class)->find($data['juego_id']);
        if (!$juego) {
            return new JsonResponse(['error' => 'Juego no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Guardar puntuación
        $puntuacion = new Puntuaciones();
        $puntuacion->setUser($user);
        $puntuacion->setJuego($juego);
        $puntuacion->setPuntuacion((int) $data['puntuacion']);
        $puntuacion->setFecha(new \DateTime());

        $this->entityManager->persist($puntuacion);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $puntuacion->getId(),
            'puntuacion' => $puntuacion->getPuntuacion(),
            'fecha' => $puntuacion->getFecha()->format('Y-m-d H:i:s')
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/puntuaciones/usuario/{token}', name: 'api_puntuaciones_usuario', methods: ['GET'])]
    public function getPuntuacionesUsuario(string $token): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
        
        if (!$user) {
            return new JsonResponse([]);
        }

        $puntuaciones = $this->entityManager->getRepository(Puntuaciones::class)->findBy(['user' => $user]);

        $data = array_map(fn($p) => [
            'id' => $p->getId(),
            'juego' => $p->getJuego()->getNombre(),
            'puntuacion' => $p->getPuntuacion(),
            'fecha' => $p->getFecha()->format('Y-m-d H:i:s')
        ], $puntuaciones);

        return new JsonResponse($data);
    }

    #[Route('/ranking/general', name: 'api_ranking_general', methods: ['GET'])]
    public function getRankingGeneral(): JsonResponse
    {
        $puntuaciones = $this->entityManager->getRepository(Puntuaciones::class)
            ->createQueryBuilder('p')
            ->orderBy('p.puntuacion', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        $data = array_map(fn($p) => [
            'usuario' => $p->getUser()->getNombre(),
            'juego' => $p->getJuego()->getNombre(),
            'puntuacion' => $p->getPuntuacion(),
            'fecha' => $p->getFecha()->format('Y-m-d H:i:s')
        ], $puntuaciones);

        return new JsonResponse($data);
    }

    #[Route('/ranking/{juego_id}', name: 'api_ranking_juego', methods: ['GET'])]
    public function getRankingJuego(int $juego_id): JsonResponse
    {
        $juego = $this->entityManager->getRepository(Juegos::class)->find($juego_id);
        if (!$juego) {
            return new JsonResponse(['error' => 'Juego no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        $puntuaciones = $this->entityManager->getRepository(Puntuaciones::class)
            ->createQueryBuilder('p')
            ->where('p.juego = :juego')
            ->setParameter('juego', $juego)
            ->orderBy('p.puntuacion', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        $data = array_map(fn($p) => [
            'usuario' => $p->getUser()->getNombre(),
            'puntuacion' => $p->getPuntuacion(),
            'fecha' => $p->getFecha()->format('Y-m-d H:i:s')
        ], $puntuaciones);

        return new JsonResponse($data);
    }

    // ==================== USUARIO ====================

    #[Route('/usuario/{token}', name: 'api_usuario_get', methods: ['GET'])]
    public function getUsuario(string $token): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
        if (!$user) {
            return new JsonResponse(['error' => 'Token inválido'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'nombre' => $user->getNombre(),
            'activo' => $user->isActivo()
        ]);
    }

    #[Route('/usuario/{token}', name: 'api_usuario_update', methods: ['PUT'])]
    public function updateUsuario(string $token, Request $request): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
        if (!$user) {
            return new JsonResponse(['error' => 'Token inválido'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nombre'])) {
            $user->setNombre($data['nombre']);
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'nombre' => $user->getNombre(),
            'activo' => $user->isActivo()
        ]);
    }

    // ==================== DOCUMENTACIÓN ====================

    #[Route('', name: 'app_api', methods: ['GET'])]
    public function index(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
}
