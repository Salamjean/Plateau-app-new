import os
import sys
import site

# Add user site-packages to sys.path
user_site = site.getusersitepackages()
if user_site not in sys.path:
    sys.path.append(user_site)

try:
    from PIL import Image
except ImportError:
    print("PIL still not found in sys.path. Running pip install again and printing sys.path...")
    print("sys.path:", sys.path)
    import subprocess
    subprocess.check_call([sys.executable, "-m", "pip", "install", "pillow"])
    from PIL import Image

img_path = r"public\assets\assets\img\backhero.png"
if not os.path.exists(img_path):
    print(f"Error: {img_path} not found.")
    sys.exit(1)

img = Image.open(img_path)
width, height = img.size
print(f"Image loaded: size={width}x{height}, format={img.format}")

# Convert image to RGB
img_rgb = img.convert("RGB")

# Let's check some pixels in the regions where cards are expected.
# Floating card 1 (top-left of the man/middle of the screen): around x = 1100, y = 250
# Floating card 2 (top-right of the man): around x = 1800, y = 250
# Floating card 3 (bottom-right of the man): around x = 1800, y = 600

# Let's count white pixels (255, 255, 255) in the right half of the image
right_half_white_pixels = 0
total_pixels_right_half = 0

for x in range(width // 2, width):
    for y in range(height):
        r, g, b = img_rgb.getpixel((x, y))
        total_pixels_right_half += 1
        if r > 245 and g > 245 and b > 245: # near white
            right_half_white_pixels += 1

white_ratio = right_half_white_pixels / total_pixels_right_half
print(f"Right half white pixel ratio: {white_ratio:.4f} ({right_half_white_pixels} out of {total_pixels_right_half})")

# Let's also check if there are solid white lines
solid_white_segments = 0
for y in range(0, height, 5):
    white_run = 0
    for x in range(width // 2, width):
        r, g, b = img_rgb.getpixel((x, y))
        if r > 250 and g > 250 and b > 250:
            white_run += 1
        else:
            if white_run > 80: # a run of more than 80 white pixels
                solid_white_segments += 1
            white_run = 0

print(f"Found {solid_white_segments} horizontal segments of solid white > 80px in the right half.")
if solid_white_segments > 5:
    print("Conclusion: The image CONTAINS the white floating cards baked in!")
else:
    print("Conclusion: The image does NOT contain baked-in white cards (or they are transparent/different).")
