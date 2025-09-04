<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch category-wise totals
$stmt = $conn->prepare("SELECT category, SUM(amount) as total FROM expenses WHERE user_id=? GROUP BY category");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$categories = [];
$totals = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row["category"];
    $totals[] = $row["total"];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Expense Visualization</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container">
    <h2>Category-wise Expense Distribution</h2>
    <canvas id="expenseChart"></canvas>
    <br>
    <a href="dashboard.php" class="btn">Back to Dashboard</a>
</div>

<script>
    const ctx = document.getElementById('expenseChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?= json_encode($categories) ?>,
            datasets: [{
                data: <?= json_encode($totals) ?>,
                backgroundColor: ['#3498db','#2ecc71','#e74c3c','#f1c40f','#9b59b6','#1abc9c']
            }]
        }
    });
</script>
</body>
</html>
