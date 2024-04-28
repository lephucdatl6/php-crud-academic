<?php
// Include the connection file
require_once('connection.php');

// Check if the user is logged in and get their ID and username
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Check if the user is logged in and has admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: signin.php'); // Redirect unauthorized users to sign-in page
    exit();
}

// Fetch post data from the database
$sql = "SELECT id, title, author, created_at FROM dashboard ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<html>
<head>
<title>Module Manager</title>
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
            text-align: center;
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
        <a href="feedback_panel.php">Feedback Panel</a>
        <a href="module_manager.php" class="active">Module Manager</a>
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
        <h2>Module Manager</h2>
        <table>
            <thead>
                <tr>
                    <th>Post ID</th>
                    <th>Title</th> 
                    <th>Author</th>
                    <th>Date Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post) : ?>
                    <tr>
                        <td><?php echo $post['id']; ?></td>
                        <td><?php echo $post['title']; ?></td>
                        <td><?php echo $post['author']; ?></td>
                        <td><?php echo $post['created_at']; ?></td>
                        <td>
                        <form method="POST" action="delete_post.php" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
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