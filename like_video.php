<?php
session_start(); 
$user_id = $_SESSION['user_id']; 
$video_id = $_POST['video_id'];

include('db_config.php');

// Check if the user has already liked the video
$check_like_sql = "SELECT * FROM likes WHERE user_id = $user_id AND video_id = $video_id";
$check_like_result = $conn->query($check_like_sql);

if ($check_like_result->num_rows == 0) {
    // Insert like
    $insert_like_sql = "INSERT INTO likes (user_id, video_id) VALUES ($user_id, $video_id)";
    if ($conn->query($insert_like_sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Already liked']);
}

$conn->close();
?>
 
