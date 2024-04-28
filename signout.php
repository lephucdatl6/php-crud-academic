<html>
<head>
<title>Logout</title>
    <style>
        /* Center align the page */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Style the container */
        .container {
            text-align: center;
        }

        /* Style the logout button */
        .logout-button {
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        session_start();
        session_destroy();
        echo "Logged out successfully.";
        ?>
        <br><br>
        <form action="signin.php">
            <button type="submit" class="logout-button">Login Back</button>
        </form>
    </div>
</body>
</html>
