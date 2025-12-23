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
            $aplicacion = $aplicacionesRepository->findOneBy(['apikey' => $apiKey, 'activo' => true]);
            
            if (!$aplicacion) {
                return $this->json([
                    'success' => false,
                    'message' => 'Conexión no permitida, API-KEY inválida',
                    'data' => ''
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Buscar el usuario por email y que esté activo
            $user = $userRepository->findOneBy(['email' => $email, 'activo' => true]);

            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'Usuario o contraseña incorrectos',
                    'data' => ''
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Verificar la contraseña (soporta hash y texto plano)
            $passwordValida = false;
            
            // Primero intentar con password hasheada
            if ($passwordHasher->isPasswordValid($user, $password)) {
                $passwordValida = true;
            } 
            // Si no funciona, comparar directamente (para contraseñas en texto plano)
            else if ($user->getPassword() === $password) {
                $passwordValida = true;
            }

            if (!$passwordValida) {
                return $this->json([
                    'success' => false,
                    'message' => 'Usuario o contraseña incorrectos',
                    'data' => ''
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Obtener juegos de la aplicación
            $juegos = $juegosRepository->findBy(['aplicacion' => $aplicacion, 'activo' => true]);

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
}
