<?php
require_once '../config.php';

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Search functionality
$search_term = "";
if (isset($_GET['search'])) {
    $search_term = trim($_GET['search']);
    $sql = "SELECT t.id, b.title, bc.barcode, m.name AS member_name, t.issue_date, t.due_date, t.return_date, t.fine 
            FROM transactions t
            JOIN books b ON t.book_id = b.id
            JOIN book_copies bc ON t.book_id = bc.book_id
            JOIN members m ON t.member_id = m.id
            WHERE (b.title LIKE :search OR m.name LIKE :search OR b.barcode LIKE :search) AND t.return_date IS NULL
            ORDER BY t.issue_date DESC";
} else {
    $sql = "SELECT t.id, b.title, bc.barcode, m.name AS member_name, t.issue_date, t.due_date, t.return_date, t.fine 
            FROM transactions t
            JOIN books b ON t.book_id = b.id
            JOIN book_copies bc ON t.book_id = bc.book_id
            JOIN members m ON t.member_id = m.id
            WHERE t.return_date IS NULL
            ORDER BY t.issue_date DESC";
}

try {
    $stmt = $pdo->prepare($sql);
    if (!empty($search_term)) {
        $stmt->bindValue(':search', '%' . $search_term . '%', PDO::PARAM_STR);
    }
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Transaction History - Library Management</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin-top: 2rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .table-hover tbody tr:hover {
            background-color: #e9f5ff;
        }
        .not-returned {
            color: #dc3545;
            font-weight: 600;
        }
        .fine-amount {
            font-weight: 600;
            color: #28a745;
        }
        .search-btn, .clear-btn {
            min-width: 90px;
        }
        @media (max-width: 576px) {
            .table-responsive {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="bi bi-journal-check"></i> Transaction History</h4>
        </div>
        <div class="card-body">

            <!-- Back Button -->
            <button type="button" class="btn btn-outline-primary mb-3" onclick="history.back();">
                &larr; Back
            </button>

            <!-- Search Form -->
            <form class="row g-3 align-items-center mb-4" action="transactions.php" method="get" novalidate>
                <div class="col-sm-8">
                    <input 
                        type="search" 
                        name="search" 
                        class="form-control" 
                        placeholder="Search by Book Title, Member Name, or Barcode" 
                        value="<?php echo htmlspecialchars($search_term); ?>" 
                        aria-label="Search transactions"
                    />
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success search-btn">Search</button>
                    <a href="transactions.php" class="btn btn-secondary clear-btn ms-2">Clear</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Book Title</th>
                            <th scope="col">Book Barcode</th>
                            <th scope="col">Member Name</th>
                            <th scope="col">Issue Date</th>
                            <th scope="col">Due Date</th>
                            <th scope="col">Return Date</th>
                            <th scope="col">Fine (Rs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($transactions): ?>
                            <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <th scope="row"><?php echo htmlspecialchars($transaction['id']); ?></th>
                                    <td><?php echo htmlspecialchars($transaction['title']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['barcode']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['member_name']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($transaction['issue_date'])); ?></td>
                                    <td><?php echo date('d M Y', strtotime($transaction['due_date'])); ?></td>
                                    <td>
                                        <?php 
                                        if ($transaction['return_date']) {
                                            echo date('d M Y', strtotime($transaction['return_date']));
                                        } else {
                                            echo '<span class="not-returned">Not Returned</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <span class="fine-amount">
                                            <?php echo number_format($transaction['fine'], 2); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted fst-italic">No transactions found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
