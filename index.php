<!-- login page for user and admin -->

<!-- admin login -->
<?php 
// set session lifetime (7 days)
ini_set('session.gc_maxlifetime', 604800);
session_set_cookie_params(604800);
session_start();
require 'vendor/autoload.php';

$uri = "mongodb+srv://rootadmin:rootadmin@cluster0.ge5ruc5.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";

// Connect MongoDB Client
$client = new MongoDB\Client($uri);

// Select database and collection
$db = $client->wap_system;
$collection = $db->admin;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']);
    $password = $_POST['password'];

    // Search by admin_email or admin_id
    $admin = $collection->findOne([
        '$or' => [
            ['_id' => $identifier],
            ['admin_email' => $identifier]
        ]
    ]);

    if ($admin && password_verify($password, $admin['password'])) {
        //login successful
        $_SESSION['admin_id'] =  $admin['_id'];
        $_SESSION['admin_email'] = $admin['admin_email'];
        // track activity
        $_SESSION['last_activity'] = time();
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Invalid id/email or password";
    }
}
?>

<!-- simple form for backend to do testing first -->
<!DOCTYPE html>
<html lang="en">
<body>
    <h2>Admin Login</h2>
    
    <!-- timeout message -->
    <?php if (isset($_GET['timeout']) && $_GET['timeout'] == 1): ?>
        <p>Your session has expired due to inactivity. Please log in again.</p>
    <?php endif; ?>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>

    <form method="POST" action="">
        <label for="identifier">Admin ID or Email:</label><br>
        <input type="text" name="identifier" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>