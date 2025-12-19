<?php

namespace App\Controller;

use App\Entity\Puntuaciones;
use App\Form\PuntuacionesType;
use App\Repository\PuntuacionesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/puntuaciones')]
final class PuntuacionesController extends AbstractController
{
    #[Route(name: 'app_puntuaciones_index', methods: ['GET'])]
    public function index(PuntuacionesRepository $puntuacionesRepository): Response
    {
        return $this->render('puntuaciones/index.html.twig', [
            'puntuaciones' => $puntuacionesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_puntuaciones_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $puntuacione = new Puntuaciones();
        $form = $this->createForm(PuntuacionesType::class, $puntuacione);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($puntuacione);
            $entityManager->flush();

            return $this->redirectToRoute('app_puntuaciones_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('puntuaciones/new.html.twig', [
            'puntuacione' => $puntuacione,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_puntuaciones_show', methods: ['GET'])]
    public function show(Puntuaciones $puntuacione): Response
    {
        return $this->render('puntuaciones/show.html.twig', [
            'puntuacione' => $puntuacione,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_puntuaciones_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Puntuaciones $puntuacione, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PuntuacionesType::class, $puntuacione);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_puntuaciones_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('puntuaciones/edit.html.twig', [
            'puntuacione' => $puntuacione,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_puntuaciones_delete', methods: ['POST'])]
    public function delete(Request $request, Puntuaciones $puntuacione, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$puntuacione->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($puntuacione);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_puntuaciones_index', [], Response::HTTP_SEE_OTHER);
    }
}
