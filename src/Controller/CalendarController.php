<?php

namespace App\Controller;

use App\Repository\RendezvousRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;


class CalendarController extends AbstractController
{
 
    #[Route('/calendar/events', name: 'calendar_events')]
public function events(RendezvousRepository $rendezvousRepository): JsonResponse
{
    // 👤 Static patient ID
    $patientId = $this->getUser()->getId();

    // 📥 Fetch only appointments for the patient
    $rendezvous = $rendezvousRepository->createQueryBuilder('r')
        ->andWhere('r.patient = :patientId')
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
            // 🔍 Type
            $type = $rdv->getTypeConsultationId() == 2 ? 'Téléconsultation' : 'Consultation';
            $event['title'] = $type;

            // 🩺 Medecin
            if (method_exists($rdv, 'getMedecin') && $rdv->getMedecin()) {
                $medecin = $rdv->getMedecin();
                if (method_exists($medecin, 'getNom')) {
                    $event['title'] .= ' - ' . $medecin->getNom();
                }
            }

            // Extra props
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
    // 🩺 Static Médecin ID
    $medecinId =  $this->getUser()->getId();

    // 📥 Fetch only appointments for this médecin
    $rendezvous = $rendezvousRepository->createQueryBuilder('r')
        ->andWhere('r.medecin = :medecinId')
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
            // 🔍 Type de consultation
            $type = $rdv->getTypeConsultationId() == 2 ? 'Téléconsultation' : 'Consultation';
            $event['title'] = $type;

            // 👤 Ajouter le nom du patient
            if (method_exists($rdv, 'getPatient') && $rdv->getPatient()) {
                $patient = $rdv->getPatient();
                if (method_exists($patient, 'getNom')) {
                    $event['title'] .= ' - ' . $patient->getNom();
                }
            }

            // Extra props
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