import os
from PIL import Image

media_path = r'C:\Users\LENOVO\.gemini\antigravity-ide\brain\d3bd8390-afc5-4c5f-a2ae-a18b1f75dafd\media__1782208365503.png'

if not os.path.exists(media_path):
    print("Media file does not exist!")
    exit(1)

img = Image.open(media_path)
width, height = img.size

# Nous allons balayer l'image pour trouver la zone du téléphone.
# Le téléphone contient généralement des pixels très spécifiques (l'écran de l'application, des bords de téléphone sombres ou clairs).
# La couleur de fond de la section bleue de la capture (en haut à droite, par exemple à X=width-20, Y=50) est la couleur de référence du fond bleu.
# Nous allons trouver tous les pixels de la moitié droite (X > width // 2) qui diffèrent significativement de la couleur de fond locale.

ref_bg = img.getpixel((width - 10, 50))
print(f"Couleur de fond de référence (haut-droite): {ref_bg}")

# Seuil de différence pour considérer qu'un pixel appartient au téléphone ou à un autre élément que le fond
threshold = 30

phone_pixels = []

for y in range(height):
    for x in range(width // 2, width):
        p = img.getpixel((x, y))
        # Distance euclidienne simple ou différence absolue
        diff = sum(abs(p[c] - ref_bg[c]) for c in range(min(3, len(p), len(ref_bg))))
        if diff > threshold:
            phone_pixels.append((x, y))

if phone_pixels:
    xs = [x for x, y in phone_pixels]
    ys = [y for x, y in phone_pixels]
    min_x, max_x = min(xs), max(xs)
    min_y, max_y = min(ys), max(ys)
    print(f"Téléphone détecté dans la boîte de délimitation :")
    print(f"  X: {min_x} à {max_x} (Largeur: {max_x - min_x}px, soit {((max_x - min_x)/width)*100:.1f}% de l'écran)")
    print(f"  Y: {min_y} à {max_y} (Hauteur: {max_y - min_y}px, soit {((max_y - min_y)/height)*100:.1f}% de l'écran)")
    print(f"  Bord droit de l'image au téléphone : {width - max_x}px ({((width - max_x)/width)*100:.1f}%)")
else:
    print("Aucun téléphone détecté avec ces critères.")
