<?php
require_once '../config.php';

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["member_loggedin"]) || $_SESSION["member_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Search functionality
$search_term = "";
if (isset($_GET['search'])) {
    $search_term = trim($_GET['search']);
    $sql = "SELECT * FROM books WHERE available = 1 AND (title LIKE :search OR author LIKE :search OR isbn LIKE :search) ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM books WHERE available = 1 ORDER BY id DESC";
}

try {
    $stmt = $pdo->prepare($sql);
    if (!empty($search_term)) {
        $stmt->bindValue(':search', '%' . $search_term . '%', PDO::PARAM_STR);
    }
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Books</title>
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
  <div class="brand"><?php echo htmlspecialchars($_SESSION["member_name"]); ?></div>
  <a href="dashboard.php">Dashboard</a>
  <a href="books.php">Available Books</a>
  <a href="history.php">Borrowing History</a>
  <a href="edit_profile.php">Edit Profile</a>
  <a href="logout.php" class="btn btn-light" style="background-color: #f8d7da; color: #721c24;">Logout</a>
</div>

<div class="main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2>Available Books</h2>
                
                <!-- Search Form -->
                <form class="form-inline mb-3" action="books.php" method="get">
                    <input class="form-control mr-sm-2" type="search" placeholder="Search by Title, Author, ISBN" name="search" value="<?php echo htmlspecialchars($search_term); ?>">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                    <a href="books.php" class="btn btn-outline-secondary ml-2">Clear</a>
                </form>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>ISBN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($books): ?>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($book['id']); ?></td>
                                    <td>
                                        <?php if (!empty($book['image_path'])): ?>
                                            <img src="../<?php echo htmlspecialchars($book['image_path']); ?>" alt="Book Image" width="50">
                                        <?php else: ?>
                                            No Image
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No available books found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
