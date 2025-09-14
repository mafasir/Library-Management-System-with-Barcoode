<?php
include("../config.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
    <title>Issue Book</title>

    <!-- Bootstrap 5 CSS -->
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

    .main .container {
        background-color: rgba(255, 255, 255, 0.9);
        padding: 20px;
        border-radius: 10px;
        margin-top: 20px;
    }
    </style>
</head>

<body>
    <div style="position: absolute; top: 20px; left: 20px;">
        <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>

    <div class="main">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Issue Book</h2>
                    <p>Please fill this form to issue a book to a member.</p>

                    <!-- Issue Book Requirements: -->
                    <!-- Member ID: To identify which user is borrowing the book. -->
                    <!-- Book ID / Title: To identify the book being issued. -->
                    <!-- Book Copy ID or Barcode: Required if there are multiple copies of the same book. -->
                    <!-- Issue Date: The date on which the book is issued. -->
                    <!-- Due Date: The return deadline based on library policy. -->
                    <!-- Librarian/Admin Authentication: Only authorized users should be able to issue books. -->
                    <!-- Availability Check: Ensure the book copy is available and not already issued. -->
                    <!-- Member Eligibility Check: Ensure the member: Has not exceeded the issue limit. Has no pending fines. Is active. -->
                    
                    <!-- Update Book Status: Mark the book copy as "issued". -->

                    <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                    <?php endif; ?>

                    <form action="issue_book_handler.php" method="POST" novalidate>
                        <div class="form-group">
                            <label for="user_id">User ID</label>
                            <input type="number" class="form-control" id="user_id" name="user_id" placeholder="User ID"
                                min="1" required />
                        </div>

                        <div class="form-group">
                            <label for="barcode">Book Barcode</label>
                            <input type="text" class="form-control" id="barcode" name="barcode"
                                placeholder="Book Barcode" required />
                        </div>

                        <button type="submit" class="btn btn-primary">Issue Book</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>