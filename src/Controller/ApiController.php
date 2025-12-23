<?php

namespace App\Controller;

use App\Entity\Aplicaciones;
use App\Repository\AplicacionesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

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
}
