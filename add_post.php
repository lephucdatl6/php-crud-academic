<?php
// Include the connection file
require_once('connection.php');

// Check if the user is logged in and get their ID and username
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Retrieve the username from the user database based on the user ID
if (!empty($user_id)) {
    $sql = "SELECT username FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $post_author = $user['username'];
}

// If the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_submit'])) {
    // Get data submitted
    $post_name = isset($_POST['txt_title']) ? trim($_POST['txt_title']) : '';
    $post_description = isset($_POST['txt_description']) ? trim($_POST['txt_description']) : '';
    $file_path = '';

    // Check if a file was uploaded
    if (isset($_FILES['file_source']) && $_FILES['file_source']['error'] === UPLOAD_ERR_OK) {
        // Handle file upload
        $folder = 'uploads/';  
        $tmpfile = $_FILES['file_source']['tmp_name'];
        $filename = basename($_FILES['file_source']['name']);
        $target_file = $_SERVER['DOCUMENT_ROOT'] . '/StudentQuerySystem/' . $folder . $filename;

        if (move_uploaded_file($tmpfile, $target_file)) {
            $file_path = $folder . $filename;
        } else {
            // Error uploading file
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    }

    // Insert into database if name and description are set
    if (!empty($post_name) && !empty($post_description)) {
        // Prepare the SQL statement
        $sql = "INSERT INTO dashboard (title, author, image, description, user_id) 
                VALUES (?, ?, ?, ?, ?)";
        
        // Create a prepared statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(1, $post_name);
        $stmt->bindParam(2, $post_author);
        $stmt->bindParam(3, $file_path);
        $stmt->bindParam(4, $post_description);
        $stmt->bindParam(5, $user_id);

        // Execute the statement
        if ($stmt->execute()) {
            header('Location: dashboard.php');
            exit(); // Stop execution after redirection
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "Error: " . $errorInfo[2];
        }
        // Set $stmt to null to close the statement
        $stmt = null;
    }
}
?>
<html>
<head>
<title>Add Post</title>
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

        .btn_submit{
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
        <a href="add_post.php" class="active">Add Post</a>
        <a href="feedback.php">Send Feedback</a>
        <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
            <a href="feedback_panel.php">Feedback Panel</a>
        <?php endif; ?>
        <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
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
    <h2>Add Post</h2>
        <form method="POST" enctype="multipart/form-data" onsubmit="return confirm('Are you sure you want to submit this post?');">
            <div class="form-group">
                <label for="txt_title">Title</label>
                <input type="text" id="txt_title" name="txt_title" value="<?php if(isset($_POST['txt_title'])) echo $_POST['txt_title']; ?>"/>
                <?php if(isset($_POST['txt_title']) && trim($_POST['txt_title'])=='') { ?>
                <div class="red">Please input the title</div>
                <?php } ?>
            </div>
            <div class="form-group">
                <label for="txt_description">Description</label>
                <textarea id="txt_description" name="txt_description" cols="30" rows="5"><?php if(isset($_POST['txt_description'])) echo $_POST['txt_description']; ?></textarea>
                <?php if(isset($_POST['txt_description']) && trim($_POST['txt_description'])=='') { ?>
                <div class="red">Please input the description</div>
                <?php } ?>
            </div>
                <!-- Image preview -->
                <?php if (!empty($post['image'])) : ?>
                    <img id="image_preview" src="<?php echo $post['image']; ?>" width="150" height="auto" />
                <?php else : ?>
                    <img id="image_preview" src="#" alt="Preview" style="display:none;" width="150" height="auto" />
                <?php endif; ?>                
                <input type="file" id="file_source" name="file_source" accept="image/*" onchange="previewImage(event)" />
                <input type="submit" value="Update" name="btn_submit" class="btn_submit"/> 
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