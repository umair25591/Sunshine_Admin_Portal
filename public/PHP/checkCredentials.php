<?php
require_once '../../db-connection.php';

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        // Use prepared statements to prevent SQL injection
        $checkCredentials = "SELECT id, firstName, lastName, email, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($checkCredentials);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                session_start();
                $_SESSION['adminfirstName'] = $user['firstName'];
                $_SESSION['adminlastName'] = $user['lastName'];
                $_SESSION['adminemail'] = $user['email'];
                $_SESSION['adminId'] = $user['id'];

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login successful',
                ]);
            }
            else {

                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid credentials'
                ]);
            }
        } 
        else {

            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ]);
        }

        $stmt->close();
    } 
    else {

        echo json_encode([
            'status' => 'error',
            'message' => 'Please fill in both email and password'
        ]);
    }
} 
else {

    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request'
    ]);
}

$conn->close();
?>
