<?php
session_start(); 
$user_id = $_SESSION['user_id']; 
$video_id = $_POST['video_id']; 
$comment_text = $_POST['comment_text'];

include('db_config.php');

// Insert comment into the database
$insert_comment_sql = "INSERT INTO comments (video_id, user_id, comment_text) VALUES ($video_id, $user_id, '$comment_text')";
if ($conn->query($insert_comment_sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

$conn->close();
?>
 
