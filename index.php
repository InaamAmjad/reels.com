<?php include('includes/header.php'); ?>

<main>
    <h1>Welcome to Reels.com</h1>
    <div id="video-container">
        <?php
            include('db_config.php');
            $sql = "SELECT * FROM reels_db.videos";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $video_id = $row['id'];
                    // Fetch likes count
                    $like_sql = "SELECT COUNT(*) AS like_count FROM reels_db.likes WHERE video_id = $video_id";
                    $like_result = $conn->query($like_sql);
                    $like_data = $like_result->fetch_assoc();
                    $likes = $like_data['like_count'];

                    // Fetch comments for the video
                    $comment_sql = "SELECT comment_text FROM reels_db.comments WHERE video_id = $video_id";
                    $comment_result = $conn->query($comment_sql);
                    $comments = [];
                    while ($comment = $comment_result->fetch_assoc()) {
                        $comments[] = $comment['comment_text'];
                    }

                    echo '<div class="video">';
                    echo '<h3>' . $row['title'] . '</h3>';
                    echo '<video src="' . $row['video_url'] . '" controls></video>';
                    echo '<div class="actions">';
                    echo '<button class="like-btn" data-video-id="' . $video_id . '">Like (' . $likes . ')</button>';
                    echo '<div class="comments">';
                    foreach ($comments as $comment) {
                        echo '<p>' . htmlspecialchars($comment) . '</p>';
                    }
                    echo '<textarea class="comment-text" placeholder="Add a comment"></textarea>';
                    echo '<button class="comment-btn" data-video-id="' . $video_id . '">Comment</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "No videos found.";
            }
            $conn->close();
        ?>
    </div>
</main>

<?php include('includes/footer.php'); ?>
 
