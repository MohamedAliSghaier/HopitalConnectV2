<?php

// src/Service/FaceRecognitionService.php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FaceRecognitionService
{
    private string $knownFacesDir;

    public function __construct(string $knownFacesDir)
    {
        $this->knownFacesDir = $knownFacesDir;
    }

    public function verifyUserFace(UploadedFile $uploadedImage, string $username): bool
    {
        $tempFile = $uploadedImage->getPathname();
        $knownImagePath = $this->knownFacesDir . '/' . $username . '.jpg';

        if (!file_exists($knownImagePath)) {
            return false;
        }

        $process = new Process([
            'python',
            __DIR__ . '/../../scripts/recognize_face.py',
            $tempFile,
            $knownImagePath
        ]);

        try {
            $process->mustRun();
            $output = $process->getOutput();

            return trim($output) === 'MATCH';
        } catch (ProcessFailedException $e) {
            return false;
        }
    }
}