<?php
// Include the connection file
require_once('connection.php');

// Check if the user is logged in and get their ID and username
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Count number of posts
$sql = "SELECT COUNT(*) as post_count FROM dashboard";
$stmt = $pdo->query($sql);
$post_count = $stmt->fetch(PDO::FETCH_ASSOC)['post_count'];

// Count number of users
$sql = "SELECT COUNT(*) as user_count FROM users";
$stmt = $pdo->query($sql);
$user_count = $stmt->fetch(PDO::FETCH_ASSOC)['user_count'];

// Function to get the count of comments for a post
function getCommentCount($post_id) {
    global $pdo;
    
    // SQL query to count comments for a specific post
    $sql = "SELECT COUNT(*) as comment_count FROM comments WHERE post_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id]);
    $comment_count = $stmt->fetch(PDO::FETCH_ASSOC)['comment_count'];
    return $comment_count;
}
?>
<html>
<head>
<title>Dashboard</title>
    <style>
        .container {
            width: 80%;
            margin: 0 auto;
        }

        .username {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .add_post-button{
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 15px;
            border-radius: 5px;
            cursor: pointer;
            vertical-align: middle;
            position: absolute;
            top: 10px;
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

        #side-bar {
            position: fixed;
            flex: 1;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            margin-top: 70px;
            margin-right: 1400px;
            font-size: 18px;
        }
        
        /* Main content area */
        .main-content {
            position: relative;
            max-width: 600px;
            width: 100%;
            margin: 300px;
            margin-top: 50px;
            padding: 20px;
            box-sizing: border-box;  
        }

        /* Post container */
        .post {
            position: relative;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 40px;
            min-height: 200px;
        }

        .post img {
            max-width: 100%;
            height: auto;
            margin-bottom: 50px;
        }

        .post-title {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .post-author {
            margin-bottom: 10px;
        }

        .post-created {
            margin-bottom: 10px;
        }

        .post-actions {
            position: absolute;
            bottom: 10px;
            left: 10px;
        }
        
        .edit-button,
        .delete-button,
        .comment-button {
            padding: 8px 25px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            vertical-align: middle;
        }

        .edit-button{
            position: absolute;
            bottom: 10px;
            left: 330px;
        }

        .delete-button {
            position: absolute;
            bottom: 10px;
            left: 430px;
        }

        .comment-button {
            position: absolute;
            bottom: 10px;
            left: 10px;
            margin-right: 10px;
            white-space: nowrap 
        }

        /* Reset default padding and margin for all elements */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
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

    </style>
    <?php require_once('connection.php'); ?>
</head>
<body>
    <!-- Top Navigation Bar -->
    <div class="topnav">
        <a href="dashboard.php" class="active">Dashboard</a>
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
                <a href="signin.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
    <!-- Counter section -->
    <div id="side-bar">
        <p>Total Posts: <?php echo $post_count; ?></p>         
        <p>Total Users: <?php echo $user_count; ?></p>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php
        // Fetch and display posts ordered by created_at in descending order
        $sql = "SELECT * FROM dashboard ORDER BY created_at DESC";
        $result = $pdo->query($sql);
        if ($result->rowCount() > 0) {
            // Output data of each row
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                ?>         
                <div class="post">
                    <div class="post-title">
                        <a href="<?php echo 'post_detail.php?id=' . $row['id']; ?>">
                            <?php echo $row['title']; ?>
                        </a>
                    </div>
                    <div class="post-author">Author: <?php echo ucfirst($row['author']); ?></div>
                    <div class="post-created">Created at: <?php echo $row['created_at']; ?></div><br>
                    <?php if (!empty($row['image'])) : ?>
                        <img src="<?php echo $row['image']; ?>" width="600" height="auto"/>
                    <?php endif; ?>
                    <div class="post-actions">
                        <!-- Edit Button -->
                        <?php if ($is_admin || $user_id == $row['user_id']) : ?>
                            <form method="GET" action="edit_post.php">
                                <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="edit-button" name="btn_edit">Edit</button>
                            </form>
                        <?php endif; ?>

                        <!-- Delete Button -->
                        <?php if ($is_admin || $user_id == $row['user_id']) : ?>
                            <form method="POST" action="delete_post.php" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="delete-button" name="btn_delete">Delete</button>
                            </form>
                        <?php endif; ?>
                        <!-- Comment Button -->
                        <form method="GET" action="post_detail.php">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <?php $comment_count = getCommentCount($row['id']); ?>
                            <button type="submit" class="comment-button" name="btn_comment">
                                <?php echo ($comment_count == 0) ? "(0)" : "($comment_count)"; ?> Comment
                            </button>
                        </form>
                    </div>
                </div>
            <?php
            }
        } else {
            echo "No posts found.";
        }
        ?>
    </div>
</body>
</html>
