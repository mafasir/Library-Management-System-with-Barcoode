<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once '../config.php';

$id = $title = $author = $isbn = "";
$title_err = $author_err = $isbn_err = "";

// Process update operation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter a title.";
    } else {
        $title = trim($_POST["title"]);
    }

    // Validate author
    if (empty(trim($_POST["author"]))) {
        $author_err = "Please enter an author.";
    } else {
        $author = trim($_POST["author"]);
    }

    // Validate ISBN
    $isbn = trim($_POST["isbn"]); // ISBN can be empty
    $category_id = trim($_POST["category_id"]);

    // Get ID from hidden input
    $id = $_POST["id"];

    // Check input errors before updating in database
    if (empty($title_err) && empty($author_err)) {
        // Prepare an update statement
        $sql = "UPDATE books SET title = :title, author = :author, isbn = :isbn WHERE id = :id";

        if ($stmt = $pdo->prepare($sql)) {
            // Bind parameters
            $stmt->bindParam(":title", $param_title, PDO::PARAM_STR);
            $stmt->bindParam(":author", $param_author, PDO::PARAM_STR);
            $stmt->bindParam(":isbn", $param_isbn, PDO::PARAM_STR);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);

            // Set parameters
            $param_title = $title;
            $param_author = $author;
            $param_isbn = $isbn;
            $param_id = $id;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                header("location: books.php");
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }

    // Close connection
    unset($pdo);
} else {
    // Check existence of id parameter before processing
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        // Get URL parameter
        $id = trim($_GET["id"]);

        // Prepare a select statement
        $sql = "SELECT * FROM books WHERE id = :id";
        if ($stmt = $pdo->prepare($sql)) {
            // Bind parameters
            $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);

            // Set parameters
            $param_id = $id;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, no need to use while loop */
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Retrieve individual field value
                    $title = $row["title"];
                    $author = $row["author"];
                    $isbn = $row["isbn"];
                } else {
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        unset($stmt);

        // Close connection
        unset($pdo);
    } else {
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Book</title>
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
    background: #243B55;
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
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="dashboard.php">Library Management</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="add-book.php">Add Book</a></li>
                <li class="nav-item active"><a class="nav-link" href="books.php">Books</a></li>
                <li class="nav-item"><a class="nav-link" href="add-member.php">Add Member</a></li>
                <li class="nav-item"><a class="nav-link" href="members.php">Members</a></li>
                <li class="nav-item"><a class="nav-link" href="issue-book.php">Issue Book</a></li>
                <li class="nav-item"><a class="nav-link" href="return-book.php">Return Book</a></li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="logout.php" class="btn btn-light" style="background-color: #f8d7da; color: #721c24;">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mt-5">Edit Book</h2>
                <p>Please edit the input values and submit to update the book record.</p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $title; ?>">
                        <span class="invalid-feedback"><?php echo $title_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>Author</label>
                        <input type="text" name="author" class="form-control <?php echo (!empty($author_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $author; ?>">
                        <span class="invalid-feedback"><?php echo $author_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>ISBN</label>
                        <input type="text" name="isbn" class="form-control <?php echo (!empty($isbn_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $isbn; ?>">
                        <span class="invalid-feedback"><?php echo $isbn_err; ?></span>
                    </div>
                    <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                    <input type="submit" class="btn btn-primary" value="Submit" style="background: #0052cc;">
                    <a href="books.php" class="btn btn-secondary ml-2">Cancel</a>
                </form>

                <h3 class="mt-5">Manage Book Copies</h3>
                <div id="bookCopiesList">
                    <!-- Book copies will be loaded here via AJAX or PHP -->
                </div>
                <hr>
                <h4>Add New Book Copies</h4>
                <form id="addCopyForm" action="add_book_copy_handler.php" method="post">
                    <input type="hidden" name="book_id" value="<?php echo $id; ?>"/>
                    <div id="barcodeFields">
                        <div class="form-group">
                            <label>Barcode</label>
                            <input type="text" name="barcodes[]" class="form-control" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-info mb-3" id="addBarcode">Add Another Barcode</button>
                    <button type="submit" class="btn btn-success" style="background: #0052cc;">Add Copies</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        let barcodeCount = 1;
        document.getElementById('addBarcode').addEventListener('click', function() {
            barcodeCount++;
            const barcodeDiv = document.getElementById('barcodeFields');
            const newField = document.createElement('div');
            newField.classList.add('form-group');
            newField.innerHTML = `
                <label>Barcode ${barcodeCount}</label>
                <input type="text" name="barcodes[]" class="form-control" required>
            `;
            barcodeDiv.appendChild(newField);
        });

        function loadBookCopies() {
            const bookId = <?php echo json_encode($id); ?>;
            fetch(`fetch_book_copies.php?book_id=${bookId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('bookCopiesList').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error loading book copies:', error);
                    document.getElementById('bookCopiesList').innerHTML = '<p class="text-danger">Error loading book copies.</p>';
                });
        }

        // Load copies on page load
        loadBookCopies();

        // Barcode Modal JavaScript
        $('#barcodeModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var barcodeSrc = button.data('barcode-src'); // Extract info from data-* attributes
            var modal = $(this);
            modal.find('#largeBarcodeImage').attr('src', barcodeSrc);
            
            // Extract barcode number from the src (e.g., ../assets/barcodes/12345.png -> 12345)
            var barcodeNumber = barcodeSrc.split('/').pop().split('.')[0];
            
            // Clear previous scanned output
            modal.find('#scannedBarcodeOutput').val('');

            // Set up scan button click handler
            $('#scanButton').off('click').on('click', function() {
                modal.find('#scannedBarcodeOutput').val(barcodeNumber);
            });
        });
    </script>

    <!-- Barcode Modal -->
    <div class="modal fade" id="barcodeModal" tabindex="-1" role="dialog" aria-labelledby="barcodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="barcodeModalLabel">Book Barcode</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="largeBarcodeImage" src="" alt="Large Barcode" class="img-fluid mb-3">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="scannedBarcodeOutput" placeholder="Scanned Barcode" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="scanButton">Scan</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>