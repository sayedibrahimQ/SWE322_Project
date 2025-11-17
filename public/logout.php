<?php

// 1. Start the session
// This is necessary to access and manipulate the session.
session_start();

// 2. Unset all session variables
// This clears all the data stored in the session, like user_id, user_type, etc.
$_SESSION = array();

// 3. Destroy the session
// This completely removes the session from the server.
session_destroy();

// 4. Redirect the user to the login page
// After logging out, the user is sent back to the login page.
// You could also redirect to the homepage: header("Location: ../index.php");
header("Location: login.php");
exit();
?>