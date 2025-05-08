<?php
namespace App\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class MedicationSuggestionService
{
    public function getSuggestedMedicines($symptoms)
    {
        // Préparer les symptômes sous forme de texte à passer au script Python
        $symptoms_text = implode(",", $symptoms);

        // Créer le processus pour exécuter le script Python avec les symptômes comme argument
        // Remarque : Assure-toi que le chemin du script Python est correct.
        $process = new Process(['python3', 'C:/Users/LENOVO/script.py', $symptoms_text]);
        
        // Lancer le processus
        $process->run();

        // Vérifier si le processus a réussi
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Récupérer la sortie du processus (les résultats du script Python)
        $output = $process->getOutput();

        // Décoder la sortie JSON pour la renvoyer sous forme de tableau associatif
        return json_decode($output, true);
    }
}
