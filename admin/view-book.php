<?php
require_once("../config.php");

if (!isset($_GET['barcode'])) {
    echo "No barcode provided.";
    exit;
}

$barcode = $_GET['barcode'];

// Fetch book info using the barcode
$sql = "SELECT b.title, b.author, bc.barcode, bc.status
        FROM book_copies bc
        JOIN books b ON bc.book_id = b.id
        WHERE bc.barcode = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $barcode);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo "<h2>📚 Book Details</h2>";
    echo "Title: " . htmlspecialchars($row['title']) . "<br>";
    echo "Author: " . htmlspecialchars($row['author']) . "<br>";
    echo "Barcode: " . htmlspecialchars($row['barcode']) . "<br>";
    echo "Status: " . htmlspecialchars($row['status']) . "<br>";
} else {
    echo "❌ Book not found for barcode: $barcode";
}
?>
