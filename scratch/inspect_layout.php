<?php
$mediaPath = 'C:/Users/LENOVO/.gemini/antigravity-ide/brain/d3bd8390-afc5-4c5f-a2ae-a18b1f75dafd/media__1782208365503.png';
$fondPath = 'c:/Users/LENOVO/Documents/@KKS-technologiesWEB/plateau-app/public/assets/assets/img/Fond PA 25.png';

echo "Media path exists: " . (file_exists($mediaPath) ? 'YES' : 'NO') . "\n";
if (file_exists($mediaPath)) {
    $info = getimagesize($mediaPath);
    echo "Media dimensions: {$info[0]}x{$info[1]}, mime: {$info['mime']}\n";
}

echo "Fond path exists: " . (file_exists($fondPath) ? 'YES' : 'NO') . "\n";
if (file_exists($fondPath)) {
    $info = getimagesize($fondPath);
    echo "Fond dimensions: {$info[0]}x{$info[1]}, mime: {$info['mime']}\n";
}
