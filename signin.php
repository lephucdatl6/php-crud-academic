<?php
// Include the connection file
require_once('connection.php');

// Check if the user is logged in and get their ID and username
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Check if the user submitted the login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['txt_email'];
    $password = $_POST['txt_pass'];

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        // Using prepared statements to prevent SQL injection
        $sql = "SELECT * FROM users WHERE email=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // User found
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['username'] = $user['username'];

                // Set role if user is admin
                if ($user['role'] === 'admin') {
                    $_SESSION['role'] = 'admin';
                }

                // Redirect to dashboard or any other page
                header('Location: dashboard.php');
                exit();
            } else {
                // Incorrect password
                $error = "Incorrect password.";
            }
        } else {
            // User not found
            $error = "User not found.";
        }
    }
}
?>
<html>
<head>
    <title>Signin</title>
    <style>
        .red {color: red;}
        h2{
            text-align: center
        }
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            width: 300px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: lightblue;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .link {
            text-align: center;
            margin-top: 10px;
        }

        .link a {
            color: blue;
            text-decoration: none;
        }

        .link a:hover {
            text-decoration: underline;
        }

        .error-message {
            margin-top: 10px;
            color: red;
        }
    </style>
</head>
<body>
<form method="POST">
    <h2>Login</h2>
    <div>
        <input type="text" placeholder="Email" name="txt_email"/>
    </div>
    <div>
        <input type="password" placeholder="Password" name="txt_pass"/>
    </div>
    <?php
    if (isset($error)) {
        echo "<div class='error-message'>$error</div>";
    }
    ?>
    <div>
        <input type="submit" value="Login" name="btn_submit"/>
    </div>
    <div class="link">
        <a href="signup.php">New Registration</a>
    </div>
</form>
</body>
</html>
