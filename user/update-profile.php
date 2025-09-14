<?php
session_start();
include("../config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = trim($_POST["password"]);

    if ($password !== "") {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, password=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $email, $phone, $hashed, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $phone, $id);
    }

    if ($stmt->execute()) {
        $_SESSION["success"] = "Profile updated successfully.";
    } else {
        $_SESSION["error"] = "Update failed.";
    }

    header("Location: dashboard.php");
    exit();
}
?>
