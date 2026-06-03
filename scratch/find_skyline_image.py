import os
import sys
import site

user_site = site.getusersitepackages()
if user_site not in sys.path:
    sys.path.append(user_site)

from PIL import Image

img_dir = r"public\assets\assets\img"
images = ["arrierep.jpg", "backgroud.jpg", "bavk.jpg", "C.jpg", "A.jpg", "Plateau-immeuble.jpg"]

for name in images:
    path = os.path.join(img_dir, name)
    if os.path.exists(path):
        try:
            with Image.open(path) as img:
                # Get a small 10x10 resized version to check average colors
                small = img.resize((10, 10))
                colors = list(small.getdata())
                # Check if it is a dark image or light image
                avg_r = sum(c[0] for c in colors) / 100
                avg_g = sum(c[1] for c in colors) / 100
                avg_b = sum(c[2] for c in colors) / 100 if len(colors[0]) > 2 else avg_r
                print(f"{name}: size={img.size}, avg_rgb=({avg_r:.1f}, {avg_g:.1f}, {avg_b:.1f})")
        except Exception as e:
            print(f"{name}: Error - {e}")
    else:
         print(f"{name}: Not found")
