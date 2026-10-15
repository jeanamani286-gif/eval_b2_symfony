<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class ValidateController extends AbstractController
{
    #[Route('/validate', name: 'app_validate')]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('validate/index.html.twig', [
            'events' => $eventRepository->findNotValidated(),
        ]);
    }

    #[Route('/validate/{id}', name: 'app_validate_event', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function validateEvent(Event $event, EntityManagerInterface $entityManager): Response
    {
        $event->setValidated(true);
        $entityManager->flush();

        $this->addFlash('success', 'Événement validé.');

        return $this->redirectToRoute('app_validate');
    }
}