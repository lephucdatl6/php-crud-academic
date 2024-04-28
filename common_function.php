<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer autoloader
require 'Composer\vendor\autoload.php'; 

// Function to check if email exists in the database
function emailExists($email) {
    // Database connection details
    $host = 'localhost';
    $dbname = 'coursework';
    $user = 'root';
    $password = '';

    // Establish a PDO database connection
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Error: Could not connect. " . $e->getMessage());
    }

    // Check if email exists
    $email = $pdo->quote($email);
    $query = "SELECT * FROM users WHERE email = $email";
    $result = $pdo->query($query);

    // Close the connection
    unset($pdo);

    if ($result->rowCount() > 0) {
        return true; // Email exists
    } else {
        return false; // Email does not exist
    }
}

// Function to check if username exists in the database
function usernameExists($username) {
    // Database connection details
    $host = 'localhost';
    $dbname = 'coursework';
    $user = 'root';
    $password = '';

    // Establish a PDO database connection
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Error: Could not connect. " . $e->getMessage());
    }

    // Check if username exists
    $username = $pdo->quote($username);
    $query = "SELECT * FROM users WHERE username = $username";
    $result = $pdo->query($query);

    // Close the connection
    unset($pdo);

    if ($result->rowCount() > 0) {
        return true; 
    } else {
        return false;
    }
}

// Function to send confirmation email
function sendConfirmationEmail($email) {
    $mail = new PHPMailer(true);

    try {
        //Server settings for Gmail SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'davidgw2004@gmail.com'; // Gmail address
        $mail->Password   = 'bvxltjvmrjkzwozy';       // App password
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        //Recipients
        $mail->setFrom('davidgw2004@gmail.com', 'David'); // Sender's email and name
        $mail->addAddress($email);                         // Recipient's email

        // Content
        $mail->isHTML(true);                               // Set email format to HTML
        $mail->Subject = 'Confirmation Email';
        $mail->Body    = 'Thank you for registering! Your account has been created.';

        $mail->send();
        return 'Email has been sent.';
    } catch (Exception $e) {
        return 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo;
    }
}

// Function to send feedback email to admin
function sendFeedbackEmail($admin_email, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        //Server settings for Gmail SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'davidgw2004@gmail.com'; // Gmail address
        $mail->Password   = 'bvxltjvmrjkzwozy';       // App password
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        //Recipients
        $mail->setFrom('davidgw2004@gmail.com', 'David'); // Sender's email and name
        $mail->addAddress($admin_email);                         // Admin's email

        // Content
        $mail->isHTML(true);                               // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return 'Email has been sent.';
    } catch (Exception $e) {
        return 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo;
    }
}
?>
