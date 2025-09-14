<?php
include("../config.php");

$book_id = $_POST['book_id'];
$num_copies = $_POST['num_copies'];

for ($i = 1; $i <= $num_copies; $i++) {
    $unique_code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8)); // Generate unique barcode
    $barcode = "BID" . $book_id . "-" . $unique_code;

    $sql = "INSERT INTO book_copies (book_id, barcode, status) VALUES ('$book_id', '$barcode', 'available')";
    mysqli_query($conn, $sql);
}

header("Location: books.php?msg=Book copies added successfully");
?>
