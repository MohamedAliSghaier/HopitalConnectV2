<?php

namespace App\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Psr\Log\LoggerInterface;

class PythonScriptService
{
    private string $pythonScriptPath;
    private LoggerInterface $logger;
    private string $pythonExecutable;
    private string $csvPath;

    public function __construct(string $projectDir, LoggerInterface $logger)
    {
        $this->pythonScriptPath = 'C:/Users/LENOVO/script.py';
        $this->logger = $logger;
        $this->pythonExecutable = 'C:/Users/LENOVO/anaconda3/python.exe';
        $this->csvPath = $projectDir . '/data/medications.csv';
    }

    public function getMedicationsForSymptoms(array $symptoms): array
    {
        try {
            // Nettoyer et valider les symptômes
            $symptoms = array_map('trim', $symptoms);
            $symptoms = array_filter($symptoms, function($symptom) {
                return !empty($symptom);
            });

            if (empty($symptoms)) {
                throw new \RuntimeException('Aucun symptôme valide fourni');
            }

            $this->logger->info('Tentative d\'exécution du script Python avec les symptômes: ' . implode(', ', $symptoms));
            
            // Vérifier si le fichier Python existe
            if (!file_exists($this->pythonScriptPath)) {
                $this->logger->error('Le fichier Python n\'existe pas: ' . $this->pythonScriptPath);
                throw new \RuntimeException('Le fichier Python n\'existe pas');
            }

            // Vérifier si l'exécutable Python existe
            if (!file_exists($this->pythonExecutable)) {
                $this->logger->error('L\'exécutable Python n\'existe pas: ' . $this->pythonExecutable);
                throw new \RuntimeException('L\'exécutable Python n\'existe pas');
            }

            // Vérifier si le fichier CSV existe
            if (!file_exists($this->csvPath)) {
                $this->logger->error('Le fichier CSV n\'existe pas: ' . $this->csvPath);
                throw new \RuntimeException('Le fichier CSV n\'existe pas');
            }

            $this->logger->info('Utilisation de l\'exécutable Python: ' . $this->pythonExecutable);

            // Construire la commande avec les symptômes comme arguments séparés
            $command = array_merge(
                [$this->pythonExecutable, $this->pythonScriptPath],
                $symptoms
            );

            $this->logger->info('Commande exécutée: ' . implode(' ', $command));

            $process = new Process($command);
            $process->run();

            if (!$process->isSuccessful()) {
                $errorOutput = $process->getErrorOutput();
                $this->logger->error('Erreur lors de l\'exécution du script Python: ' . $errorOutput);
                $this->logger->error('Code de sortie: ' . $process->getExitCode());
                throw new ProcessFailedException($process);
            }

            $output = $process->getOutput();
            $this->logger->info('Sortie brute du script Python: ' . $output);

            $result = json_decode($output, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error('Erreur de décodage JSON: ' . json_last_error_msg());
                $this->logger->error('Contenu reçu: ' . $output);
                throw new \RuntimeException('Erreur de décodage JSON: ' . json_last_error_msg());
            }

            $this->logger->info('Résultats décodés: ' . json_encode($result));
            return $result;
        } catch (\Exception $e) {
            $this->logger->error('Erreur dans PythonScriptService: ' . $e->getMessage());
            $this->logger->error('Trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }
} 