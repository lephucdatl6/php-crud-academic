<?php
// Include the connection file
require_once('connection.php');

// Check if the user is logged in and get their ID and username
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Include the common functions file
require_once('common_function.php');

// If the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_submit'])) {
    // Get data submitted
    $feedback_subject = isset($_POST['txt_subject']) ? trim($_POST['txt_subject']) : '';
    $feedback_message = isset($_POST['txt_message']) ? trim($_POST['txt_message']) : '';

    // Insert into database if subject and message are set
    if (!empty($feedback_subject) && !empty($feedback_message)) {
        // Prepare the SQL statement
        $sql = "INSERT INTO feedback (user_id, subject, message) 
                VALUES (?, ?, ?)";
        
        // Create a prepared statement
        $stmt = $pdo->prepare($sql);

        // Get user ID from session
        $user_id = $_SESSION['user_id'];

        // Bind parameters
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $feedback_subject);
        $stmt->bindParam(3, $feedback_message);

        // Execute the statement
        if ($stmt->execute()) {
            $success_message = "Feedback sent successfully.";
            
            $admin_email = 'davidgw2004@gmail.com';
            
            // Send email notification to admin
            $email_subject = "A new feedback has been received.";
            $email_body = "";
            $email_body .= "Subject: $feedback_subject<br>";
            $email_body .= "Message: $feedback_message";
            
            sendFeedbackEmail($admin_email, $email_subject, $email_body);
        } else {
            $error_message = "Error sending feedback.";
        }
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
            margin-left: 650px; 
            }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <div class="topnav">
        <a href="dashboard.php">Dashboard</a>
        <a href="add_post.php">Add Post</a>
        <a href="feedback.php" class="active">Send Feedback</a>
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
    <h2>Send Feedback</h2>
        <form method="POST" onsubmit="return confirm('Are you sure you want to submit this feedback?');">
            <div class="form-group">
                <label for="txt_subject">Subject</label>
                <input type="text" id="txt_subject" name="txt_subject" value="<?php if(isset($_POST['txt_subject'])) echo $_POST['txt_subject']; ?>"/>
                <?php if(isset($_POST['txt_subject']) && trim($_POST['txt_subject'])=='') { ?>
                <div class="red">Please input the subject</div>
                <?php } ?>
                </div>
            <div class="form-group">
            <label for="txt_message">Message</label>
                <textarea id="txt_message" name="txt_message" cols="30" rows="5"><?php if(isset($_POST['txt_message'])) echo $_POST['txt_message']; ?></textarea>
                <?php if(isset($_POST['txt_message']) && trim($_POST['txt_message'])=='') { ?>
                <div class="red">Please input the message</div>
                <?php } ?>
                    </div>
            <div class="form-group">
                <input type="submit" value="Submit" name="btn_submit" class="btn_submit"/>
            </div>
            <?php if(isset($success_message)): ?>
                <div class="green"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if(isset($error_message)): ?>
                <div class="red"><?php echo $error_message; ?></div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
