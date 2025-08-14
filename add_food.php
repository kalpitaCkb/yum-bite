<?php
$foodname = $_POST["foodname"];
$price = $_POST["price"];
$description = $_POST["description"];
$category = $_POST["category"];
$restaurant = $_POST["restaurant"];
$cuisine = $_POST["cuisine"];

$imageNames = "";
$totalFiles = count($_FILES["images"]["name"]);

if (!file_exists("uploads")) {
    mkdir("uploads", 0777, true);
}

for ($i = 0; $i < $totalFiles; $i++) {
    $filename = $_FILES["images"]["name"][$i];
    $tempname = $_FILES["images"]["tmp_name"][$i];
    move_uploaded_file($tempname, "uploads/" . $filename);
    $imageNames .= $filename;
    if ($i != $totalFiles - 1) {
        $imageNames .= ",";
    }
}

require_once "database.php";

$query = "INSERT INTO food (name, price, description, category_title, restaurant, cuisine, image)
          VALUES ('$foodname', '$price', '$description', '$category', '$restaurant', '$cuisine', '$imageNames')";

if (mysqli_query($conn, $query)) {
    echo "Food added successfully!";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>