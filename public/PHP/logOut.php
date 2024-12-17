<?php
session_start(); // Start the session

// Destroy all session data to log out the user
session_unset();  // Remove all session variables
session_destroy(); // Destroy the session

// Return a success message in JSON format
echo json_encode(['status' => 'success', 'message' => 'User logged out successfully']);
?>
