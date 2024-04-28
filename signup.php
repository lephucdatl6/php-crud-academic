<?php
require 'common_function.php';
session_start();

// Function to insert user into the database
function insertUser($username, $email, $password) {
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Database connection details
    $host = 'localhost';
    $dbname = 'coursework';
    $user = 'root';
    $dbPassword = ''; 

    // Establish a PDO database connection
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $dbPassword);
        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Error: Could not connect. " . $e->getMessage());
    }

    try {
        // Prepare an SQL statement for execution
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR); 

        // Execute the prepared statement
        $stmt->execute();

        return "User registered successfully.";
    } catch(PDOException $e) {
        die("Error: Could not execute. " . $e->getMessage());
    }
}
?>
<html>
<head>
<title>Signup</title>
    <style>
        .red {color: red;}
        .success {color: green;}
        h2{
            text-align: center;
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
    </style>
</head>
<body>
    <form method="POST" onsubmit="return validateForm()">
        <div>
            <h2>Signup</h2>
            <input type="text" placeholder="Username" name="txt_name"/>
        </div>
        <div>
            <input type="text" placeholder="Email" name="txt_email"/>
        </div>
        <div>
            <input type="password" placeholder="Password" name="txt_pass"/>
        </div>
        <div>
            <input type="password" placeholder="Retype Password" name="txt_retype_pass"/>
        </div>
        </div>
    <?php
        $successMessage = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['txt_name'];
            $email = $_POST['txt_email'];
            $password = $_POST['txt_pass'];
            $retypePassword = $_POST['txt_retype_pass'];

            if (empty($username) || empty($email) || empty($password) || empty($retypePassword)) {
                $error = "All fields are required.";
            } elseif ($password !== $retypePassword) {
                $error = "Passwords do not match.";
            } elseif (usernameExists($username)) {
                $error = "Username already exists. Please choose a different username.";
            } elseif (emailExists($email)) {
                $error = "Email already exists. Please use a different email.";
            } else {
                insertUser($username, $email, $password);

                // Send email only if email is not empty and is valid
                if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emailResult = sendConfirmationEmail($email);
                    $successMessage = 'User registered successfully. ' . $emailResult;
                } else {
                    $error = "Invalid email address.";
                }
            }

            if (isset($error)) {
                echo "<div class='red'>$error</div>";
            }
        }
    ?>
    <?php
        if (!empty($successMessage)) {
            echo "<div class='success'>$successMessage</div>";
        }
    ?>
        <div>
        <div>
            <input type="submit" value="Register" name="btn_submit"/>
        </div>            
        <div class="link">
            <a href="signin.php">Click to Login</a>
        </div>
    </form>
</body>
</html>
