<?php
// Include the connection file
require_once('connection.php');

// Check if the user is logged in and get their ID and username
session_start(); // Start the session here
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Check if the user is logged in and has admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: signin.php'); // Redirect unauthorized users to sign-in page
    exit();
}

// Function to delete feedback
function deleteFeedback($pdo, $feedbackId) {
    $sql = "DELETE FROM feedback WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$feedbackId]);
}

// Handle delete action if feedback ID is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_delete'])) {
    $feedbackId = $_POST['feedback_id'];
    if (deleteFeedback($pdo, $feedbackId)) {
        // Feedback deleted successfully
        header('Location: feedback_panel.php'); // Redirect to refresh the page
        exit();
    } else {
        // Error occurred while deleting feedback
        echo '<script>alert("Error occurred while deleting feedback.");</script>';
    }
}

// Fetch all feedback from the database with usernames, ordered by created_at in descending order
$sql = "SELECT f.id, f.user_id, u.username, f.subject, f.message, f.created_at
        FROM feedback f
        JOIN users u ON f.user_id = u.id
        ORDER BY f.created_at DESC";
$stmt = $pdo->query($sql);
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<html>
<head>
<title>Feedback Panel</title>
    <style>
        /* Reset default padding and margin for all elements */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Top Navigation Bar */
            .topnav {
            overflow: hidden;
            background-color: #333;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000; 
        }

        .topnav a {
            float: left;
            display: block;
            color: #fff;
            text-align: center;
            padding: 15px 20px;
            text-decoration: none;
        }

        .topnav a:hover {
            background-color: #ddd; 
            color: #333;
        }

        .topnav a.active {
            background-color: #ddd; 
            color: black; 
        }

        .topnav-right {
            float: right;
        }

        /* Style for user info section */
        .user-info {
            float: right;
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        /* Styling for logout button */
        .user-info form {
            margin-left: 10px;
        }

        .user-info button {
            padding: 10px;
            background-color: #ddd; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
        }

        h2{
            text-align: center
        }

        /* Main Content */
        .main-content {
            max-width: 800px;
            width: 80%;
            margin: 70px auto 0;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Feedback table style */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn_delete{
            padding: 8px 20px;
            text-decoration: none;
            display: inline-block;
            border-radius: 5px;
            border: none; 
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <div class="topnav">
            <a href="dashboard.php">Dashboard</a>
            <a href="add_post.php">Add Post</a>
            <a href="feedback.php">Send Feedback</a>
            <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
                <a href="feedback_panel.php" class="active">Feedback Panel</a>
            <?php endif; ?>
            <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
                <a href="module_manager.php">Module Manager</a>
            <?php endif; ?>
        <!-- Username and logout button -->
        <div class="user-info">
            <?php if (!empty($_SESSION['username'])) : ?>
                <a href="account_setting.php">Welcome, <?php echo $username; ?></a>
                <a href="signout.php">Logout</a>
            <?php else : ?>
                <a href="signin.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
    <!-- Main Content -->
    <div class="main-content">
    <h2>Feedback Panel</h2>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th> 
                <th>Subject</th>
                <th>Message</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($feedbacks as $feedback) : ?>
                <tr>
                    <td><?php echo $feedback['user_id']; ?></td>
                    <td><?php echo $feedback['username']; ?></td>
                    <td><?php echo $feedback['subject']; ?></td>
                    <td><?php echo $feedback['message']; ?></td>
                    <td><?php echo $feedback['created_at']; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="feedback_id" value="<?php echo $feedback['id']; ?>">
                            <button type="submit" name="btn_delete" class="btn_delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</body>
</html>
