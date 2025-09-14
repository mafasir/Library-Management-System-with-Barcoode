
<?php
require_once '../config.php';
function generateBarcodeHash($input, $length = 12) {
    $hash = hash('sha256', $input);
    $decimal = base_convert(substr($hash, 0, 15), 16, 10);
    $barcode = substr(str_pad($decimal, $length, '0', STR_PAD_RIGHT), 0, $length);
    return $barcode;
}

function generateBarcodeImage($barcode, $path) {
    $width = 300;
    $height = 80;
    $fontSize = 5;

    $img = imagecreate($width, $height);
    $white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 0, 0, 0);

    imagefilledrectangle($img, 0, 0, $width, $height, $white);

    $textWidth = imagefontwidth($fontSize) * strlen($barcode);
    $textHeight = imagefontheight($fontSize);
    $x = ($width - $textWidth) / 2;
    $y = ($height - $textHeight) / 2;

    imagestring($img, $fontSize, $x, $y, $barcode, $black);

    // Ensure folder exists
    if (!is_dir("../assets/barcodes")) {
        mkdir("../assets/barcodes", 0777, true);
    }

    imagepng($img, $path);
    imagedestroy($img);
}
?>