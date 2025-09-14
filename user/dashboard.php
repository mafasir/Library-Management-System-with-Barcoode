<?php
require_once '../config.php';


// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["member_loggedin"]) || $_SESSION["member_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Fetch currently issued books for the logged-in member
$member_id = $_SESSION["member_id"];
$sql = "SELECT b.title, b.image_path, t.issue_date, t.due_date, t.fine
        FROM transactions t
        JOIN books b ON t.book_id = b.id
        WHERE t.member_id = :member_id AND t.return_date IS NULL";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
    $stmt->execute();
    $issued_books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}

unset($pdo);
?>++

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: "Lato", sans-serif;
            background-image: url('https://images.unsplash.com/photo-1521587760476-6c12a4b040da?q=80&w=1000&auto=format&fit=crop');
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
  <div class="brand"><?php echo htmlspecialchars($_SESSION["member_name"]); ?></div>
  <a href="dashboard.php">Dashboard</a>
  <a href="books.php">Available Books</a>
  <a href="history.php">Borrowing History</a>
  <a href="edit_profile.php">Edit Profile</a>
  <a href="logout.php" class="btn btn-light" style="background-color: #f8d7da; color: #721c24;">Logout</a>
</div>

<div class="main">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Dashboard <small style="font-size: 0.6em;">Control Panel</small></h1>
                
                <h3 class="mt-4"><a href="history.php">Currently Issued Books</a></h3>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Book Title</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Fine</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($issued_books): ?>
                            <?php foreach ($issued_books as $book): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $imagePath = '../' . htmlspecialchars($book['image_path']);
                                        $defaultImagePath = '../assets/book_images/default_book.png';
                                        if (!empty($book['image_path']) && file_exists($imagePath)) {
                                            echo '<img src="' . $imagePath . '" alt="Book Image" width="50">';
                                        } else {
                                            echo '<img src="' . $defaultImagePath . '" alt="No Image Available" width="50">';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                                    <td><?php echo htmlspecialchars($book['issue_date']); ?></td>
                                    <td><?php echo htmlspecialchars($book['due_date']); ?></td>
                                    <td>RS<?php echo htmlspecialchars(number_format($book['fine'], 2)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">You have no books currently issued.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    
</body>
</html>
