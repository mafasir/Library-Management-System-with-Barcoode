<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Return Book</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<style>
  body {
    background-image: url('https://source.unsplash.com/random/?library,books'); /* Random library image */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    padding: 40px;
  }
  .container {
    max-width: 600px;
    background: #fff;
    padding: 30px 40px;
    border-radius: 10px;
    box-shadow: 0 6px 20px rgb(0 0 0 / 0.1);
    margin: 0 auto;
  }
  h2 {
    margin-bottom: 25px;
    color: #007bff;
    font-weight: 700;
  }
  /* Fixed position back button top-left */
  .back-button {
    position: fixed;
    top: 20px;
    left: 20px;
    z-index: 1050;
  }
  form button[type="submit"] {
    background: #0052cc;
    border: none;
    font-weight: 600;
    padding: 12px 0;
    font-size: 1.1rem;
    border-radius: 6px;
    transition: background-color 0.3s ease;
  }
  form button[type="submit"]:hover {
    background: #003d99;
  }
</style>
</head>
<body>

<!-- Fixed Back Button -->
<a href="dashboard.php" class="btn btn-outline-primary d-inline-flex align-items-center back-button" style="gap: 6px; font-weight: 600; font-size: 1rem;">
  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 1-.5.5H3.707l4.147 4.146a.5.5 0 0 1-.708.708l-5-5a.5.5 0 0 1 0-.708l5-5a.5.5 0 1 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
  </svg>
  Back to Dashboard
</a>

<div class="container">
  <h2>Return Book</h2>

  <form action="return_book_handler.php" method="POST" novalidate>
    <div class="mb-3">
      <label for="transaction_id" class="form-label">Transaction ID</label>
      <input type="number" class="form-control" id="transaction_id" name="transaction_id" required placeholder="Enter transaction ID">
    </div>

    <div class="mb-3">
      <label for="barcode" class="form-label">Book Barcode</label>
      <input type="text" class="form-control" id="barcode" name="barcode" required placeholder="Enter book barcode">
    </div>

    <button type="submit" class="btn btn-primary" style="background: #0052cc;">Return Book</button>
  </form>
</div>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
