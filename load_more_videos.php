<?php
session_start();
include('db_config.php');

if (!isset($_SESSION['user_id'])) {
    echo '<p class="no-videos">Please log in to view videos.</p>';
    include('includes/footer.php');
    exit;
}

$user_id = $_SESSION['user_id'];


$offset = $_GET['offset'];
$limit = $_GET['limit'];

$sql = "SELECT videos.*, users.username FROM videos JOIN users ON videos.user_id = users.id LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $video_id = $row['id'];

        // Fetch likes count
        $like_sql = "SELECT COUNT(*) AS like_count FROM likes WHERE video_id = ?";
        $like_stmt = $conn->prepare($like_sql);
        $like_stmt->bind_param("i", $video_id);
        $like_stmt->execute();
        $like_result = $like_stmt->get_result();
        $like_data = $like_result->fetch_assoc();
        $likes = $like_data['like_count'];

        // Check if the user has already liked the video
        $user_like_sql = "SELECT * FROM likes WHERE user_id = ? AND video_id = ?";
        $user_like_stmt = $conn->prepare($user_like_sql);
        $user_like_stmt->bind_param("ii", $user_id, $video_id);
        $user_like_stmt->execute();
        $user_like_result = $user_like_stmt->get_result();
        $user_liked = $user_like_result->num_rows > 0;

        // Fetch comments for the video
        $comment_sql = "SELECT comments.comment_text, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.video_id = ?";
        $comment_stmt = $conn->prepare($comment_sql);
        $comment_stmt->bind_param("i", $video_id);
        $comment_stmt->execute();
        $comment_result = $comment_stmt->get_result();
        $comments = [];
        while ($comment = $comment_result->fetch_assoc()) {
            $comments[] = $comment;
        }

        echo '<div class="video-card">';
        echo '<div class="video-details">';
        echo '<h3 class="video-title">' . htmlspecialchars($row['title']) . ' by ' . htmlspecialchars($row['username']) . '</h3>';
        echo '<video class="video-player" src="' . htmlspecialchars($row['video_url']) . '" controls></video>';
        echo '<div class="action-buttons">';
        if ($user_liked) {
            echo '<button class="dislike-button" data-video-id="' . $video_id . '">Dislike (' . $likes . ')</button>';
        } else {
            echo '<button class="like-button" data-video-id="' . $video_id . '">Like (' . $likes . ')</button>';
        }
        echo '</div>';
        echo '</div>';
        echo '<div class="comments-container">';
        echo '<div class="comments-section">';
        foreach ($comments as $comment) {
            $comment_class = $comment['user_id'] == $_SESSION['user_id'] ? 'current-user comment-card' : 'other-user comment-card';
            $username_position = $comment['user_id'] == $_SESSION['user_id'] ? 'comment-username-right' : 'comment-username-left';
            echo '<div class="' . $comment_class . '">';
            echo '<p class="' . $username_position . '">' . htmlspecialchars($comment['username']) . '</p>';
            echo '<p class="comment-text">' . htmlspecialchars($comment['comment_text']) . '</p>';
            echo '</div>';
        }
        echo '</div>';
        echo '<textarea class="comment-input" placeholder="Add a comment..."></textarea>';
        echo '<button class="comment-button" data-video-id="' . $video_id . '">Post Comment</button>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '';
}

$conn->close();
?>
