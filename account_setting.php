<?php
// Include the connection file
require_once('connection.php');
// Include the common functions file
require_once('common_function.php');

// Check if the user is logged in and get their ID, username, and email
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : null;
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';


// Initialize the $user_info variable
$user_info = null;

// Fetch the user details from the database if the user ID is provided in the session
if (!empty($user_id)) {
    // Fetch the user details from the database
    $sql_fetch_user = "SELECT * FROM users WHERE id = ?";
    $stmt_fetch_user = $pdo->prepare($sql_fetch_user);
    $stmt_fetch_user->execute([$user_id]);
    $user_info = $stmt_fetch_user->fetch(PDO::FETCH_ASSOC);

    if (!$user_info) {
        // User not found
        echo "User not found.";
        exit();
    }
}

// Define variables to hold error/success messages
$error_message = '';
$success_message = '';

// If the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_submit'])) {
    // Get data submitted
    $new_username = isset($_POST['txt_new_username']) ? trim($_POST['txt_new_username']) : '';
    $new_email = isset($_POST['txt_new_email']) ? trim($_POST['txt_new_email']) : '';
    $new_password = isset($_POST['txt_new_password']) ? trim($_POST['txt_new_password']) : '';
    $retype_password = isset($_POST['txt_retype_password']) ? trim($_POST['txt_retype_password']) : '';

    // Check if new username or email is filled and already exists
    if (empty($new_username)) {
        $error_message = "Username fields are required.";
    } elseif (empty($new_email)) {
        $error_message = "Email fields are required";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email address.";
    } elseif ($new_username !== $username && usernameExists($new_username)) {
        $error_message = "Username already exists. Please choose a different username.";
    } elseif ($new_email !== $email && emailExists($new_email)) {
        $error_message = "Email already exists. Please use a different email.";
    } elseif (!empty($new_password) && $new_password !== $retype_password) {
        $error_message = "Passwords do not match.";
    } else {
        
        // Hash the new password if provided
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        }

        // Update user information in the database
        $sql_update_user = "UPDATE users SET username = ?, email = ?";
        $params = [$new_username, $new_email];
        
        // Include password update in the query only if a new password is provided
        if (!empty($hashed_password)) {
            $sql_update_user .= ", password = ?";
            $params[] = $hashed_password;
        }
        
        $sql_update_user .= " WHERE id = ?";
        $params[] = $user_id;
        
        $stmt_update_user = $pdo->prepare($sql_update_user);

        if ($stmt_update_user->execute($params)) {
            // Update session variables if the update is successful
            $_SESSION['username'] = $new_username;
            $_SESSION['email'] = $new_email;

            // Set success message
            $success_message = "Account settings updated successfully.";
        } else {
            // Error updating user
            $errorInfo = $stmt_update_user->errorInfo();
            $error_message = "Error updating user: " . $errorInfo[2];
        }
    }
}

?>
<html>
<head>
<title>Account Setting</title>
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
        .form-group input[type="password"],
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

        /* Error message style */
        .red {
            color: red;
        }

        .green {
            color: green;
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
            margin-left: 600px; 
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
                <a href="account_setting.php" class="active">Welcome, <?php echo isset($_SESSION['username']) ? $_SESSION['username']: ''; ?></a>
                <a href="signout.php">Logout</a>
            <?php else : ?>
                <a href="signin.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
    <!-- Main Content -->
    <div class="main-content">
        <h2>Account Setting</h2>
        <form method="POST">
            <div class="form-group">
                <label for="txt_new_username">New Username</label>
                <input type="text" id="txt_new_username" name="txt_new_username" value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>"/>
            </div>
            <div class="form-group">
                <label for="txt_new_email">New Email</label>
                <input type="text" id="txt_new_email" name="txt_new_email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>"/>
            </div>
            <div class="form-group">
                <label for="txt_new_password">New Password</label>
                <input type="password" id="txt_new_password" name="txt_new_password" value=""/>
            </div>
            <div class="form-group">
                <label for="txt_retype_password">Retype Password</label>
                <input type="password" id="txt_retype_password" name="txt_retype_password" value=""/>
            </div>
            <div class="form-group">
                <input type="submit" value="Save Changes" name="btn_submit" class="btn_submit"/>
            </div>
            <?php if(!empty($error_message)): ?>
                <div class="red"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <?php if(!empty($success_message)): ?>
                <div class="green"><?php echo $success_message; ?></div>
            <?php endif; ?>
        </form>
        <script>
            function reloadPage() {
                location.reload();
            }
        </script>
    </div>
</body>
</html>
