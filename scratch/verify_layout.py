import os
from PIL import Image

media_path = r'C:\Users\LENOVO\.gemini\antigravity-ide\brain\d3bd8390-afc5-4c5f-a2ae-a18b1f75dafd\services_penses_pour_vous_1782210132830.png'

if not os.path.exists(media_path):
    print("Media file does not exist!")
    exit(1)

img = Image.open(media_path)
width, height = img.size

# Nous voulons détecter la zone du texte (moitié gauche, X < 500)
# et la zone du téléphone (moitié droite, X >= 500)
# En comparant la couleur de fond locale (bleue) avec les pixels pour trouver les boîtes de délimitation.

ref_bg = img.getpixel((50, 50))  # Coin haut gauche (fond de la section bleue)
print(f"Fond bleu de référence: {ref_bg}")

threshold = 30

text_pixels = []
phone_pixels = []

for y in range(height):
    # Moitié gauche pour le texte (on exclut les 20 premiers pixels de marge)
    for x in range(20, 500):
        p = img.getpixel((x, y))
        diff = sum(abs(p[c] - ref_bg[c]) for c in range(min(3, len(p), len(ref_bg))))
        if diff > threshold:
            text_pixels.append((x, y))
            
    # Moitié droite pour le téléphone
    for x in range(500, width - 20):
        p = img.getpixel((x, y))
        diff = sum(abs(p[c] - ref_bg[c]) for c in range(min(3, len(p), len(ref_bg))))
        if diff > threshold:
            phone_pixels.append((x, y))

if text_pixels:
    ys_text = [y for x, y in text_pixels]
    min_y_text, max_y_text = min(ys_text), max(ys_text)
    print(f"Zone Texte (Gauche): Y de {min_y_text} à {max_y_text} (Hauteur: {max_y_text - min_y_text}px)")
else:
    print("Texte non détecté.")

if phone_pixels:
    ys_phone = [y for x, y in phone_pixels]
    min_y_phone, max_y_phone = min(ys_phone), max(ys_phone)
    print(f"Zone Téléphone (Droite): Y de {min_y_phone} à {max_y_phone} (Hauteur: {max_y_phone - min_y_phone}px)")
else:
    print("Téléphone non détecté.")
