<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Member</title>
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
    </style>
</head>
<body>

<div class="sidebar">
  <div class="brand">
    <img src=""  style="vertical-align: middle; margin-right: 5px;">
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
                <h2 class="mt-5">Add a New Member</h2>
                <p>Please fill this form to add a member to the library.</p>
                
                <?php
                if (isset($_GET['success']) && $_GET['success'] == 1) {
                    echo '<div class="alert alert-success">Member added successfully.</div>';
                }
                if (isset($_GET['error'])) {
                    echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
                }
                ?>

                <form action="add_member_handler.php" method="post">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control"></textarea>
                    </div>
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
