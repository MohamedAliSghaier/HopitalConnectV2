<?php

namespace App\Service;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class QrCodeService
{
    private $params;
    private $qrCodeDir;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
        $this->qrCodeDir = $this->params->get('kernel.project_dir') . '/public/qr_codes';
        
        // Ensure the QR code directory exists
        if (!file_exists($this->qrCodeDir)) {
            mkdir($this->qrCodeDir, 0777, true);
        }
    }

    public function generateQrCode(string $data, string $filename): string
    {
        try {
            $renderer = new ImageRenderer(
                new RendererStyle(300, 10),
                new SvgImageBackEnd()
            );

            $writer = new Writer($renderer);
            $filepath = $this->qrCodeDir . '/' . $filename;
            
            $writer->writeFile($data, $filepath);

            return $filename;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to generate QR code: ' . $e->getMessage());
        }
    }

    public function getQrCodePath(string $filename): string
    {
        return $this->qrCodeDir . '/' . $filename;
    }
} 