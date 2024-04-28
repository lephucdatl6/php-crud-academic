<?php
// Include the connection file
require_once('connection.php');

// Check if the user is logged in and get their ID and username
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';


// Initialize the $post variable
$post = null;

// Fetch the post details from the database if the post ID is provided in the URL
if (isset($_GET['post_id'])) {
    // Get the post ID from the URL
    $post_id = $_GET['post_id'];

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
}

// If the form was submitted for updating the post
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_update'])) {
    // Get the new title and description from the form
    $new_title = isset($_POST['txt_title']) ? trim($_POST['txt_title']) : '';
    $new_description = isset($_POST['txt_description']) ? trim($_POST['txt_description']) : '';

    // Check if a new file is being uploaded
    if (isset($_FILES['file_source']) && $_FILES['file_source']['error'] === UPLOAD_ERR_OK) {
        // Handle file upload
        $folder = 'uploads/';
        $filename = basename($_FILES['file_source']['name']);
        $target_file = $_SERVER['DOCUMENT_ROOT'] . '/StudentQuerySystem/' . $folder . $filename;

        if (move_uploaded_file($_FILES['file_source']['tmp_name'], $target_file)) {
            $file_path = $folder . $filename;  // File path to store in the database

            // Update the post with the new details including the image path
            $sql_update_post = "UPDATE dashboard SET title = ?, description = ?, image = ? WHERE id = ?";
            $stmt_update_post = $pdo->prepare($sql_update_post);

            if ($stmt_update_post->execute([$new_title, $new_description, $file_path, $post_id])) {
                // Redirect back to dashboard after successful update
                header('Location: dashboard.php');
                exit();
            } else {
                // Error updating post
                $errorCode = $stmt_update_post->errorCode();
                echo "Error updating post: SQLSTATE error code: " . $errorCode;
            }
        } else {
            // Error uploading file
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        // No new file uploaded, update without changing image
        $sql_update_post = "UPDATE dashboard SET title = ?, description = ? WHERE id = ?";
        $stmt_update_post = $pdo->prepare($sql_update_post);

        if ($stmt_update_post->execute([$new_title, $new_description, $post_id])) {
            // Redirect back to dashboard after successful update
            header('Location: dashboard.php');
            exit();
        } else {
            // Error updating post
            $errorInfo = $stmt_update_post->errorInfo();
            echo "Error updating post: " . $errorInfo[2];
        }
    }
}
?>
<html>
<head>
<title>Edit Post</title>
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

        /* Form fields */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }

        .form-group textarea {
            height: 150px;
        }

        .form-group input[type="file"] {
            margin-top: 5px;
        }

        /* Error message style */
        .red {
            color: red;
        }
        
        .btn_update{
            padding: 8px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 15px;
            border-radius: 5px;
            border: none; 
            cursor: pointer;
            bottom: 10px;
            margin-left: 650px; 
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
                <a href="signin.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
    <!-- Main Content -->
    <div class="main-content">
    <h2>Edit Post</h2>
        <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()" name="editForm">
            <div class="form-group">
                <label for="txt_title">Title</label>
                <input type="text" id="txt_title" name="txt_title" value="<?php echo $post['title']; ?>"/>
            </div>
            <div class="form-group">
                <label for="txt_description">Description</label>
                <textarea id="txt_description" name="txt_description" cols="30" rows="5"><?php echo $post['description']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="file_source"></label>
                <!-- Image preview -->
                <?php if (!empty($post['image'])) : ?>
                    <img id="image_preview" src="<?php echo $post['image']; ?>" width="150" height="auto" />
                <?php else : ?>
                    <img id="image_preview" src="#" alt="Preview" style="display:none;" width="150" height="auto" />
                <?php endif; ?>
                <input type="file" id="file_source" name="file_source" accept="image/*" onchange="previewImage(event)" />
            </div>
            <div class="form-group">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <input type="submit" value="Update" name="btn_update" class="btn_update"/>
            </div>
        </form>
    </div>
    <script>
        // JavaScript function to preview the selected image
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('image_preview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>
