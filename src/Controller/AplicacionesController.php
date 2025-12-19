<?php

namespace App\Controller;

use App\Entity\Aplicaciones;
use App\Form\AplicacionesType;
use App\Repository\AplicacionesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/aplicaciones')]
final class AplicacionesController extends AbstractController
{
    #[Route(name: 'app_aplicaciones_index', methods: ['GET'])]
    public function index(AplicacionesRepository $aplicacionesRepository): Response
    {
        return $this->render('aplicaciones/index.html.twig', [
            'aplicaciones' => $aplicacionesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_aplicaciones_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $aplicacione = new Aplicaciones();
        $form = $this->createForm(AplicacionesType::class, $aplicacione);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($aplicacione);
            $entityManager->flush();

            return $this->redirectToRoute('app_aplicaciones_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('aplicaciones/new.html.twig', [
            'aplicacione' => $aplicacione,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_aplicaciones_show', methods: ['GET'])]
    public function show(Aplicaciones $aplicacione): Response
    {
        return $this->render('aplicaciones/show.html.twig', [
            'aplicacione' => $aplicacione,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_aplicaciones_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Aplicaciones $aplicacione, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AplicacionesType::class, $aplicacione);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_aplicaciones_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('aplicaciones/edit.html.twig', [
            'aplicacione' => $aplicacione,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_aplicaciones_delete', methods: ['POST'])]
    public function delete(Request $request, Aplicaciones $aplicacione, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$aplicacione->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($aplicacione);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_aplicaciones_index', [], Response::HTTP_SEE_OTHER);
    }
}
