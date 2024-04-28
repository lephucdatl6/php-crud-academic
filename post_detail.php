<?php
// Include the connection file
require_once('connection.php');

// Check if the user is logged in and get their ID and username
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

// Check if the post ID is provided in the URL
if (isset($_GET['id'])) {
    // Get the post ID from the URL
    $post_id = $_GET['id'];

    // Fetch the post details from the database
    $sql_fetch_post = "SELECT * FROM dashboard WHERE id = ?";
    $stmt_fetch_post = $pdo->prepare($sql_fetch_post);
    $stmt_fetch_post->execute([$post_id]);
    $post = $stmt_fetch_post->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        // Post not found
        echo "Post not found.";
        exit();
    }
} else {
    // No post ID provided
    echo "Post ID not provided.";
    exit();
}

// Function to fetch comments for a specific post
function fetchComments($pdo, $post_id) {
    $sql = "SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC"; // Fetch comments in descending order
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// If the form was submitted for adding a comment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_comment'])) {
    // Get the comment text from the form
    $comment_text = isset($_POST['txt_comment']) ? trim($_POST['txt_comment']) : '';

    // Insert the comment into the database
    if (!empty($comment_text)) {
        $sql_insert_comment = "INSERT INTO comments (post_id, username, comment_text) VALUES (?, ?, ?)";
        $stmt_insert_comment = $pdo->prepare($sql_insert_comment);
        if ($stmt_insert_comment->execute([$post_id, $username, $comment_text])) {
            // Redirect back to post detail page after successful comment
            header("Location: post_detail.php?id=$post_id");
            exit();
        } else {
            echo "Error adding comment.";
        }
    } else {
        // Set a flag to indicate that the form was submitted but the comment was empty
        $comment_error = true;
    }
}

// Delete Comment Logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_delete'])) {
    // Check if the user is the owner of the post or the commenter
    $comment_id = $_POST['comment_id'];
    $sql_check_comment_owner = "SELECT username FROM comments WHERE id = ?";
    $stmt_check_comment_owner = $pdo->prepare($sql_check_comment_owner);
    $stmt_check_comment_owner->execute([$comment_id]);
    $comment_owner = $stmt_check_comment_owner->fetchColumn();

    if ($comment_owner === $username || $post['author'] === $username || $is_admin) { // Using $is_admin here
        // Delete the comment
        $sql_delete_comment = "DELETE FROM comments WHERE id = ?";
        $stmt_delete_comment = $pdo->prepare($sql_delete_comment);
        if ($stmt_delete_comment->execute([$comment_id])) {
            // Redirect back to post detail page after successful deletion
            header("Location: post_detail.php?id=$post_id");
            exit();
        } else {
            echo "Error deleting comment.";
        }
    } else {
        echo "You do not have permission to delete this comment.";
    }
}

?>
<html>
<head>
<title>Post Detail</title>
    <style>
        .username {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .dashboard-button{
            position: absolute;
            bottom: 20px;
            left: 10px;
        }

        /* Center align the page */
        body {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
            margin: 0;
        }

        /* Main content area */
        .main-content {
            max-width: 950px;
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            padding-top: 60px;
        }

        /* Post container */
        .post {
            flex: 2;
            margin-right: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
        }

        .post img {
            max-width: 100%;
            height: auto;
            margin-bottom: 60px;
        }

        /* Top Navigation Bar */
        .topnav {
            overflow: hidden;
            background-color: #f0f0f0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        /* Styling for navigation links */
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

        /* Sidebar for comments */
        .sidebar {
            flex: 1;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            position: relative;
            width: 500px;
        }

        .comment-container {
            max-height: 500px;
            overflow-y: auto;
            margin-bottom: 90px;
            word-wrap: break-word;
        }

        .comment {
            margin-bottom: 5px;
            position: relative;
        }

        .comment .delete-button {
            position: absolute;
            top: 0;
            right: 0;
        }

        /* Add Comment Form */
        .add-comment-form {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #fff;
            padding: 10px;
            box-sizing: border-box;
            padding: 8px 16px;
        }

        .btn_comment{
            padding: 5px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            margin-left: 180px;
            width: 100px;
            cursor: pointer;
            border-radius: 5px;
            border: none;
        }

        textarea[name="txt_comment"] {
            width: 100%;
            height: 50px;
            border-radius: 5px;
            resize: none;
            margin-bottom: 5px; 
        }

        .error-message {
            color: red;
            position: absolute; 
            top: 60px; 
            left: 10px; 
        }

        .delete-button {
            background: transparent;
            border: none !important;
            font-size:0;
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <div class="topnav">
        <a href="dashboard.php">Dashboard</a>
        <a href="add_post.php">Add Post</a>
        <a href="feedback.php">Send Feedback</a>
        <?php if ($is_admin) : ?>
            <a href="feedback_panel.php">Feedback Panel</a>
            <a href="module_manager.php">Module Manager</a>
        <?php endif; ?>
        <!-- Username and logout button -->
        <div class="user-info">
            <?php if (!empty($username)) : ?>
                <a href="account_setting.php">Welcome, <?php echo $username; ?></a>
                <a href="signout.php">Logout</a>
            <?php else : ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
    <!-- Display Username -->
    <div class="username">
    <?php if (!empty($username)) : ?>
        User: <?php echo $username; ?>
    <?php endif; ?>
    </div>
    <div class="main-content">
    <!-- Post Section -->
    <div class="post">
        <!-- Display Post Details -->
        <div>
            <h2><?php echo $post['title']; ?></h2>
            <p>Author: <?php echo $post['author']; ?></p>
            <div>Description:</div>
            <p><?php echo $post['description']; ?></p>
            <img src="<?php echo $post['image']; ?>" />

        </div>
    </div>
    <!-- Sidebar for Comments -->
    <div class="sidebar">
        <h2>Comments</h2>
        <!-- Comment Container (Scrollable) -->
        <div class="comment-container">
            <!-- Display Comments -->
            <?php
            $comments = fetchComments($pdo, $post_id);
            if (!empty($comments)) {
                foreach ($comments as $comment) {
                    ?>
                        <div class="comment">
                        <p>
                            <strong><?php echo $comment['username']; ?> (<?php echo $comment['created_at']; ?>)</strong><br> <?php echo $comment['comment_text']; ?>
                            <?php if ($comment['username'] === $username || $post['author'] === $username || $user_role === 'admin') : ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                    <!-- Use an icon for the delete button -->
                                    <button type="submit" name="btn_delete" class="delete-button">
                                        <img src="Icon/Trash Icon.svg" alt="Delete" style="width: 20px; height: 20px;">
                                    </button>
                                </form>
                            <?php endif; ?>
                        </p>
                        </div>
                    <?php
                }
            } else {
                echo "<p>No comments yet.</p>";
            }
            ?>
        </div>
        <!-- Add Comment Form -->
        <div class="add-comment-form">
            <form method="POST" style="position: relative;">
                <textarea name="txt_comment" placeholder="Add your comment here"></textarea>
                <input type="submit" value="Post Comment" name="btn_comment" class="btn_comment">
            </form>
            <?php if (isset($comment_error)) : ?>
                <div class="error-message">Please enter a comment.</div>
            <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
