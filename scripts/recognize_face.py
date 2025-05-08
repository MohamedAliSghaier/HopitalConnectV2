import face_recognition
import sys

def recognize_face(uploaded_image_path, known_image_path):
    # Charger l'image téléchargée
    uploaded_image = face_recognition.load_image_file(uploaded_image_path)
    uploaded_encoding = face_recognition.face_encodings(uploaded_image)

    if not uploaded_encoding:
        print("NO MATCH")
        return

    # Charger l'image connue
    known_image = face_recognition.load_image_file(known_image_path)
    known_encoding = face_recognition.face_encodings(known_image)

    if not known_encoding:
        print("NO MATCH")
        return

    # Comparer les visages
    results = face_recognition.compare_faces([known_encoding[0]], uploaded_encoding[0])
    if results[0]:
        print("MATCH")
    else:
        print("NO MATCH")

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python recognize_face.py <uploaded_image_path> <known_image_path>")
        sys.exit(1)

    uploaded_image_path = sys.argv[1]
    known_image_path = sys.argv[2]
    recognize_face(uploaded_image_path, known_image_path)