<?php

namespace App\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EventController extends AbstractController
{
    #[Route('/evenement/ajouter', name: 'app_event_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $newName = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('kernel.project_dir') . '/public/uploads', $newName);
                $event->setImage($newName);
            }

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Événement ajouté, il sera visible après validation.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('event/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/evenement/{id}', name: 'app_event_show', requirements: ['id' => '\d+'])]
    public function show(int $id, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Événement introuvable.');
        }

        $seats = $event->getSeats();
        $taken = $event->getParticipants()->count();
        $remaining = max(0, $seats - $taken);
        $percent = $seats > 0 ? min(100, (int) round($taken / $seats * 100)) : 0;
        $user = $this->getUser();

        return $this->render('event/show.html.twig', [
            'event' => $event,
            'remaining' => $remaining,
            'isFull' => $remaining === 0,
            'percent' => $percent,
            'isRegistered' => $user !== null && $event->getParticipants()->contains($user),
        ]);
    }

    #[Route('/evenement/{id}/inscription', name: 'app_event_register', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function register(Event $event, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($event->getParticipants()->contains($user)) {
            $this->addFlash('error', 'Vous êtes déjà inscrit à cet événement.');
        } elseif ($event->getParticipants()->count() >= $event->getSeats()) {
            $this->addFlash('error', 'Cet événement est complet.');
        } else {
            $event->addParticipant($user);
            $entityManager->flush();
            $this->addFlash('success', 'Vous êtes inscrit à cet événement.');
        }

        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
    }

        #[Route('/evenement/{id}/desinscription', name: 'app_event_unregister', requirements: ['id' => '\d+'], methods: ['POST'])]
        #[IsGranted('ROLE_USER')]
        public function unregister(Event $event, EntityManagerInterface $entityManager): Response
        {
            $event->removeParticipant($this->getUser());
            $entityManager->flush();

            $this->addFlash('success', 'Vous êtes désinscrit de cet événement.');

            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
        }
}