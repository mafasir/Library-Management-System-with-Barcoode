<?php
require_once '../config.php';

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Search functionality
$search_term = "";
if (isset($_GET['search'])) {
    $search_term = trim($_GET['search']);
    $sql = "SELECT * FROM members WHERE name LIKE :search OR email LIKE :search";
} else {
    $sql = "SELECT * FROM members ORDER BY id DESC";
}

try {
    $stmt = $pdo->prepare($sql);
    if (!empty($search_term)) {
        $stmt->bindValue(':search', '%' . $search_term . '%', PDO::PARAM_STR);
    }
    $stmt->execute();
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Members</title>
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
    <img src="" style="vertical-align: middle; margin-right: 5px;">
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
                <h2 class="mt-5">Manage Members</h2>

                <!-- Search Form -->
                <form class="form-inline mb-3" action="members.php" method="get">
                    <input class="form-control mr-sm-2" type="search" placeholder="Search by Name or Email" name="search" value="<?php echo htmlspecialchars($search_term); ?>">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                    <a href="members.php" class="btn btn-outline-secondary ml-2">Clear</a>
                </form>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($members): ?>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($member['id']); ?></td>
                                    <td><?php echo htmlspecialchars($member['name']); ?></td>
                                    <td><?php echo htmlspecialchars($member['email']); ?></td>
                                    <td><?php echo htmlspecialchars($member['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($member['address']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No members found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
