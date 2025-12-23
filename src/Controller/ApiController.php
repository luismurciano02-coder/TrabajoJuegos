<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ApiController extends AbstractController
{
    private const VALID_API_KEY = 'tu_api_key_secreta_aqui';

    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    #[Route('/api/conexion', name: 'app_api_conexion', methods: ['GET', 'POST'])]
    public function conexion(Request $request): JsonResponse
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

            // Validar API-KEY
            if ($apiKey !== self::VALID_API_KEY) {
                return $this->json([
                    'success' => false,
                    'message' => 'Conexión no permitida, API-KEY inválida',
                    'data' => ''
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Generar token JWT simple
            $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
            $payload = base64_encode(json_encode(['iat' => time(), 'exp' => time() + 3600]));
            $signature = hash_hmac('sha256', $header . '.' . $payload, 'tu-clave-secreta', true);
            $signature = base64_encode($signature);
            $token = $header . '.' . $payload . '.' . $signature;

            return $this->json([
                'success' => true,
                'message' => 'Conexión exitosa',
                'data' => [
                    'api_key' => self::VALID_API_KEY,
                    'access_token' => $token
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error en la conexión',
                'data' => ''
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
