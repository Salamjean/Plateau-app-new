import os
from PIL import Image

media_path = r'C:\Users\LENOVO\.gemini\antigravity-ide\brain\d3bd8390-afc5-4c5f-a2ae-a18b1f75dafd\media__1782208365503.png'

if not os.path.exists(media_path):
    print("Media file does not exist!")
    exit(1)

img = Image.open(media_path)
width, height = img.size
print(f"Dimensions: {width}x{height}")

# Analyse des couleurs sur 4 bandes horizontales (Haut, Milieu-Haut, Milieu-Bas, Bas)
band_height = height // 4

for i in range(4):
    band = img.crop((0, i * band_height, width, (i + 1) * band_height))
    # Convert to RGB to analyze pixels
    rgb_band = band.convert('RGB')
    pixels = list(rgb_band.getdata())
    
    # Compter le nombre de pixels clairs (proches du blanc, ex: R>220, G>220, B>220)
    white_pixels = sum(1 for r, g, b in pixels if r > 200 and g > 200 and b > 200)
    # Compter le nombre de pixels foncés/bleus (ex: R<50, G<80, B<150)
    blue_pixels = sum(1 for r, g, b in pixels if r < 60 and g < 100 and b > 100)
    dark_pixels = sum(1 for r, g, b in pixels if r < 40 and g < 40 and b < 80)
    
    total = len(pixels)
    print(f"Bande {i+1} (Y: {i*band_height} à {(i+1)*band_height}):")
    print(f"  Clairs/Blancs: {white_pixels} ({white_pixels/total*100:.1f}%)")
    print(f"  Bleus: {blue_pixels} ({blue_pixels/total*100:.1f}%)")
    print(f"  Foncés/Sombres: {dark_pixels} ({dark_pixels/total*100:.1f}%)")
