<?php
// Show all errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// === Replace the path below with the correct path to your config.php ===
require_once("../config.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";
$error = "";

// Make sure $conn exists and is your MySQLi connection
if (!$conn) {
    die("Database connection failed");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));

    $sql = "UPDATE users SET name=?, email=?, username=?, phone_number=?, address=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssi", $name, $email, $username, $phone_number, $address, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Profile updated successfully.";
        } else {
            $error = "Failed to update profile.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Failed to prepare statement.";
    }

    // Password update logic
    if (!empty($_POST['new_password'])) {
        $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
        $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
        $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

        // Fetch current hashed password
        $sql_fetch_pass = "SELECT password FROM users WHERE id=?";
        $stmt_fetch_pass = mysqli_prepare($conn, $sql_fetch_pass);
        mysqli_stmt_bind_param($stmt_fetch_pass, "i", $user_id);
        mysqli_stmt_execute($stmt_fetch_pass);
        mysqli_stmt_bind_result($stmt_fetch_pass, $hashed_password);
        mysqli_stmt_fetch($stmt_fetch_pass);
        mysqli_stmt_close($stmt_fetch_pass);

        if (password_verify($current_password, $hashed_password)) {
            if ($new_password === $confirm_password) {
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql_update_pass = "UPDATE users SET password=? WHERE id=?";
                $stmt_update_pass = mysqli_prepare($conn, $sql_update_pass);
                mysqli_stmt_bind_param($stmt_update_pass, "si", $new_hashed_password, $user_id);
                if (mysqli_stmt_execute($stmt_update_pass)) {
                    $msg .= " Password updated successfully.";
                } else {
                    $error .= " Failed to update password.";
                }
                mysqli_stmt_close($stmt_update_pass);
            } else {
                $error .= " New password and confirm password do not match.";
            }
        } else {
            $error .= " Current password is incorrect.";
        }
    }
} else {
    // Fetch current user data
    $sql = "SELECT name, email, username, phone_number, address FROM users WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $name, $email, $username, $phone_number, $address);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    } else {
        die("Failed to prepare statement for user data");
    }
}

// Fetch current user data (for initial form display)
$sql = "SELECT name, email, username, phone_number, address FROM users WHERE id=?";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $name, $email, $username, $phone_number, $address);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    die("Failed to prepare statement for user data");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width:500px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4>Edit Profile</h4>
        </div>
        <div class="card-body">
            <?php if ($msg): ?>
                <div class="alert alert-success"><?php echo $msg; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" required value="<?php echo htmlspecialchars($name); ?>">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($email); ?>">
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required value="<?php echo htmlspecialchars($username); ?>">
                </div>

                <div class="mb-3">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($phone_number ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" id="address" name="address" class="form-control" value="<?php echo htmlspecialchars($address ?? ''); ?>">
                </div>

                <hr>
                <h5>Change Password</h5>
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                </div>

                <button type="submit" class="btn btn-success w-100">Update Profile</button>
                <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Back to Dashboard</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
