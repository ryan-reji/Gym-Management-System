<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to index.html
header("Location: ../GYM SHARK/home.html");
exit();
?>
