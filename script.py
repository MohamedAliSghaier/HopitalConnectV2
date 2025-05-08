import json
import sys
import csv
import os

def load_medications(csv_file):
    medications = {}
    try:
        # Vérifier si le fichier existe
        if not os.path.exists(csv_file):
            print(json.dumps({'error': f'Le fichier CSV n\'existe pas à l\'emplacement: {csv_file}'}))
            print(json.dumps({'debug': f'Répertoire courant: {os.getcwd()}'}))
            print(json.dumps({'debug': f'Contenu du répertoire: {os.listdir(".")}'}))
            sys.exit(1)

        print(json.dumps({'debug': f'Lecture du fichier CSV: {csv_file}'}))
        
        # Lire le fichier en mode binaire
        with open(csv_file, 'rb') as file:
            content = file.read()
            print(json.dumps({'debug': f'Contenu brut (hex): {content.hex()}'}))
            
            # Essayer différents encodages
            encodings = ['ascii', 'latin-1', 'cp1252', 'utf-8']
            decoded_content = None
            
            for encoding in encodings:
                try:
                    decoded_content = content.decode(encoding)
                    print(json.dumps({'debug': f'Décodage réussi avec: {encoding}'}))
                    break
                except UnicodeDecodeError:
                    print(json.dumps({'debug': f'Échec du décodage avec: {encoding}'}))
                    continue
            
            if decoded_content is None:
                print(json.dumps({'error': 'Impossible de décoder le fichier avec les encodages disponibles'}))
                sys.exit(1)
            
            # Lire le CSV
            reader = csv.DictReader(decoded_content.splitlines())
            print(json.dumps({'debug': f'En-têtes CSV: {reader.fieldnames}'}))
            
            for row in reader:
                print(json.dumps({'debug': f'Lecture du médicament: {row["nom"]}'}))
                print(json.dumps({'debug': f'Ses symptômes: {row["symptomes"]}'}))
                medications[row['nom']] = {
                    'description': row['description'],
                    'symptomes': [s.strip() for s in row['symptomes'].split(',')]
                }
        
        if not medications:
            print(json.dumps({'error': 'Aucun médicament trouvé dans le fichier CSV'}))
            sys.exit(1)
            
        print(json.dumps({'debug': f'Médicaments chargés: {medications}'}))
    except Exception as e:
        print(json.dumps({'error': f'Erreur lors de la lecture du fichier CSV: {str(e)}'}))
        print(json.dumps({'error': f'Type d\'erreur: {type(e)}'}))
        print(json.dumps({'error': f'Traceback: {sys.exc_info()}'}))
        sys.exit(1)
    return medications

def find_medications(symptoms, medications):
    matching_medications = {}
    print(json.dumps({'debug': f'Symptômes recherchés: {symptoms}'}))
    print(json.dumps({'debug': f'Nombre de médicaments à vérifier: {len(medications)}'}))
    
    for med_name, med_data in medications.items():
        print(json.dumps({'debug': f'Vérification du médicament: {med_name}'}))
        print(json.dumps({'debug': f'Ses symptômes: {med_data["symptomes"]}'}))
        
        for symptom in symptoms:
            symptom_lower = symptom.lower()
            print(json.dumps({'debug': f'Recherche du symptôme: {symptom_lower}'}))
            
            for med_symptom in med_data['symptomes']:
                med_symptom_lower = med_symptom.lower()
                print(json.dumps({'debug': f'Comparaison: {symptom_lower} avec {med_symptom_lower}'}))
                if symptom_lower in med_symptom_lower or med_symptom_lower in symptom_lower:
                    matching_medications[med_name] = med_data['description']
                    print(json.dumps({'debug': f'Match trouvé: {med_name} pour {symptom}'}))
                    break
    
    print(json.dumps({'debug': f'Médicaments correspondants: {matching_medications}'}))
    return matching_medications

def main():
    # Chemin vers le fichier CSV
    script_dir = os.path.dirname(os.path.abspath(__file__))
    csv_file = os.path.join(script_dir, 'medications.csv')
    
    print(json.dumps({'debug': f'Chemin actuel: {os.getcwd()}'}))
    print(json.dumps({'debug': f'Fichier CSV: {csv_file}'}))
    print(json.dumps({'debug': f'Le fichier existe: {os.path.exists(csv_file)}'}))
    print(json.dumps({'debug': f'Contenu du répertoire: {os.listdir(script_dir)}'}))
    
    # Charger les médicaments depuis le CSV
    medications = load_medications(csv_file)
    
    # Traiter les arguments
    if len(sys.argv) < 2:
        print(json.dumps({'error': 'Aucun symptôme fourni'}))
        sys.exit(1)
    
    try:
        # Essayer de décoder le premier argument comme JSON
        symptoms = json.loads(sys.argv[1])
    except json.JSONDecodeError:
        # Si ce n'est pas du JSON valide, traiter chaque argument comme un symptôme
        symptoms = sys.argv[1:]
    
    # Nettoyer les symptômes
    symptoms = [s.strip().lower() for s in symptoms if s.strip()]
    print(json.dumps({'debug': f'Symptômes après nettoyage: {symptoms}'}))
    
    if not symptoms:
        print(json.dumps({'error': 'Aucun symptôme valide fourni'}))
        sys.exit(1)
    
    # Trouver les médicaments correspondants
    results = find_medications(symptoms, medications)
    
    if not results:
        print(json.dumps({'error': 'Aucun médicament trouvé pour ces symptômes'}))
        sys.exit(1)
    
    # Afficher les résultats en JSON
    print(json.dumps(results, ensure_ascii=False))

if __name__ == '__main__':
    main()