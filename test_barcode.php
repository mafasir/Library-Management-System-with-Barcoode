<?php
require_once 'scripts/code128.php';

// The data to encode
$testData = "TEST12345";

// Create a new barcode
$barcode = new Code128($testData);

// Generate the image data
$imageData = $barcode->get_image();

// Define the file path
$filePath = 'assets/barcodes/test_barcode.png';

// Save the image to a file
file_put_contents($filePath, $imageData);

echo "Barcode test image generated successfully.\n";
echo "You can find it at: " . $filePath . "\n";
?>