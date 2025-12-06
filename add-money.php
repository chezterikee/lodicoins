<?php

session_start();
header('Content-Type: application/json');

$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
if($amount <= 0){
    echo json_encode(['success'=>false,'message'=>'Invalid amount']);
    exit;
}


if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false,'message'=>'Not logged in']);
    exit;
}

// DB connection
$link = mysqli_connect('localhost','root','','lodipayy');
if(!$link) {
    echo json_encode(['success'=>false,'message'=>'Database connection failed']);
    exit;
}

$user_id = $_SESSION['user_id'];
$amount = floatval($_POST['amount'] ?? 0);

if($amount <= 0){
    echo json_encode(['success'=>false,'message'=>'Invalid amount']);
    exit;
}

// Update user wallet
mysqli_query($link, "UPDATE users SET mainWallet = mainWallet + $amount WHERE id = $user_id");

// Get username
$result = mysqli_query($link, "SELECT username FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($result);
$username = $user['username'];

// Insert transaction
$date = date('Y-m-d H:i:s');
$description = 'Added money to wallet';
mysqli_query($link, "INSERT INTO transactions (user_id, username, transaction_type, amount, date, description, status) VALUES ($user_id, '$username', 'Add Money', $amount, '$date', '$description', 'Completed')");

echo json_encode(['success'=>true,'message'=>'Money added successfully']);
?>
