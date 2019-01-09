<?php
include_once '../../php.php';

sec_session_start(); // Our custom secure way of starting a PHP session.

if (isset($_POST['email'], $_POST['p'])) {
    $email = $_POST['email'];
    $password = $_POST['p']; // The hashed password.

    if (login($email, $password, $db) == true) {
        // Login success
        $stmt = $db->conn->prepare("INSERT INTO login_success(userId, ip, date) VALUES (?, ?, ?)");
        $stmt->bind_param('iss', $_SESSION['user_id'],  get_ip(), $date = date('Y-m-d H:i:s'));
        $stmt->execute();

        // Log the login
        $log = fopen("../../data/log.txt", "a");
        fwrite($log, date("Y/m/d h:i:sa") . " - " . $_SESSION['username'] . " has signed in.\n");
        fclose($log);
        header('Location: ../../bookingRequests');
        exit();
    } else {
        // Login failed
        header('Location: ../../login?error=y');
        exit();
    }
} else {
    // The correct POST variables were not sent to this page.
    echo 'Invalid Request';
}
?>
