<?php

namespace App\Controller;

use App\Entity\Juegos;
use App\Form\JuegosType;
use App\Repository\JuegosRepository;
use App\Repository\PuntuacionesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/juegos')]
final class JuegosController extends AbstractController
{
    #[Route(name: 'app_juegos_index', methods: ['GET'])]
    public function index(JuegosRepository $juegosRepository): Response
    {
        return $this->render('juegos/index.html.twig', [
            'juegos' => $juegosRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_juegos_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $juego = new Juegos();
        $form = $this->createForm(JuegosType::class, $juego);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($juego);
            $entityManager->flush();

            return $this->redirectToRoute('app_juegos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('juegos/new.html.twig', [
            'juego' => $juego,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/play', name: 'app_juegos_play', methods: ['GET'])]
    public function play(Juegos $juego): Response
    {
        return $this->render('juegos/play.html.twig', [
            'juego' => $juego,
        ]);
    }

    #[Route('/{id}', name: 'app_juegos_show', methods: ['GET'])]
    #[Route('/{id}', name: 'app_juegos_show', methods: ['GET'])]
    public function show(Juegos $juego, PuntuacionesRepository $puntuacionesRepository): Response
    {
        // Obtener las puntuaciones del juego, ordenadas por puntuación descendente
        $puntuaciones = $puntuacionesRepository->findBy(
            ['juego' => $juego],
            ['puntuacion' => 'DESC']
        );

        return $this->render('juegos/show.html.twig', [
            'juego' => $juego,
            'puntuaciones' => $puntuaciones,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_juegos_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Juegos $juego, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(JuegosType::class, $juego);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_juegos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('juegos/edit.html.twig', [
            'juego' => $juego,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_juegos_delete', methods: ['POST'])]
    public function delete(Request $request, Juegos $juego, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$juego->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($juego);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_juegos_index', [], Response::HTTP_SEE_OTHER);
    }
}
