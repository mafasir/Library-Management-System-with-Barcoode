<?php
require_once '../config.php';

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// SQL query to fetch all transactions with unpaid fines
$sql = "SELECT t.id, b.title, m.name AS member_name, t.due_date, t.return_date, t.fine
        FROM transactions t
        JOIN books b ON t.book_id = b.id
        JOIN members m ON t.member_id = m.id
        WHERE t.fine > 0
        ORDER BY t.due_date ASC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $fines = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Overdue Fines</title>
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

        .main .container-fluid {
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
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mt-5">Overdue Fines</h2>
                <table class="table table-bordered table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Transaction S.No</th>
                            <th>Book Title</th>
                            <th>Member Name</th>
                            <th>Due Date</th>
                            <th>Return Date</th>
                            <th>Fine Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($fines): ?>
                            <?php foreach ($fines as $fine): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($fine['id']); ?></td>
                                    <td><?php echo htmlspecialchars($fine['title']); ?></td>
                                    <td><?php echo htmlspecialchars($fine['member_name']); ?></td>
                                    <td><?php echo htmlspecialchars($fine['due_date']); ?></td>
                                    <td><?php echo htmlspecialchars($fine['return_date'] ?? 'Not Returned'); ?></td>
                                    <td>RS<?php echo htmlspecialchars(number_format($fine['fine'], 2)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No unpaid fines found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
