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
    $sql = "SELECT b.id, b.title, b.author, b.isbn, b.image_path, COUNT(bc.id) AS total_copies, ROUP_CONCAT(CASE WHEN bc.status = 'available' THEN bc.barcode ELSE NULL END SEPARATOR ',') as barcodes, SUM(CASE WHEN bc.status = 'available' THEN 1 ELSE 0 END) AS available_copies FROM books b LEFT JOIN book_copies bc ON b.id = bc.book_id WHERE b.title LIKE :search OR b.author LIKE :search OR b.isbn LIKE :search GROUP BY b.id ORDER BY b.id DESC";
} else {
    $sql = "SELECT b.id, b.title, b.author, b.isbn, b.image_path, COUNT(bc.id) AS total_copies, GROUP_CONCAT(CASE WHEN bc.status = 'available' THEN bc.barcode ELSE NULL END SEPARATOR ',') as barcodes, SUM(CASE WHEN bc.status = 'available' THEN 1 ELSE 0 END) AS available_copies FROM books b LEFT JOIN book_copies bc ON b.id = bc.book_id GROUP BY b.id ORDER BY b.id DESC";
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
    <title>Manage Books</title>
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
        background-color: #2c3e50;
        /* Midnight Blue background */
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
        margin-left: 250px;
        /* Same as the width of the sidebar */
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
            <img src="" 
                style="vertical-align: middle; margin-right: 5px;">
            Admin Panel <span
                style="height: 10px; width: 10px; background-color: #00ff00; border-radius: 50%; display: inline-block; margin-left: 5px;"></span>
            <span style="font-size: 12px; color: #00ff00;">Online</span>
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
                    <h2>Manage Books</h2>

                    <!-- Search Form -->
                    <form class="form-inline mb-3" action="books.php" method="get">
                        <input class="form-control mr-sm-2" type="search" placeholder="Search by Title, Author, ISBN"
                            name="search" value="<?php echo htmlspecialchars($search_term); ?>">
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
                                <th>Total Copies</th>
                                <th>Available Copies</th>
                                <th>Barcode</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($books): ?>
                            <?php foreach ($books as $book): ?>
                            <tr>
                                <!-- <td>
                                        <pre><?= print_r($book) ?></pre>
                                    </td> -->
                                <td><?php echo htmlspecialchars($book['id']); ?></td>
                                <td>
                                    <?php if (!empty($book['image_path'])): ?>
                                    <img src="../<?php echo htmlspecialchars($book['image_path']); ?>" alt="Book Image"
                                        width="50">
                                    <?php else: ?>
                                    No Image
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                                <td><?php echo htmlspecialchars($book['total_copies']); ?></td>
                                <td><?php echo htmlspecialchars($book['available_copies']); ?></td>
                                <td>
                                    <?php 
                                            $barcodes = explode(',', $book['barcodes']);
                                            foreach ($barcodes as $barcode) {
                                                if (!empty($barcode)) {
                                                    echo '<img class="mb-2" onclick="showBarcodeDetail(\''.$barcode.'\')" src="../assets/barcodes/' . htmlspecialchars($barcode) . '.png" alt="Barcode" height="30"><br/>';
                                                }
                                            }
                                        ?>
                                </td>
                                <td>
                                    <a href="edit_book.php?id=<?php echo $book['id']; ?>"
                                        class="btn btn-sm btn-primary">Edit</a>
                                    <a href="delete_book_handler.php?id=<?php echo $book['id']; ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this book and all its copies?');">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No books found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Barcode Modal -->
        <div class="modal fade" id="barcodeModal" tabindex="-1" aria-labelledby="barcodeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="barcodeModalLabel">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <script>
        function showBarcodeDetail(barcode) {
            $('#barcodeModalLabel').text('Barcode: ' + barcode);
            $('#barcodeModal .modal-body').html('<img src="../assets/barcodes/' + barcode +
                '.png" alt="Barcode" class="img-fluid">');
            $('#barcodeModal').modal('show');
        }
        </script>

</body>

</html>