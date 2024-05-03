<?php

// Connect to the database
include ('database.php');

// Ambil method HTTP yang digunakan
$method = $_SERVER['REQUEST_METHOD'];

// Set header content type to JSON
header('Content-Type: application/json');

// Function to generate verification code
function generateVerificationCode() {
    return md5(uniqid(rand(), true)); // Example code to generate verification code
}

// Check request method
if ($method  === 'GET') {
    // Retrieve data from the database
    $query = "SELECT * FROM users";
    $result = mysqli_query($db, $query);
    
    // Check if query is successful
    if ($result) {
        $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode($users);
    } else {
        // Set HTTP status code 500 Internal Server Error
        http_response_code(500);
        echo json_encode(array("message" => "Internal Server Error"));
    }
} elseif ($method === 'POST') {
    // Access data from $_POST
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    
     // Generate verification code
     $verification_code = generateVerificationCode(); // Implement this function

    // Validate input data
    if (!empty($name) && !empty($email) && !empty($phone) && !empty($password)) { 
         // Encrypt password
         $password = password_hash($password, PASSWORD_DEFAULT);

        // Insert data into the database
        $query = "INSERT INTO users (name, email, phone, password, verification_code) VALUES ('$name', '$email', '$phone', '$password', '$verification_code')";
        $result = mysqli_query($db, $query);
        
         // Your INSERT query here
        if ($result) {
            echo json_encode(array("message" => "User created successfully"));
        } else {
            // Set HTTP status code 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Failed to insert user into database"));
        }
    } else {
        // Set HTTP status code 400 Bad Request
        http_response_code(400);
        echo json_encode(array("message" => "Missing required data"));
    
    }
} elseif ($method === 'PUT') {
    // Access data from $_GET
    $id = $_GET['id'];
    $name = $_GET['name'];
    $email = $_GET['email'];
    $phone = $_GET['phone'];
    $password = $_GET['password'];

    // Generate verification code
    $verification_code = generateVerificationCode(); // Implement this function
    
    // Validate input data
    if (!empty($name) && !empty($email) && !empty($phone) && !empty($password)) {   
         // Encrypt password
         $password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert data into the database
        $query = "UPDATE users SET name='$name', email='$email', phone='$phone', password='$password', verification_code='$verification_code' WHERE id='$id'";
        $result = mysqli_query($db, $query);
        
        // Your UPDATE query here
        if ($result) {
            echo json_encode(array("message" => "User update successfully"));
        } else {
            // Set HTTP status code 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Failed to insert user into database"));
        }
    } else {
        // Set HTTP status code 400 Bad Request
        http_response_code(400);
        echo json_encode(array("message" => "Missing required data"));
    
    }
} elseif ($method === 'DELETE') {
    // Access data from $_GET
    $id = $_GET['id']; // Mengambil id dari query string

    // Validate input data
    if (!empty($id)) { // Periksa apakah id tidak kosong
        // Delete data from the database
        $query = "DELETE FROM users WHERE id = '$id'";
        $result = mysqli_query($db, $query);
        
         // Your DELETE query here
         if ($result) {
            echo json_encode(array("message" => "User delete successfully"));
        } else {
            // Set HTTP status code 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Failed to insert user into database"));
        }
    } else {
        // Set HTTP status code 400 Bad Request
        http_response_code(400);
        echo json_encode(array("message" => "Missing required data"));
    
    }
} else {
    // Set HTTP status code 405 Method Not Allowed
    http_response_code(405);
    echo json_encode(array("message" => "Method Not Allowed"));
}

?>
