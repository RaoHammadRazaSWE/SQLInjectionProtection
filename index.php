<?php
// Start the session:
session_start();

// Check if session exists, if yes, redirect to dashboard:
if (isset($_SESSION['id'])) {
    header("location: dashboard.php");
    exit; // Terminate script execution after redirection
}

// Database connection details:
$servername = "localhost";
$username = "root";
$password = "";
$db_name = "sql-test";

// Create Connection:
$conn = new mysqli($servername, $username, $password, $db_name);

// Check connection:
if ($conn->connect_errno) {
    die("Connection failed: " . $conn->connect_errno);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Injection</title>
    <link rel="stylesheet" href="style.css"> <!-- Fixed typo in rel attribute -->
</head>

<body>
    <div id="main">
        <div id="header">
            <h1>User Login</h1>
        </div>
        <div id="content">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" autocomplete="off" />
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" />
                </div>
                <input type="submit" class="btn" name="login" id="submit" value="Submit" />
            </form>
            <?php
            if (isset($_POST['login'])) {
                if (empty($_POST['username']) || empty($_POST['password'])) {
                    echo '<div class="message error">Please fill all the fields.</div>';
                } else {
                    // Sanitize user input:
                    $username = $conn->real_escape_string($_POST['username']);
                    $password = $conn->real_escape_string($_POST['password']);

                    # ' or '' = ' SQL injection:
                    
                    // Prepare and execute SQL statement with parameterized query:
                    $sql = $conn->prepare("SELECT * FROM users WHERE user_email=? AND password=?");
                    if (!$sql) {
                        die("Error in preparing SQL statement: " . $conn->error);
                    }
                    $success = $sql->bind_param("ss", $username, $password);
                    if (!$success) {
                        die("Error in binding parameters: " . $sql->error);
                    }
                    $success = $sql->execute();
                    if (!$success) {
                        die("Error in executing SQL statement: " . $sql->error);
                    }

                    // Get result:
                    $result = $sql->get_result()->fetch_all(MYSQLI_ASSOC);

                    if (count($result) > 0) {
                        // Start session and set session variables:
                        $_SESSION["id"] = $result[0]['id'];
                        $_SESSION["first_name"] = $result[0]['first_name'];
                        header("location: dashboard.php");
                        exit; // Terminate script execution after redirection
                    } else {
                        echo "<div class='message error'>Email and Password Not Matched.</div>";
                    }
                }
            }
            ?>
        </div>
    </div>
</body>

</html>
