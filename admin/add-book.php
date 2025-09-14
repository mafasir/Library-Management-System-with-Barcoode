<?php
session_start();
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
<title>Add Book - Library Management</title>

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #4a90e2, #0052cc);
    min-height: 100vh;
    margin: 0;
    display: flex;
  }
  /* Sidebar */
  .sidebar {
    position: fixed;
    top: 0; left: 0;
    width: 250px;
    height: 100vh;
    background: #2c3e50;
    color: #ccc;
    padding-top: 1.5rem;
    display: flex;
    flex-direction: column;
    z-index: 1000;
    box-shadow: 3px 0 12px rgba(0,0,0,0.3);
  }
  .sidebar .brand {
    font-size: 1.5rem;
    font-weight: 700;
    color: #f1f1f1;
    text-align: center;
    margin-bottom: 2rem;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
  }
  .sidebar .brand img {
    width: 30px;
    border-radius: 5px;
  }
  .sidebar nav a {
    display: block;
    color: #b0c7e2;
    padding: 12px 25px;
    text-decoration: none;
    font-size: 1.1rem;
    transition: background-color 0.3s ease, color 0.3s ease;
    border-left: 4px solid transparent;
  }
  .sidebar nav a:hover, .sidebar nav a.active {
    background-color: #1a2a45;
    color: #fff;
    border-left-color: #00bcd4;
  }
  .sidebar nav a.btn-logout {
    margin-top: auto;
    margin-bottom: 20px;
    background: #f44336;
    color: white !important;
    font-weight: 600;
    border-radius: 5px;
    text-align: center;
  }
  .sidebar nav a.btn-logout:hover {
    background: #d32f2f;
  }

  /* Main content */
  main {
    margin-left: 250px;
    padding: 40px 50px;
    width: calc(100% - 250px);
    background: rgba(255, 255, 255, 0.95);
    min-height: 100vh;
    overflow-y: auto;
  }
  main h2 {
    font-weight: 700;
    color: #243B55;
    margin-bottom: 15px;
    letter-spacing: 1px;
  }
  main p {
    color: #555;
    margin-bottom: 30px;
    font-size: 1.1rem;
  }
  form label {
    font-weight: 600;
    color: #243B55;
  }
  form .form-control,
  form .form-control-file {
    border-radius: 6px;
    box-shadow: none;
    border: 1.5px solid #ccc;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
  }
  form .form-control:focus,
  form .form-control-file:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 8px rgba(74, 144, 226, 0.5);
    outline: none;
  }
  #addBarcode {
    min-width: 180px;
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
  form .btn-secondary {
    padding: 12px 25px;
    font-weight: 600;
    border-radius: 6px;
  }

  @media (max-width: 768px) {
    .sidebar {
      width: 60px;
      padding-top: 15px;
    }
    .sidebar .brand {
      font-size: 0;
    }
    .sidebar .brand img {
      margin: 0 auto;
      display: block;
    }
    .sidebar nav a {
      font-size: 0;
      padding: 10px 0;
      text-align: center;
      border-left: none;
    }
    .sidebar nav a:hover, .sidebar nav a.active {
      border-left: none;
      background-color: #1a2a45;
      color: #00bcd4;
    }
    main {
      margin-left: 60px;
      padding: 20px;
      width: calc(100% - 60px);
    }
  }
</style>
</head>
<body>

<div class="sidebar">
  <div class="brand">
            <img src=""
                style="vertical-align: middle; margin-right: 5px;">
            Admin Panel <span
                style="height: 10px; width: 10px; background-color: #00ff00; border-radius: 50%; display: inline-block; margin-left: 5px;"></span>
            <span style="font-size: 12px; color: #00ff00;">Online</span>
        </div>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="books.php">Books</a>
    <a href="add-book.php" class="active">Add Book</a>
    <a href="members.php">Members</a>
    <a href="add-member.php">Add Member</a>
    <a href="issue-book.php">Issue Book</a>
    <a href="return-book.php">Return Book</a>
    <a href="transactions.php">Transactions</a>
    
    <a href="fines.php">Fines</a>
    <a href="logout.php" class="btn-logout">Logout</a>
  </nav>
</div>

<main>
  <h2>Add a New Book</h2>
  <p>Please fill this form to add a book to the database.</p>

  <form action="add_book_handler.php" method="post" enctype="multipart/form-data" novalidate>
    <div class="mb-3">
      <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
      <input
        type="text"
        name="title"
        id="title"
        class="form-control"
        placeholder="Enter book title"
        required
      />
    </div>

    <div class="mb-3">
      <label for="author" class="form-label">Author <span class="text-danger">*</span></label>
      <input
        type="text"
        name="author"
        id="author"
        class="form-control"
        placeholder="Enter author name"
        required
      />
    </div>

    <div class="mb-3">
      <label for="isbn" class="form-label">ISBN</label>
      <input
        type="text"
        name="isbn"
        id="isbn"
        class="form-control"
        placeholder="Enter ISBN (optional)"
      />
    </div>

    <div class="mb-3">
      <label for="book_image" class="form-label">Book Image</label>
      <input
        type="file"
        name="book_image"
        id="book_image"
        class="form-control"
        accept="image/*"
      />
    </div>

    <div class="mb-3">
      <label for="num_copies" class="form-label">Number of Copies <span class="text-danger">*</span></label>
      <input
        type="number"
        name="num_copies"
        id="num_copies"
        class="form-control"
        placeholder="Enter number of copies"
        required
        min="1"
        value="1"
      />
    </div>

    <div class="d-flex gap-3">
      <button type="submit" class="btn btn-primary flex-fill">Submit</button>
      <a href="dashboard.php" class="btn btn-secondary flex-fill">Cancel</a>
    </div>
  </form>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



</body>
</html>
