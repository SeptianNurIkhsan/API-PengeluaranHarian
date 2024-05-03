<?php
// Connect to the database
include('database.php');

// Ambil method HTTP yang digunakan
$method = $_SERVER['REQUEST_METHOD'];

// Memulai session
session_start();

// Set header content type to JSON
header('Content-Type: application/json');

// Check request method
if ($method === 'GET') {
    // Retrieve data from the database
    $query = mysqli_query($db, "SELECT e.CategoryId, e.CategoryName AS category, e.UserId 
    FROM tblcategory e
    LEFT JOIN users u ON e.UserId = u.id");
    
    // Check if query is successful
    if ($query) {
        $result = array();
        // Loop through the query results and insert into array
        while ($row = mysqli_fetch_assoc($query)) {
            $result[] = $row;
        }
        // Provide response with expense data
        echo json_encode($result);
    } else {
        // Set HTTP status code 500 Internal Server Error
        http_response_code(500);
        echo json_encode(array("message" => "Internal Server Error"));
    }
} elseif ($method === 'POST') {
    // Bypass authentication for testing purposes
    // Access data from $_POST
    $username = $_POST['user']; // Mengambil nilai nama pengguna dari $_POST
    $category = $_POST['category'];

    // Mengambil ID pengguna berdasarkan nama pengguna
    $query_user_id = mysqli_query($db, "SELECT id FROM users WHERE name = '$username'");
    $user = mysqli_fetch_assoc($query_user_id);
    $userid = $user['id'];

    // Validate input data
    if (!empty($category)) { 
        // Insert data into the database
        $query = mysqli_query($db, "INSERT INTO tblcategory(UserId, CategoryName) VALUES ('$userid', '$category')");
        
        // Check if the query was successful
        if ($query) {
            // Return success message
            echo json_encode(array("message" => "Category added successfully"));
        } else {
            // Return error message
            http_response_code(500);
            echo json_encode(array("message" => "Failed to add category"));
        }
    } else {
        // Return error message if required fields are missing
        http_response_code(400);
       echo json_encode(array("message" => "Missing required fields"));
    }
} elseif ($method === 'PUT') {
    // Access data from $_GET
    $id = $_GET['categoryid']; 
    $username = $_GET['user'];
    $category = $_GET['category'];

    // Mengambil ID pengguna berdasarkan nama pengguna
    $query_user_id = mysqli_query($db, "SELECT id FROM users WHERE name = '$username'");
    $user = mysqli_fetch_assoc($query_user_id);
    $userid = $user['id'];
    
    // Validate input data
    if (!empty($category)) { 
        // Update data in the database
        $query = "UPDATE tblcategory SET UserId='$userid', CategoryName='$category' WHERE CategoryId='$id'";
        $result = mysqli_query($db, $query);
        
        // Your UPDATE query here
        if ($result) {
            echo json_encode(array("message" => "Category updated successfully"));
        } else {
            // Set HTTP status code 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Failed to update category"));
        }
    } else {
        // Set HTTP status code 400 Bad Request
        http_response_code(400);
        echo json_encode(array("message" => "Missing required data"));
    }
} elseif ($method === 'DELETE') {
    // Access data from $_GET
    $id = $_GET['categoryid']; // Mengambil id dari query string

    // Validate input data
    if (!empty($id)) { // Periksa apakah id tidak kosong
        // Delete data from the database
        $query = "DELETE FROM tblcategory WHERE CategoryId = '$id'";
        $result = mysqli_query($db, $query);
        
         // Your DELETE query here
         if ($result) {
            echo json_encode(array("message" => "Category delete successfully"));
        } else {
            // Set HTTP status code 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Failed to insert category into database"));
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