<?php

namespace App\Controller;

use App\Entity\Ordonnance;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Psr\Log\LoggerInterface;

class QrCodeController extends AbstractController
{
    #[Route('/ordonnance/qr/{id}', name: 'ordonnance_qr', methods: ['GET'])]
    public function generateQr(
        int $id,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ): Response {
        try {
            $logger->info('Début de la génération du QR code pour l\'ordonnance ID: ' . $id);

            $ordonnance = $entityManager->getRepository(Ordonnance::class)->find($id);

            if (!$ordonnance) {
                $this->addFlash('error', 'Ordonnance non trouvée.');
                return $this->redirectToRoute('ordonnance_my');
            }

            // 👉 Génération d’un lien vers le PDF de l’ordonnance
            $qrContent = $this->generateUrl(
                'ordonnance_pdf',
                ['id' => $ordonnance->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            // ✅ Génération du QR code avec Builder
            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($qrContent)
                ->size(300)
                ->margin(10)
                ->build();

            $qrCodeDataUri = $result->getDataUri();

            return $this->render('ordonnance/qr_code.html.twig', [
                'ordonnance' => $ordonnance,
                'qrCode' => $qrCodeDataUri,
            ]);

        } catch (\Exception $e) {
            $logger->error('Erreur QR code : ' . $e->getMessage());
            $this->addFlash('error', 'Erreur lors de la génération du QR code.');
            return $this->redirectToRoute('ordonnance_my');
        }
    }
}

