<?php
require_once '../config.php';
require_once '../scripts/code128.php';

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $author = trim($_POST["author"]);
    $isbn = trim($_POST["isbn"]);
    $image_path = NULL;
    $num_copies = $_POST["num_copies"];

    // Handle image upload
    if (isset($_FILES["book_image"]) && $_FILES["book_image"]["error"] == 0) {
        $target_dir = "../assets/book_images/";
        // Create the directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_name = uniqid() . '_' . basename($_FILES["book_image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Allow certain file formats
        $allowed_types = array("jpg", "png", "jpeg", "gif");
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["book_image"]["tmp_name"], $target_file)) {
                $image_path = 'assets/book_images/' . $image_name;
            } else {
                header("location: add-book.php?error=" . urlencode('Error uploading image.'));
                exit;
            }
        } else {
            header("location: add-book.php?error=" . urlencode('Sorry, only JPG, JPEG, PNG & GIF files are allowed.'));
            exit;
        }
    }

    try {
        // Insert book details into the books table
        $sql_book = "INSERT INTO books (title, author, isbn, image_path) VALUES (:title, :author, :isbn, :image_path)";
        $stmt_book = $pdo->prepare($sql_book);
        $stmt_book->bindParam(":title", $title, PDO::PARAM_STR);
        $stmt_book->bindParam(":author", $author, PDO::PARAM_STR);
        $stmt_book->bindParam(":isbn", $isbn, PDO::PARAM_STR);
        $stmt_book->bindParam(":image_path", $image_path, PDO::PARAM_STR);
        $stmt_book->execute();

        $book_id = $pdo->lastInsertId();

        // Insert each barcode into the book_copies table
        $sql_copy = "INSERT INTO book_copies (book_id, barcode, status) VALUES (:book_id, :barcode, 'available')";
        $stmt_copy = $pdo->prepare($sql_copy);

        for ($i = 0; $i < $num_copies; $i++) {
            $barcode = uniqid();
            $stmt_copy->bindParam(":book_id", $book_id, PDO::PARAM_INT);
            $stmt_copy->bindParam(":barcode", $barcode, PDO::PARAM_STR);
            $stmt_copy->execute();

            // Generate barcode image
            $code128 = new Code128($barcode);
            $barcode_image = $code128->get_image();
            file_put_contents('../assets/barcodes/' . $barcode . '.png', $barcode_image);
        }

        header("location: add-book.php?success=1");

    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) { // 1062 is the MySQL error code for duplicate entry
            header("location: add-book.php?error=" . urlencode('Error: Duplicate entry (ISBN or Barcode) already exists.'));
        } else {
            header("location: add-book.php?error=" . urlencode($e->getMessage()));
        }
    }

    unset($pdo);
}
?>