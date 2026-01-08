<?php
namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\AppToken;
use App\Repository\AppTokenRepository;
use App\Repository\PuntuacionesRepository; 
use App\Repository\UserRepository;   
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminDashboardController extends AbstractController {

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    private function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    #[Route('/dashboard', name: 'admin_dashboard')]
    public function index(PuntuacionesRepository $puntuacionesRepo, AppTokenRepository $appRepo): Response {
        
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/dashboard.html.twig', [
            'total_jugadas' => $puntuacionesRepo->count([]),
            'juegos_activos' => $appRepo->findAll(),
            'historico_completo' => $puntuacionesRepo->findAll(), 
        ]);
    }

    #[Route('/tokens', name: 'admin_tokens')]
    public function manageTokens(AppTokenRepository $appRepo): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/tokens.html.twig', [
            'tokens' => $appRepo->findAll(),
        ]);
    }

    #[Route('/usuarios', name: 'admin_usuarios')]
    public function listarUsuarios(UserRepository $userRepo): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $usuarios = $userRepo->findAll();

        return $this->render('admin/usuarios.html.twig', [
            'usuarios' => $usuarios,
        ]);
    }

    #[Route('/usuarios/crear', name: 'admin_usuario_crear', methods: ['POST'])]
    public function crearUsuario(Request $request, UserRepository $userRepo): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = $request->request->all();

        // Validar datos
        if (empty($data['email']) || empty($data['nombre']) || empty($data['password'])) {
            $usuarios = $userRepo->findAll();
            return $this->render('admin/usuarios.html.twig', [
                'usuarios' => $usuarios,
                'error' => 'Todos los campos son requeridos',
            ]);
        }

        // Verificar que el email no exista
        $existingUser = $userRepo->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            $usuarios = $userRepo->findAll();
            return $this->render('admin/usuarios.html.twig', [
                'usuarios' => $usuarios,
                'error' => 'El email ya está registrado',
            ]);
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

        return $this->redirectToRoute('admin_usuarios');
    }

    #[Route('/aplicaciones/crear', name: 'admin_app_crear', methods: ['POST'])]
    public function crearAplicacion(Request $request, AppTokenRepository $appRepo): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = $request->request->all();

        // Validar datos
        if (empty($data['nombre'])) {
            return $this->redirectToRoute('admin_tokens');
        }

        // Verificar que el nombre no exista
        $existingApp = $appRepo->findOneBy(['nombreJuego' => $data['nombre']]);
        if ($existingApp) {
            return $this->redirectToRoute('admin_tokens');
        }

        // Crear nueva aplicación
        $appToken = new AppToken();
        $appToken->setNombreJuego($data['nombre']);
        $appToken->setToken($this->generateToken());

        $this->entityManager->persist($appToken);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_tokens');
    }
}