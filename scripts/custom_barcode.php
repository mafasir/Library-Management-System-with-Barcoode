<?php
// Function to generate a barcode image
function generate_barcode_image($text) {
    $width = 200;
    $height = 50;
    $image = imagecreate($width, $height);
    $bg_color = imagecolorallocate($image, 255, 255, 255);
    $bar_color = imagecolorallocate($image, 0, 0, 0);

    // Simple barcode generation (replace with a more robust implementation if needed)
    $x = 10;
    for ($i = 0; $i < strlen($text); $i++) {
        $char = $text[$i];
        if (is_numeric($char)) {
            $bar_width = (int)$char + 1;
            imagefilledrectangle($image, $x, 10, $x + $bar_width, $height - 20, $bar_color);
            $x += $bar_width + 2;
        }
    }

    ob_start();
    imagepng($image);
    $image_data = ob_get_clean();
    imagedestroy($image);

    return $image_data;
}
?>