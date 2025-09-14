<?php
require_once '../config.php';

$barcode = $_POST['barcode'];

// Find copy by barcode
$sql = "SELECT id FROM book_copies WHERE barcode = :barcode";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':barcode', $barcode, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $copy_id = $row['id'];

    // Update transaction
    $sql = "UPDATE transactions SET return_date = CURDATE() WHERE id = :copy_id AND return_date IS NULL";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':copy_id', $copy_id, PDO::PARAM_INT);
    $stmt->execute();

    // Update copy status
    $sql = "UPDATE book_copies SET status = 'available' WHERE id = :copy_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':copy_id', $copy_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: return-book.php?msg=Book returned successfully");
} else {
    header("Location: return-book.php?error=Invalid barcode");
}
?>
