<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Service\Geocoder;
use App\Repository\EventRepository;
use App\Service\DistanceCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{

    private DistanceCalculator $distanceCalculator;

    public function __construct(DistanceCalculator $distanceCalculator)
    {
        $this->distanceCalculator = $distanceCalculator;
    }
    

    #[Route('/events/{id}/distance', name: 'event_distance', methods: ['GET'])]
    public function calculateDistanceToEvent(
        int $id,
        Request $request,
        EventRepository $eventRepository,
        Geocoder $geocoder,
        DistanceCalculator $distanceCalculator 
    ): Response {
        $userLat = $request->query->get('lat');
        $userLon = $request->query->get('lon');
    
    
        if (!$userLat || !$userLon) {
            return $this->json(['error' => 'Les paramètres lat et lon sont requis.'], 400);
        }
    
        // Récupérer l'événement par son ID
        $event = $eventRepository->find($id);
    
        if (!$event) {
            return $this->json(['error' => 'Événement introuvable.'], 404);
        }
    
        // Récupérer l'adresse de l'événement
        $address = $event->getLocation();
    
        // Service Geocoder pour convertir l'adresse en coordonnées
        $eventCoordinates = $geocoder->getCoordinates($address);
    
        if (!$eventCoordinates) {
            return $this->json(['error' => 'Impossible de convertir l\'adresse de l\'événement en coordonnées.'], 500);
        }
    
        // Calculer la distance entre l'utilisateur et l'événement
        $distance = $distanceCalculator->calculateDistance(
            (float) $userLat,
            (float) $userLon,
            (float) $eventCoordinates['latitude'],
            (float) $eventCoordinates['longitude']
        );
    
        // Retourner la distance en JSON
        return $this->json([
            'event' => $event->getName(),
            'address' => $address,
            'distance' => $distance,
            'unit' => 'km',
        ]);
    }


    #[Route('/events', name: 'app_events')]
    public function  listEvents(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();
        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
        
    }

    #[Route('/events/{id}', name: 'app_event')]
    public function viewEvent(int $id, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Événement introuvable.');
        }

        return $this->render('event/viewEvent.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/', name: 'app_add_event')]
    public function addEvent(
        Request $request,
        EntityManagerInterface $entityManager,
        EventRepository $eventRepository
    ): Response {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingEvent = $eventRepository->findOneBy([
                'name' => $event->getName(),
                'date' => $event->getDate()
            ]);

            if ($existingEvent) {
                $this->addFlash('error', 'Un événement avec ce nom et cette date existe déjà.');
                return $this->redirectToRoute('app_add_event');
            }

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été ajouté avec succès.');

            return $this->redirectToRoute('app_events');
        }

        return $this->render('event/addEvent.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
