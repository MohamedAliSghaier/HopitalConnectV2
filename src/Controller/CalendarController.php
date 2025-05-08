<?php

namespace App\Controller;

use App\Repository\RendezvousRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
 
    #[Route('/calendar/events', name: 'calendar_events')]
public function events(RendezvousRepository $rendezvousRepository): JsonResponse
{
    
    $patientId = 1;

    
    $rendezvous = $rendezvousRepository->createQueryBuilder('r')
        ->andWhere('r.PatientId = :patientId')
        ->setParameter('patientId', $patientId)
        ->getQuery()
        ->getResult();

    $events = [];

    foreach ($rendezvous as $rdv) {
        $event = [
            'id' => $rdv->getId(),
            'start' => $rdv->getDate()->format('Y-m-d') . 'T' . $rdv->getStartTime()->format('H:i:s'),
            'backgroundColor' => '#3788d8',
            'borderColor' => '#3788d8',
            'allDay' => false,
        ];

        try {
        
            $type = $rdv->getTypeConsultationId() == 2 ? 'Téléconsultation' : 'Consultation';
            $event['title'] = $type;

            
            if (method_exists($rdv, 'getMedecinId') && $rdv->getMedecinId()) {
                $medecin = $rdv->getMedecinId();
                if (method_exists($medecin, 'getNom')) {
                    $event['title'] .= ' - ' . $medecin->getNom();
                }
            }

            
            $event['extendedProps'] = [
                'type' => $type
            ];

        } catch (\Exception $e) {
            $event['title'] = 'Consultation';
        }

        $events[] = $event;
    }

    return new JsonResponse($events);
}


#[Route('/calendar/eventmedecin', name: 'calendar_event_medecin')]
public function eventmedecin(RendezvousRepository $rendezvousRepository): JsonResponse
{
    
    $medecinId = 2;

    
    $rendezvous = $rendezvousRepository->createQueryBuilder('r')
        ->andWhere('r.medecinId = :medecinId')
        ->setParameter('medecinId', $medecinId)
        ->getQuery()
        ->getResult();

    $events = [];

    foreach ($rendezvous as $rdv) {
        $event = [
            'id' => $rdv->getId(),
            'start' => $rdv->getDate()->format('Y-m-d') . 'T' . $rdv->getStartTime()->format('H:i:s'),
            'backgroundColor' => '#3788d8',
            'borderColor' => '#3788d8',
            'allDay' => false,
        ];

        try {
            
            $type = $rdv->getTypeConsultationId() == 2 ? 'Téléconsultation' : 'Consultation';
            $event['title'] = $type;

            
            if (method_exists($rdv, 'getPatient') && $rdv->getPatient()) {
                $patient = $rdv->getPatient();
                if (method_exists($patient, 'getNom')) {
                    $event['title'] .= ' - ' . $patient->getNom();
                }
            }

            
            $event['extendedProps'] = [
                'type' => $type
            ];

        } catch (\Exception $e) {
            $event['title'] = 'Consultation';
        }

        $events[] = $event;
    }

    return new JsonResponse($events);
}


}