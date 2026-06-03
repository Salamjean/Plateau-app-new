import os
import struct

def get_image_size(file_path):
    """Get image width and height from file header without loading the image."""
    size = os.path.getsize(file_path)
    with open(file_path, 'rb') as f:
        data = f.read(30)
        
        # Check if PNG
        if data.startswith(b'\x89PNG\r\n\x1a\n'):
            w, h = struct.unpack('>II', data[16:24])
            return "PNG", w, h
            
        # Check if JPEG
        if data.startswith(b'\xff\xd8'):
            f.seek(0)
            size = os.path.getsize(file_path)
            # Read until SOF marker
            f.read(2) # skip SOI
            while True:
                marker_header = f.read(4)
                if len(marker_header) < 4:
                    break
                marker, length = struct.unpack('>HH', marker_header)
                if marker >= 0xffc0 and marker <= 0xffc3:
                    # SOF marker
                    sof_data = f.read(5)
                    h, w = struct.unpack('>HH', sof_data[1:5])
                    return "JPEG", w, h
                else:
                    f.seek(length - 2, 1) # skip marker contents
            return "JPEG", None, None
    return "Unknown", None, None

img_dir = r"c:\Users\LENOVO\Documents\@KKS-technologiesWEB\plateau-app\public\assets\assets\img"
images_to_check = ["A.jpg", "C.jpg", "bavk.jpg", "im10.png", "im6.jpeg", "im7.jpeg", "im16.jpg", "hero-bg.jpg", "backgroud.jpg", "arrierep.jpg"]

for img_name in images_to_check:
    img_path = os.path.join(img_dir, img_name)
    if os.path.exists(img_path):
        try:
            fmt, w, h = get_image_size(img_path)
            print(f"{img_name}: format={fmt}, size=({w}, {h}), bytes={os.path.getsize(img_path)}")
        except Exception as e:
            print(f"{img_name}: Error - {e}")
    else:
        print(f"{img_name}: Not found")
