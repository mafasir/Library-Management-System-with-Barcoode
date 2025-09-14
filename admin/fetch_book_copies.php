<?php
require_once '../config.php';

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // For AJAX requests, just exit or return an error message
    echo "Unauthorized access.";
    exit;
}

if (isset($_GET['book_id']) && !empty(trim($_GET['book_id']))) {
    $book_id = trim($_GET['book_id']);

    try {
        $sql = "SELECT id, barcode, status FROM book_copies WHERE book_id = :book_id ORDER BY barcode ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":book_id", $book_id, PDO::PARAM_INT);
        $stmt->execute();
        $book_copies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($book_copies) {
            echo '<table class="table table-bordered table-striped table-sm">';
            echo '<thead><tr><th>Barcode</th><th>Status</th><th>Actions</th></tr></thead>';
            echo '<tbody>';
            foreach ($book_copies as $copy) {
                $status_badge = '';
                if ($copy['status'] == 'available') {
                    $status_badge = '<span class="badge badge-success">Available</span>';
                } elseif ($copy['status'] == 'issued') {
                    $status_badge = '<span class="badge badge-warning">Issued</span>';
                } elseif ($copy['status'] == 'lost') {
                    $status_badge = '<span class="badge badge-danger">Lost</span>';
                }
                echo '<tr>';
                echo '<td>' . htmlspecialchars($copy['barcode']) . '</td>';
                echo '<td>' . $status_badge . '</td>';
                echo '<td>';
                echo '<a href="#" class="btn btn-sm btn-info mr-1" data-toggle="modal" data-target="#barcodeModal" data-barcode-src="../assets/barcodes/' . htmlspecialchars($copy['barcode']) . '.png">View Barcode</a>';
                echo '<a href="delete_book_copy_handler.php?id=' . $copy['id'] . '&book_id=' . $book_id . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this book copy?\');">Delete Copy</a>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>No copies found for this book.</p>';
        }

    } catch (PDOException $e) {
        echo "<div class=\"alert alert-danger\">Error fetching book copies: " . $e->getMessage() . "</div>";
    }

    unset($pdo);
} else {
    echo "Invalid book ID.";
}
?>