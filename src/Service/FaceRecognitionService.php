<?php
namespace App\Service;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FaceRecognitionService
{
    private string $knownFacesDir;

    public function __construct(string $knownFacesDir)
    {
        $this->knownFacesDir = $knownFacesDir;
    }

    public function verifyUserFace(string $base64Image, string $email): bool
    {
        $knownImagePath = $this->knownFacesDir . '/' . $email . '.jpg';

        if (!file_exists($knownImagePath)) {
            return false;
        }

        // Convertir base64 en image temporaire
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
        $tempFilePath = tempnam(sys_get_temp_dir(), 'face_') . '.png';
        file_put_contents($tempFilePath, $imageData);

        // Exécuter le script Python
        $process = new Process([
            'python',
            __DIR__ . '/../../scripts/recognize_face.py',
            $tempFilePath,
            $knownImagePath
        ]);

        try {
            $process->mustRun();
            $output = $process->getOutput();
            unlink($tempFilePath); // Supprimer l'image temporaire

            return trim($output) === 'MATCH';
        } catch (ProcessFailedException $e) {
            if (file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
            return false;
        }
    }
    public function compareFaces(string $photoCaptureePath, string $photoConnuePath): bool
{
    $process = new Process([
        'python',
        __DIR__ . '/../../scripts/recognize_face.py',
        $photoCaptureePath,
        $photoConnuePath
    ]);

    try {
        $process->mustRun();
        $output = trim($process->getOutput());
        return $output === 'MATCH';
    } catch (\Exception $e) {
        return false;
    }
}

}
