<?php
require_once '../config.php';

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

if (isset($_GET["id"]) && !empty(trim($_GET["id"])) && isset($_GET["book_id"]) && !empty(trim($_GET["book_id"]))) {
    $copy_id = trim($_GET["id"]);
    $book_id = trim($_GET["book_id"]);

    try {
        // Check if the book copy is currently issued
        $sql_check_status = "SELECT status FROM book_copies WHERE id = :copy_id";
        $stmt_check_status = $pdo->prepare($sql_check_status);
        $stmt_check_status->bindParam(":copy_id", $copy_id, PDO::PARAM_INT);
        $stmt_check_status->execute();
        $status = $stmt_check_status->fetchColumn();

        if ($status === 'issued') {
            throw new Exception("Cannot delete an issued book copy. Please return it first.");
        }

        // Delete the book copy
        $sql_delete_copy = "DELETE FROM book_copies WHERE id = :copy_id";
        $stmt_delete_copy = $pdo->prepare($sql_delete_copy);
        $stmt_delete_copy->bindParam(":copy_id", $copy_id, PDO::PARAM_INT);
        $stmt_delete_copy->execute();

        header("location: edit_book.php?id=" . $book_id . "&success=Copy deleted successfully.");
        exit();
    } catch (PDOException $e) {
        header("location: edit_book.php?id=" . $book_id . "&error=" . urlencode("Database error: " . $e->getMessage()));
        exit();
    } catch (Exception $e) {
        header("location: edit_book.php?id=" . $book_id . "&error=" . urlencode($e->getMessage()));
        exit();
    }

    unset($pdo);
} else {
    header("location: error.php");
    exit();
}
?>