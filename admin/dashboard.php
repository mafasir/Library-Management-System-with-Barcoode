<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once '../config.php';

// Fetch total number of books
$total_books = 0;
$sql_books = "SELECT COUNT(*) FROM books";
try {
    $stmt_books = $pdo->prepare($sql_books);
    $stmt_books->execute();
    $total_books = $stmt_books->fetchColumn();
} catch (PDOException $e) {
    // Log or handle the error appropriately
    error_log("Error fetching total books: " . $e->getMessage());
}

// Fetch total number of members
$total_members = 0;
$sql_members = "SELECT COUNT(*) FROM members";
try {
    $stmt_members = $pdo->prepare($sql_members);
    $stmt_members->execute();
    $total_members = $stmt_members->fetchColumn();
} catch (PDOException $e) {
    error_log("Error fetching total members: " . $e->getMessage());
}

// Fetch total number of currently issued books
$total_issued_books = 0;
$sql_issued_books = "SELECT COUNT(*) FROM transactions WHERE return_date IS NULL";
try {
    $stmt_issued_books = $pdo->prepare($sql_issued_books);
    $stmt_issued_books->execute();
    $total_issued_books = $stmt_issued_books->fetchColumn();
} catch (PDOException $e) {
    error_log("Error fetching total issued books: " . $e->getMessage());
}

// Close connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: "Lato", sans-serif;
            background-image: url('https://images.unsplash.com/photo-1532012197267-da84d127e765?q=80&w=1000&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
        }

        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #2c3e50; /* Midnight Blue background */
            overflow-x: hidden;
            padding-top: 20px;
        }

        .sidebar a {
            padding: 6px 8px 6px 16px;
            text-decoration: none;
            font-size: 20px;
            color: #818181;
            display: block;
        }

        .sidebar a:hover {
            color: #f1f1f1;
        }
        
        .sidebar .brand {
            color: #f1f1f1;
            font-size: 25px;
            text-align: center;
            margin-bottom: 20px;
        }

        .main {
            margin-left: 250px; /* Same as the width of the sidebar */
            padding: 0px 10px;
        }

        .main .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .card {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            transition: all 0.3s ease-in-out;
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2), 0 12px 40px 0 rgba(0, 0, 0, 0.19);
        }
    </style>
</head>
<body>

<div class="sidebar">
  <div class="brand">
    
    Admin Panel <span style="height: 10px; width: 10px; background-color: #00ff00; border-radius: 50%; display: inline-block; margin-left: 5px;"></span> <span style="font-size: 12px; color: #00ff00;">Online</span>
  </div>
  <a href="dashboard.php">Dashboard</a>
  <a href="books.php">Books</a>
  <a href="add-book.php">Add Book</a>
  <a href="members.php">Members</a>
  <a href="add-member.php">Add Member</a>
  <a href="issue-book.php">Issue Book</a>
  <a href="return-book.php">Return Book</a>
  <a href="transactions.php">Transactions</a>
  
  <a href="fines.php">Fines</a>
  <a href="logout.php" class="btn btn-light" style="background-color: #f8d7da; color: #721c24;">Logout</a>
</div>

<div class="main">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mt-5">Dashboard <small style="font-size: 0.6em;">Control Panel</small></h1>
                <hr style="border-top: 1px solid #333;">
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Books</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_books; ?></h5>
                        <p class="card-text">Number of books in the library.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total Members</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_members; ?></h5>
                        <p class="card-text">Number of registered members.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <a href="dashboard.php?page=issued_books" style="text-decoration: none;">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-header">Issued Books</div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $total_issued_books; ?></h5>
                            <p class="card-text">Books currently issued to members.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    <?php
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
        if ($page == 'issue_book') {
            include 'issue-book.php';
        } elseif ($page == 'issued_books') {
            include 'transactions.php';
        }
    }
    ?>
</body>
</html>
