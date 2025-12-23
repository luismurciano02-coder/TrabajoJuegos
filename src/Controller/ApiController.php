<?php

namespace App\Controller;

use App\Entity\Aplicaciones;
use App\Repository\AplicacionesRepository;
use App\Repository\UserRepository;
use App\Repository\JuegosRepository;
use App\Repository\PuntuacionesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    #[Route('/api/conexion', name: 'app_api_conexion', methods: ['GET', 'POST'])]
    public function conexion(Request $request, AplicacionesRepository $aplicacionesRepository): JsonResponse
    {
        try {
            // Obtener datos del request
            $data = json_decode($request->getContent(), true);

            if (!isset($data['api_key'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Conexión no permitida, API-KEY inválida',
                    'data' => ''
                ], Response::HTTP_UNAUTHORIZED);
            }

            $apiKey = $data['api_key'];

            // Validar API-KEY contra la base de datos
            $aplicacion = $aplicacionesRepository->findOneBy(['apikey' => $apiKey, 'activo' => true]);
            
            if (!$aplicacion) {
                return $this->json([
                    'success' => false,
                    'message' => 'Conexión no permitida, API-KEY inválida',
                    'data' => ''
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Generar token JWT simple
            $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
            $payload = base64_encode(json_encode([
                'iat' => time(), 
                'exp' => time() + 3600,
                'app_id' => $aplicacion->getId(),
                'app_name' => $aplicacion->getNombre()
            ]));
            $signature = hash_hmac('sha256', $header . '.' . $payload, 'tu-clave-secreta', true);
            $signature = base64_encode($signature);
            $token = $header . '.' . $payload . '.' . $signature;

            return $this->json([
                'success' => true,
                'message' => 'Conexión exitosa',
                'data' => [
                    'api_key' => $apiKey,
                    'access_token' => $token,
                    'aplicacion' => $aplicacion->getNombre()
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error en la conexión: ' . $e->getMessage(),
                'data' => ''
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/login', name: 'app_api_login', methods: ['POST'])]
    public function login(
        Request $request, 
        AplicacionesRepository $aplicacionesRepository,
        UserRepository $userRepository,
        JuegosRepository $juegosRepository,
        PuntuacionesRepository $puntuacionesRepository,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        try {
            // Obtener datos del request
            $data = json_decode($request->getContent(), true);

            // Validar que se envíen todos los campos requeridos
            if (!isset($data['api_key']) || !isset($data['usuario']) || !isset($data['password'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Faltan datos requeridos (api_key, usuario, password)',
                    'data' => ''
                ], Response::HTTP_BAD_REQUEST);
            }

            $apiKey = $data['api_key'];
            $email = $data['usuario'];
            $password = $data['password'];

            // Validar API-KEY contra la base de datos
            $aplicacion = $aplicacionesRepository->findOneBy(['apikey' => $apiKey]);
            
            if (!$aplicacion) {
                return $this->json([
                    'success' => false,
                    'message' => 'Conexión no permitida, API-KEY inválida',
                    'data' => ''
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Buscar el usuario por email (sin validar activo primero)
            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado',
                    'data' => ''
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Verificar la contraseña (soporta hash y texto plano)
            $passwordValida = false;
            
            // Primero intentar con password hasheada
            try {
                if ($passwordHasher->isPasswordValid($user, $password)) {
                    $passwordValida = true;
                }
            } catch (\Exception $e) {
                // Si falla el hash, ignorar y probar texto plano
            }
            
            // Si no funciona, comparar directamente (para contraseñas en texto plano)
            if (!$passwordValida && $user->getPassword() === $password) {
                $passwordValida = true;
            }

            if (!$passwordValida) {
                return $this->json([
                    'success' => false,
                    'message' => 'Contraseña incorrecta',
                    'data' => [
                        'debug' => 'Password en BD: ' . substr($user->getPassword(), 0, 20) . '...',
                        'debug2' => 'Password enviada: ' . $password
                    ]
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Obtener juegos de la aplicación
            $juegos = $juegosRepository->findBy(['aplicacion' => $aplicacion]);

            $listadoJuegos = [];
            foreach ($juegos as $juego) {
                // Contar las partidas del usuario en este juego
                $partidas = $puntuacionesRepository->count(['user' => $user, 'juego' => $juego]);

                $listadoJuegos[] = [
                    'juego' => $juego->getNombre(),
                    'Partidas' => (string)$partidas,
                    'token' => $juego->getToken()
                ];
            }

            return $this->json([
                'success' => true,
                'message' => 'Usuario válido',
                'data' => [
                    'usuario_token' => $user->getToken(),
                    'Listado juegos' => $listadoJuegos
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error en el login: ' . $e->getMessage(),
                'data' => ''
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/registro', name: 'app_api_registro', methods: ['POST'])]
    public function registro(
        Request $request,
        AplicacionesRepository $aplicacionesRepository,
        UserRepository $userRepository,
        JuegosRepository $juegosRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        try {
            // Obtener datos del request
            $content = $request->getContent();
            $data = json_decode($content, true);

            // Debug: verificar si el JSON se parseó correctamente
            if ($data === null) {
                return $this->json([
                    'success' => false,
                    'message' => 'Error en el registro del nuevo usuario',
                    'data' => 'JSON inválido. Verifica el formato del Body.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Validar que se envíen todos los campos requeridos
            $camposFaltantes = [];
            if (!isset($data['api_key'])) $camposFaltantes[] = 'api_key';
            if (!isset($data['nombre'])) $camposFaltantes[] = 'nombre';
            if (!isset($data['email'])) $camposFaltantes[] = 'email';
            if (!isset($data['password'])) $camposFaltantes[] = 'password';

            if (count($camposFaltantes) > 0) {
                return $this->json([
                    'success' => false,
                    'message' => 'Error en el registro del nuevo usuario',
                    'data' => 'Faltan campos: ' . implode(', ', $camposFaltantes) . '. Datos recibidos: ' . json_encode(array_keys($data))
                ], Response::HTTP_BAD_REQUEST);
            }

            $apiKey = $data['api_key'];
            $nombre = $data['nombre'];
            $email = $data['email'];
            $password = $data['password'];

            // Validar API-KEY contra la base de datos
            $aplicacion = $aplicacionesRepository->findOneBy(['apikey' => $apiKey]);
            
            if (!$aplicacion) {
                return $this->json([
                    'success' => false,
                    'message' => 'Error en el registro del nuevo usuario',
                    'data' => 'API-KEY inválida'
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Verificar si el email ya existe
            $usuarioExistente = $userRepository->findOneBy(['email' => $email]);
            
            if ($usuarioExistente) {
                return $this->json([
                    'success' => false,
                    'message' => 'Error en el registro del nuevo usuario',
                    'data' => 'La cuenta ya está dada de alta'
                ], Response::HTTP_CONFLICT);
            }

            // Crear el nuevo usuario
            $user = new \App\Entity\User();
            $user->setEmail($email);
            $user->setNombre($nombre);
            $user->setActivo(true);
            
            // Hashear la contraseña
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
            
            // Generar token único para el usuario
            $userToken = bin2hex(random_bytes(20));
            $user->setToken($userToken);
            
            // Guardar el usuario en la base de datos
            $entityManager->persist($user);
            $entityManager->flush();

            // Obtener juegos de la aplicación
            $juegos = $juegosRepository->findBy(['aplicacion' => $aplicacion]);

            $listadoJuegos = [];
            foreach ($juegos as $juego) {
                $listadoJuegos[] = [
                    'juego' => $juego->getNombre(),
                    'Partidas' => '0',
                    'token' => $juego->getToken()
                ];
            }

            return $this->json([
                'success' => true,
                'message' => 'Usuario registrado',
                'data' => [
                    'usuario_token' => $user->getToken(),
                    'Listado juegos' => $listadoJuegos
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error en el registro del nuevo usuario',
                'data' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/juego', name: 'app_api_juego', methods: ['POST'])]
    public function juego(
        Request $request,
        AplicacionesRepository $aplicacionesRepository,
        UserRepository $userRepository,
        JuegosRepository $juegosRepository,
        PuntuacionesRepository $puntuacionesRepository
    ): JsonResponse
    {
        try {
            // Obtener datos del request
            $content = $request->getContent();
            $data = json_decode($content, true);

            // Validar JSON
            if ($data === null) {
                return $this->json([
                    'success' => false,
                    'message' => 'Error, no hay registro de ese juego',
                    'data' => 'JSON inválido'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Validar campos requeridos
            $camposFaltantes = [];
            if (!isset($data['api_key'])) $camposFaltantes[] = 'api_key';
            if (!isset($data['token_usuario'])) $camposFaltantes[] = 'token_usuario';
            if (!isset($data['token_juego'])) $camposFaltantes[] = 'token_juego';

            if (count($camposFaltantes) > 0) {
                return $this->json([
                    'success' => false,
                    'message' => 'Error, no hay registro de ese juego',
                    'data' => 'Faltan campos: ' . implode(', ', $camposFaltantes)
                ], Response::HTTP_BAD_REQUEST);
            }

            $apiKey = $data['api_key'];
            $tokenUsuario = $data['token_usuario'];
            $tokenJuego = $data['token_juego'];

            // Validar API-KEY
            $aplicacion = $aplicacionesRepository->findOneBy(['apikey' => $apiKey]);
            
            if (!$aplicacion) {
                return $this->json([
                    'success' => false,
                    'message' => 'Error, no hay registro de ese juego',
                    'data' => 'API-KEY inválida'
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Buscar usuario por token
            $user = $userRepository->findOneBy(['token' => $tokenUsuario]);
            
            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'Error, no hay registro de ese juego',
                    'data' => 'Token de usuario inválido'
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Buscar juego por token
            $juego = $juegosRepository->findOneBy(['token' => $tokenJuego, 'aplicacion' => $aplicacion]);
            
            if (!$juego) {
                return $this->json([
                    'success' => false,
                    'message' => 'Error, no hay registro de ese juego',
                    'data' => 'Token de juego inválido o no pertenece a la aplicación'
                ], Response::HTTP_NOT_FOUND);
            }

            // Obtener la mejor puntuación del usuario en este juego
            $puntuacionUsuario = $puntuacionesRepository->createQueryBuilder('p')
                ->where('p.user = :user')
                ->andWhere('p.juego = :juego')
                ->setParameter('user', $user)
                ->setParameter('juego', $juego)
                ->orderBy('p.puntuacion', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            $puntosUsuario = $puntuacionUsuario ? $puntuacionUsuario->getPuntuacion() : 0;

            // Obtener top 10 de jugadores (mejores puntuaciones únicas por usuario)
            $topJugadores = $puntuacionesRepository->createQueryBuilder('p')
                ->select('u.nombre as jugador, MAX(p.puntuacion) as puntos')
                ->join('p.user', 'u')
                ->where('p.juego = :juego')
                ->setParameter('juego', $juego)
                ->groupBy('u.id')
                ->orderBy('puntos', 'DESC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();

            $listadoJugadores = [];
            foreach ($topJugadores as $jugador) {
                $listadoJugadores[] = [
                    'jugador' => $jugador['jugador'],
                    'Puntos' => (string)$jugador['puntos']
                ];
            }

            return $this->json([
                'success' => true,
                'message' => 'Listado de resultados del juego',
                'data' => [
                    'Puntos usuario' => (string)$puntosUsuario,
                    'Listado jugadores' => $listadoJugadores
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error, no hay registro de ese juego',
                'data' => 'Error en el registro del juego: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
