<?php
require_once('connection.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or handle unauthorized access
    header('Location: signin.php');
    exit();
}

if (isset($_POST['btn_delete'])) {
    // Get the post ID to delete
    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : null;

    // Get the user ID from session
    $user_id = $_SESSION['user_id'];

    // Check if the user is an admin
    $is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

    // Proceed with deletion if the user is an admin or if they have permission to delete the post
    if ($is_admin || checkUserPermission($pdo, $post_id, $user_id)) {
        // Fetch the post to get the image file path
        $post = fetchPost($pdo, $post_id);

        if ($post) {
            // Check if the image path is not empty and file exists
            if (!empty($post['image'])) {
                $image_path = $_SERVER['DOCUMENT_ROOT'] . '/StudentQuerySystem/' . $post['image'];
                if (file_exists($image_path)) {
                    unlink($image_path); // Delete the file
                }
            }
        }

        // Delete the post from the database
        $success = deletePost($pdo, $post_id);

        if ($success) {
            // Post deleted successfully, reload the dashboard page
            echo "<script>window.location.href = 'dashboard.php';</script>";
            exit();
        } else {
            echo "Error deleting post."; 
        }
    } else {
        // User does not have permission to delete this post
        echo "You do not have permission to delete this post.";
    }
}

// Function to check user permission to delete a post
function checkUserPermission($pdo, $post_id, $user_id) {
    $sql_check_permission = "SELECT * FROM dashboard WHERE id = ? AND user_id = ?";
    $stmt_check_permission = $pdo->prepare($sql_check_permission);
    $stmt_check_permission->execute([$post_id, $user_id]);
    return $stmt_check_permission->rowCount() > 0;
}

// Function to fetch post details
function fetchPost($pdo, $post_id) {
    $sql_fetch_post = "SELECT * FROM dashboard WHERE id = ?";
    $stmt_fetch_post = $pdo->prepare($sql_fetch_post);
    $stmt_fetch_post->execute([$post_id]);
    return $stmt_fetch_post->fetch(PDO::FETCH_ASSOC);
}

// Function to delete a post
function deletePost($pdo, $post_id) {
    $sql_delete_post = "DELETE FROM dashboard WHERE id = ?";
    $stmt_delete_post = $pdo->prepare($sql_delete_post);
    return $stmt_delete_post->execute([$post_id]);
}
?>

<br><br>
<!-- Add a button to return to dashboard.php -->
<form method="GET" action="dashboard.php">
    <button type="submit">Return to Dashboard</button>
</form>
