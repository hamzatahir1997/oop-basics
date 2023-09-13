<?php
if ($_SESSION["English"] == false || $_SESSION["English"] == "false") {
    include "languages/spanish/messages.php";
} else {
    include "languages/english/messages.php";
}

class Database {
    private $connection;

    public function __construct($host, $username, $password, $database_name) {
        $this->connection = new mysqli($host, $username, $password, $database_name);

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function executeQuery($query) {
        $result = $this->connection->query($query);

        if (!$result) {
            die("Query failed: " . $this->connection->error);
        }

        return $result;
    }
}

class PasswordReset {
    private $con;
    private $errors = [];
    private $success_message;

    public function __construct($con) {
        $this->con = $con;
    }

    public function resetPassword($email) {
        $email = $this->con->real_escape_string($email);
        $query = "SELECT email FROM teacher_data WHERE email = '$email'";
        $result = $this->con->executeQuery($query);

        if ($result->num_rows <= 0) {
            array_push($this->errors, $em1);
        } else {
            $token = bin2hex(random_bytes(50));
            $query = "INSERT INTO password_resets (email, token) VALUES ('$email', '$token')";
            $this->con->executeQuery($query);

            // Send an email to the user with the token in a link
            $to = $email;
            $subject = "Reset your password";
            $msg = "Hi there, click on this <a href=\"www.hotspotmapky.com/teacher%20panel/reset_password.php?token=" . $token . "\">link</a> to reset your password on our site";
            $msg = wordwrap($msg, 70);
            $headers = "From: support@liamcrest.com\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            mail($to, $subject, $msg, $headers);

            $this->success_message = $sm2;
        }
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getSuccessMessage() {
        return $this->success_message;
    }
}

class User {
    private $con;
    private $errors = [];

    public function __construct($con) {
        $this->con = $con;
    }

    public function newPassword($newPassword, $confirmPassword, $token) {
        if ($newPassword !== $confirmPassword) {
            array_push($this->errors, $em3);
        }

        $token = $this->con->real_escape_string($token);
        $query = "SELECT email FROM password_resets WHERE token = '$token' LIMIT 1";
        $result = $this->con->executeQuery($query);
        $row = $result->fetch_assoc();
        $email = $row['email'];
        $newPassword = md5($newPassword);
        $query = "UPDATE teacher_data SET upassword = '$newPassword' WHERE email = '$email'";
        $this->con->executeQuery($query);

        header("Location: login.php");
        exit();
    }

    public function getErrors() {
        return $this->errors;
    }
}

// Database connection details
$host = "localhost"; /* Host name */
$username = "root"; /* User */
$password = ""; /* Password */
$database_name = "kentucky_db"; /* Database name */

$con = new Database($host, $username, $password, $database_name);


$passwordReset = new PasswordReset($con);
$user = new User($con);

if (isset($_POST['reset-password'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $passwordReset->resetPassword($email);
}

if (isset($_POST['new_password'])) {
    $newPassword = mysqli_real_escape_string($con, $_POST['upassword']);
    $confirmPassword = mysqli_real_escape_string($con, $_POST['urpassword']);
    $token = $_POST["token_id"];
    $user->newPassword($newPassword, $confirmPassword, $token);
}


