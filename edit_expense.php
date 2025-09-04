<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

if (!isset($_GET["id"])) {
    header("Location: dashboard.php");
    exit();
}

$id = intval($_GET["id"]);

// Fetch expense
$stmt = $conn->prepare("SELECT * FROM expenses WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: dashboard.php");
    exit();
}

$expense = $result->fetch_assoc();

// Update expense
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST["category"];
    $amount = $_POST["amount"];
    $comments = $_POST["comments"];

    $stmt = $conn->prepare("UPDATE expenses SET category=?, amount=?, comments=?, updated_at=NOW() WHERE id=? AND user_id=?");
    $stmt->bind_param("sdsii", $category, $amount, $comments, $id, $user_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Failed to update expense.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Expense</title>
</head>
<body>
    <h2>Edit Expense</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="category" value="<?= htmlspecialchars($expense['category']) ?>" required><br><br>
        <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($expense['amount']) ?>" required><br><br>
        <input type="text" name="comments" value="<?= htmlspecialchars($expense['comments']) ?>"><br><br>
        <button type="submit">Update</button>
    </form>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
