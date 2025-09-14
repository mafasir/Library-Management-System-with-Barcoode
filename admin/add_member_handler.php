<?php
session_start();
require_once '../config.php';

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);

    try {
        // Prepare an insert statement
        $sql = "INSERT INTO members (name, email, phone, address) VALUES (:name, :email, :phone, :address)";
        $stmt = $pdo->prepare($sql);

        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":phone", $phone, PDO::PARAM_STR);
        $stmt->bindParam(":address", $address, PDO::PARAM_STR);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Redirect to the add member page with a success message
            header("location: add-member.php?success=1");
        } else {
            header("location: add-member.php?error=1");
        }

        unset($stmt);
    } catch (PDOException $e) {
        // Redirect with a more specific error for duplicate email
        if ($e->errorInfo[1] == 1062) {
            header("location: add-member.php?error=" . urlencode('Error: This email is already registered.'));
        } else {
            header("location: add-member.php?error=" . urlencode($e->getMessage()));
        }
    }

    unset($pdo);
}
?>