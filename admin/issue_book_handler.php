<?php
require_once '../config.php';

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

$member_id = $_POST['user_id'];
$barcode = $_POST['barcode'];

// Check if member exists
$stmt = $pdo->prepare("SELECT id FROM members WHERE id = :member_id");
$stmt->execute(['member_id' => $member_id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    header("Location: issue-book.php?error=Invalid member ID");
    exit;
}

// Find copy by barcode
$stmt = $pdo->prepare("SELECT book_id FROM book_copies WHERE barcode = :barcode AND status = 'available'");
$stmt->execute(['barcode' => $barcode]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $book_id = $row['book_id'];  

    // Insert into transaction
    $stmt = $pdo->prepare("INSERT INTO transactions (member_id, book_id, issue_date) VALUES (:member_id, :book_id, CURDATE())");
    $stmt->execute(['member_id' => $member_id, 'book_id' => $book_id]);

    // Update copy status
    $stmt = $pdo->prepare("UPDATE book_copies SET status = 'issued' WHERE book_id = :book_id AND barcode = :barcode");
    $stmt->execute(['book_id' => $book_id, 'barcode' => $barcode]);

    header("Location: issue-book.php?msg=Book issued successfully");
} else {
    header("Location: issue-book.php?error=Book not available");
}