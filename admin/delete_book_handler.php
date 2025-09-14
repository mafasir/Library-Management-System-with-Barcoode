<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once '../config.php';

// Process delete operation
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $book_id = trim($_GET["id"]);

    $pdo->beginTransaction();

    try {
        // Delete associated book copies first
        $sql_delete_copies = "DELETE FROM book_copies WHERE book_id = :book_id";
        $stmt_delete_copies = $pdo->prepare($sql_delete_copies);
        $stmt_delete_copies->bindParam(":book_id", $book_id, PDO::PARAM_INT);
        $stmt_delete_copies->execute();

        // Then delete the book itself
        $sql_delete_book = "DELETE FROM books WHERE id = :book_id";
        $stmt_delete_book = $pdo->prepare($sql_delete_book);
        $stmt_delete_book->bindParam(":book_id", $book_id, PDO::PARAM_INT);
        $stmt_delete_book->execute();

        $pdo->commit();
        header("location: books.php");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Oops! Something went wrong. Could not delete the book and its copies. " . $e->getMessage();
    }

    unset($pdo);
} else {
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>