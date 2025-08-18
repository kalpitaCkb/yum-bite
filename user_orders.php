<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Start session if needed
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

// Ensure DB connection is available (profile.php usually requires it already)
// if (!isset($conn)) {
//     require_once "database.php";
// }

// Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

// $user_id = (int) $_SESSION["user_id"];

// Fetch orders for the current user with food details
// $sql = "SELECT o.id, f.name AS food_name, o.quantity, o.amount, o.status, o.created_at
//         FROM orders o
//         JOIN food f ON o.food_id = f.id
//         WHERE o.user_id = ?
//         ORDER BY o.created_at DESC";

// $stmt = mysqli_prepare($conn, $sql);
// if (!$stmt) {
//     echo "<div class='error'>Query preparation failed: " . mysqli_error($conn) . "</div>";
//     exit();
// }

// mysqli_stmt_bind_param($stmt, "i", $user_id); // integer bind
// mysqli_stmt_execute($stmt);
// $result = mysqli_stmt_get_result($stmt);

// if (!$result || mysqli_num_rows($result) == 0) {
//     echo "<div class='error'>No orders found for this user.</div>";
//     exit();
// }


// mysqli_stmt_close($stmt);
// ?>

<!-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>All Orders</h1>

    <?php if (isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Food</th>
            <th>Quantity</th>
            <th>Amount (BDT)</th>
            <th>Status</th>
            <th>Created At</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['food_name']); ?></td>
                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                <td><?php echo htmlspecialchars($row['amount']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html> -->

<!-- new code -->

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "database.php";

if (!isset($_SESSION['user_id'])) {
    echo "<p class='error'>You must be logged in to see your orders.</p>";
    return;
}

$user_id = (int)$_SESSION['user_id'];

$sql = "SELECT o.id, f.name AS food_name, o.quantity, o.amount, o.status, o.created_at
        FROM orders o
        JOIN food f ON o.food_id = f.id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC";

$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    echo "<p class='error'>Error fetching your orders.</p>";
    return;
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo "<p>No orders found.</p>";
} else {
    echo "<h2>Your Orders</h2>";
    echo "<ul class='orders-list'>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<li>";
        echo "<strong>Order #{$row['id']}</strong> - ";
        echo htmlspecialchars($row['food_name']) . " x " . $row['quantity'];
        echo " | Amount: $" . $row['amount'];
        echo " | Status: " . ucfirst($row['status']);
        echo " | Date: " . $row['created_at'];
        echo "</li>";
    }
    echo "</ul>";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
