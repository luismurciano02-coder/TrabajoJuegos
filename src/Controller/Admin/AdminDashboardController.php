<?php
namespace App\Controller\Admin;

use App\Repository\AppTokenRepository;
use App\Repository\JugadaRepository; 
use App\Repository\UserRepository;   
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminDashboardController extends AbstractController {

    #[Route('/dashboard', name: 'admin_dashboard')]
    public function index(JugadaRepository $jugadaRepo, AppTokenRepository $appRepo): Response {
        
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/dashboard.html.twig', [
            'total_jugadas' => $jugadaRepo->count([]),
            'juegos_activos' => $appRepo->findAll(),
            'historico_completo' => $jugadaRepo->findAllSortedByDate(), 
        ]);
    }

    #[Route('/tokens', name: 'admin_tokens')]
    public function manageTokens(AppTokenRepository $appRepo): Response {

        return $this->render('admin/tokens.html.twig', [
            'tokens' => $appRepo->findAll(),
        ]);
    }
}