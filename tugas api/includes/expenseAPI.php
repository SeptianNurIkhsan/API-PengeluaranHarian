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
    $query = mysqli_query($db, "SELECT e.ID, e.UserId, e.ExpenseDate, e.CategoryId, c.CategoryName AS category, e.ExpenseCost, e.Description, e.NoteDate
    FROM tblexpense e
    LEFT JOIN tblcategory c ON e.CategoryId = c.CategoryId
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
    $dateexpense = $_POST['dateexpense'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $costitem = $_POST['cost'];

    // Mengambil ID pengguna berdasarkan nama pengguna
    $query_user_id = mysqli_query($db, "SELECT id FROM users WHERE name = '$username'");
    $user = mysqli_fetch_assoc($query_user_id);
    $userid = $user['id'];

    // Validate input data
    if (!empty($dateexpense) && !empty($category) && !empty($description) && !empty($costitem)) { 
        // Check if the provided category belongs to the user
        $query_check_category = mysqli_query($db, "SELECT CategoryId FROM tblcategory WHERE CategoryName = '$category' AND UserId = '$userid'");
        if (mysqli_num_rows($query_check_category) > 0) {
            // Insert data into the database
            $query = mysqli_query($db, "INSERT INTO tblexpense(UserId, ExpenseDate, CategoryId, category, ExpenseCost, Description) 
            VALUES ('$userid', '$dateexpense', 
            (SELECT CategoryId FROM tblcategory WHERE CategoryName = '$category' AND UserId = '$userid' LIMIT 1), 
            '$category', '$costitem', '$description')");
            
            // Check if the query was successful
            if ($query) {
                // Return success message
                echo json_encode(array("message" => "Expense added successfully"));
            } else {
                // Return error message
                http_response_code(500);
                echo json_encode(array("message" => "Failed to add expense"));
            }
        } else {
            // Return error message if the provided category does not belong to the user
            http_response_code(400);
            echo json_encode(array("message" => "Category does not belong to the user"));
        }
    } else {
    // Return error message if required fields are missing
    http_response_code(400);
    echo json_encode(array("message" => "Missing required fields"));
    }
} elseif ($method === 'PUT') {
    // Access data from $_GET
    $id = $_GET['ID'];
    $username = $_GET['user'];
    $dateexpense = $_GET['dateexpense'];
    $category = $_GET['category'];
    $description = $_GET['description'];
    $costitem = $_GET['cost'];

    // Mengambil ID pengguna berdasarkan nama pengguna
    $query_user_id = mysqli_query($db, "SELECT id FROM users WHERE name = '$username'");
    $user = mysqli_fetch_assoc($query_user_id);
    $userid = $user['id'];
    
    // Validate input data
    if (!empty($dateexpense) && !empty($category) && !empty($description) && !empty($costitem)) { 
        // Check if the provided category belongs to the user
        $query_check_category = mysqli_query($db, "SELECT CategoryId FROM tblcategory WHERE CategoryName = '$category' AND UserId = '$userid'");
        if (mysqli_num_rows($query_check_category) > 0) {
            // Update data in the database
            $query = "UPDATE tblexpense SET UserId='$userid', ExpenseDate='$dateexpense', 
                      CategoryId=(SELECT CategoryId FROM tblcategory WHERE CategoryName = '$category' AND UserId = '$userid' LIMIT 1), 
                      category='$category', ExpenseCost='$costitem', Description='$description' WHERE ID='$id'";
            $result = mysqli_query($db, $query);
            
            if ($result) {
                echo json_encode(array("message" => "Expense updated successfully"));
            } else {
                // Set HTTP status code 500 Internal Server Error
                http_response_code(500);
                echo json_encode(array("message" => "Failed to update expense"));
            }
        } else {
            // Return error message if the provided category does not belong to the user
            http_response_code(400);
            echo json_encode(array("message" => "Category does not belong to the user"));
        }
    } else {
        // Set HTTP status code 400 Bad Request
        http_response_code(400);
        echo json_encode(array("message" => "Missing required data"));
    }
} elseif ($method === 'DELETE') {
    // Access data from $_GET
    $id = $_GET['ID']; // Mengambil id dari query string

    // Validate input data
    if (!empty($id)) { // Periksa apakah id tidak kosong
        // Delete data from the database
        $query = "DELETE FROM tblexpense WHERE ID = '$id'";
        $result = mysqli_query($db, $query);
        
         // Your DELETE query here
         if ($result) {
            echo json_encode(array("message" => "Expense delete successfully"));
        } else {
            // Set HTTP status code 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Failed to insert expense into database"));
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