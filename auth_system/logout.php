<?php
session_start();

// Unset all active in-memory session variables
$_SESSION = array();

// Request browser client to delete the session tracking cookie
if (ini_get("session.use_cookies")) {
$params = session_get_cookie_params();
setcookie(session_name(), '', time() - 42000,
$params["path"], $params["domain"],
$params["secure"], $params["httponly"]
);
}

// Destroy session file from server storage
session_destroy();

// Redirect user to login interface securely
header("Location: ../home.php");
exit();
?>
