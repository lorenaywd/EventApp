<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\EventRepository;
use App\Service\Geocoder; // Ajout du service Geocoder
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends AbstractController
{
    #[Route('/events/{eventId}/participants/new', name: 'app_add_participant')]
    public function addParticipant(
        int $eventId,
        EventRepository $eventRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        Geocoder $geocoder 
    ): Response {
        $event = $eventRepository->find($eventId);

        if (!$event) {
            throw $this->createNotFoundException('Événement introuvable.');
        }

        $participant = new Participant();
        $participant->setEvent($event);

        $form = $this->createForm(ParticipantType::class, $participant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de l'existence du participant dans l'événement
            $existingParticipants = $event->getParticipants();

            foreach ($existingParticipants as $existingParticipant) {
                if ($existingParticipant->getEmail() === $participant->getEmail()) {
                    $this->addFlash('error', 'Ce participant est déjà inscrit à cet événement.');
                    return $this->redirectToRoute('app_add_participant', ['eventId' => $eventId]);
                }
            }

            // Conversion de l'adresse en coordonnées GPS
            $address = $participant->getAddress();
            $coordinates = $geocoder->getCoordinates($address);

            if (!$coordinates) {
                // Gestion des erreurs de conversion d'adresse
                $this->addFlash('error', 'Adresse non valide ou non trouvée.');
                return $this->render('participant/add.html.twig', [
                    'form' => $form->createView(),
                    'event' => $event,
                ]);
            }

            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->render('participant/addSuccess.html.twig');
        }

        return $this->render('participant/add.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }
}

