<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch user info
$user_stmt = $conn->prepare("SELECT username, email, contact_no FROM users WHERE id=?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

// Add expense
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST["category"];
    $amount = $_POST["amount"];
    $comments = $_POST["comments"];

    $stmt = $conn->prepare("INSERT INTO expenses (user_id, category, amount, comments) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $user_id, $category, $amount, $comments);
    $stmt->execute();
}

$result = $conn->query("SELECT * FROM expenses WHERE user_id=$user_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <h2>Welcome, <?= htmlspecialchars($user["username"]) ?>!</h2>
    <div class="profile-box">
        <p><strong>Email:</strong> <?= htmlspecialchars($user["email"]) ?><br>
        <strong>Contact:</strong> <?= htmlspecialchars($user["contact_no"]) ?></p>
    </div>
    <a href="logout.php" class="btn">Logout</a>
    <hr><br>

    <h3>Add Expense</h3>
    <form method="POST">
        <input type="text" name="category" placeholder="Category" required><br>
        <input type="number" step="0.01" name="amount" placeholder="Amount" required><br>
        <input type="text" name="comments" placeholder="Comments"><br>
        <button type="submit">Add</button>
    </form>

    <h3>Your Expenses</h3>
    <table>
        <tr>
            <th>Category</th>
            <th>Amount</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th>Comments</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row["category"]) ?></td>
                <td><?= htmlspecialchars($row["amount"]) ?></td>
                <td><?= htmlspecialchars($row["created_at"]) ?></td>
                <td><?= htmlspecialchars($row["updated_at"]) ?></td>
                <td><?= htmlspecialchars($row["comments"]) ?></td>
                <td class="action-links">
                    <a href="edit_expense.php?id=<?= $row['id'] ?>">Edit</a> | 
                    <a href="delete_expense.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this expense?');">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <br>
    <a href="visualization.php" class="btn">View Expense Visualization</a>
</div>
</body>
</html>
