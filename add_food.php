<?php

require_once "database.php";

$foodname = $_POST["foodname"];
$price = $_POST["price"];
$description = $_POST["description"];
$category = $_POST["category"];
$restaurant = $_POST["restaurant"];
$cuisine = $_POST["cuisine"];

$imageName = "";

if (!file_exists("uploads")) {
    mkdir("uploads", 0777, true);
}

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $imageName = $_FILES['image']['name'];
    $tempname = $_FILES['image']['tmp_name'];
    move_uploaded_file($tempname, "uploads/" . $imageName);
}

// Get IDs from the respective tables based on the submitted names
// $category_id = 0;
// $restaurant_id = 0;
// $cuisine_id = 0;

if ($category) {
    $category_query = "SELECT id FROM categories WHERE id = ?";
    if ($category_stmt = mysqli_prepare($conn, $category_query)) {
        mysqli_stmt_bind_param($category_stmt, "i", $category);
        mysqli_stmt_execute($category_stmt);
        $result = mysqli_stmt_get_result($category_stmt);
        $row = mysqli_fetch_assoc($result);
        $category_id = $row['id'];
        error_log("Submitted category: $category, Retrieved category_id: $category_id");
        mysqli_stmt_close($category_stmt);
    }
}

if ($restaurant) {
    $restaurant_query = "SELECT id FROM restaurants WHERE id = ?";
    if ($restaurant_stmt = mysqli_prepare($conn, $restaurant_query)) {
        mysqli_stmt_bind_param($restaurant_stmt, "i", $restaurant);
        mysqli_stmt_execute($restaurant_stmt);
        $result = mysqli_stmt_get_result($restaurant_stmt);
        $row = mysqli_fetch_assoc($result);
        $restaurant_id = $row['id'];
        error_log("Submitted restaurant: $restaurant, Retrieved restaurant_id: $restaurant_id");
        mysqli_stmt_close($restaurant_stmt);
    }
}

if ($cuisine) {
    $cuisine_query = "SELECT id FROM cuisines WHERE id = ?";
    if ($cuisine_stmt = mysqli_prepare($conn, $cuisine_query)) {
        mysqli_stmt_bind_param($cuisine_stmt, "i", $cuisine);
        mysqli_stmt_execute($cuisine_stmt);
        $result = mysqli_stmt_get_result($cuisine_stmt);
        $row = mysqli_fetch_assoc($result);
        $cuisine_id = $row['id'];
        error_log("Submitted cuisine: $cuisine, Retrieved cuisine_id: $cuisine_id");
        mysqli_stmt_close($cuisine_stmt);
    }
}

// Validate IDs
if (!$category_id || !$restaurant_id || !$cuisine_id) {
    echo "Invalid category, restaurant, or cuisine ID.";
    exit();
}

// Validate required fields
// if (empty($foodname) || $price <= 0 || empty($description) || empty($imageName)) {
//     echo "All fields are required and must be valid.";
//     exit();
// }

// Validate IDs with specific error messages
$invalidIds = [];
if (!$category_id)
    $invalidIds[] = "category";
if (!$restaurant_id)
    $invalidIds[] = "restaurant";
if (!$cuisine_id)
    $invalidIds[] = "cuisine";
if (!empty($invalidIds)) {
    header("Location: admin_panel.php?error=" . urlencode("Invalid " . implode(", ", $invalidIds) . " ID(s)."));
    exit();
}

// Validate required fields
if (empty($imageName)) {
    echo "All fields are required and must be valid.";
    exit();
}

// Insert using prepared statement
$query = "INSERT INTO food (name, price, description, image, category_id, restaurant_id, cuisine_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
if ($stmt = mysqli_prepare($conn, $query)) {
    mysqli_stmt_bind_param($stmt, "sdssiii", $foodname, $price, $description, $imageName, $category_id, $restaurant_id, $cuisine_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: admin_menu.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
} else {
    header("Location: admin_panel.php?error=" . urlencode("Error preparing statement: " . mysqli_error($conn)));
}

// if (mysqli_query($conn, $query)) {
//     echo "Food added successfully!";
// } else {
//     echo "Error: " . mysqli_error($conn);
// }

mysqli_close($conn);
?>