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

$stmt = $conn->prepare("DELETE FROM expenses WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);

if ($stmt->execute()) {
    header("Location: dashboard.php");
    exit();
} else {
    echo "Error deleting expense.";
}
?>
