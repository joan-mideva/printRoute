<?php
// backend/logout.php
session_start();
session_unset();
session_destroy();
// Redirect to the main landing page, not the login page
header("Location: ../frontend/index.html");
exit();
?>