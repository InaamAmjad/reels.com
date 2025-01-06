<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); // Ensure session is started
include('includes/header.php'); ?>

<main class="main-container">
    <h1 class="page-title">Welcome to Reels.com</h1>
    <div id="video-container" class="video-grid">
        <?php
            include('db_config.php');
            if (!isset($_SESSION['user_id'])) {
                echo '<p class="no-videos">Please log in to view videos.</p>';
                include('includes/footer.php');
                exit;
            }
            // echo "<script>alert('STEP 1');</script>";
            // echo "<script>alert('STEP 1.1: " . $_SESSION['user_id'] . "');</script>";
            
            $user_id = $_SESSION['user_id'];
            $limit = 5; // Number of videos to load per page
            $offset = 0; // Initial offset
            // echo "<script>alert('STEP 1.2: " . $_SESSION . "');</script>";

        
            $sql = "SELECT videos.*, users.username FROM reels_db.videos JOIN users ON reels_db.videos.user_id = users.id LIMIT ? OFFSET ?";
            // echo "<script>alert('STEP 1.2: " . $sql . "');</script>";

            $stmt = $conn->prepare($sql);
            // echo "<script>alert('STEP 1.2: " . $stmt . "');</script>";

            $stmt->bind_param("ii", $limit, $offset);

            $stmt->execute();

            $result = $stmt->get_result();
            // echo "<script>alert('STEP 1.2: " . $result . "');</script>";

            // echo "<script>alert('STEP 2');</script>";
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $video_id = $row['id'];
            // echo "<script>alert('STEP 3');</script>";

                    // Fetch likes count
                    $like_sql = "SELECT COUNT(*) AS like_count FROM reels_db.likes WHERE video_id = ?";
                    $like_stmt = $conn->prepare($like_sql);
                    $like_stmt->bind_param("i", $video_id);
                    $like_stmt->execute();
                    $like_result = $like_stmt->get_result();
                    $like_data = $like_result->fetch_assoc();
                    $likes = $like_data['like_count'];
            // echo "<script>alert('STEP 4');</script>";

                    // Check if the user has already liked the video
                    $user_like_sql = "SELECT * FROM likes WHERE user_id = ? AND video_id = ?";
                    $user_like_stmt = $conn->prepare($user_like_sql);
                    $user_like_stmt->bind_param("ii", $user_id, $video_id);
                    $user_like_stmt->execute();
                    $user_like_result = $user_like_stmt->get_result();
                    $user_liked = $user_like_result->num_rows > 0;

                    // Fetch comments for the video
                    $comment_sql = "SELECT comments.comment_text, comments.user_id, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.video_id = ?";
                    $comment_stmt = $conn->prepare($comment_sql);
                    $comment_stmt->bind_param("i", $video_id);
                    $comment_stmt->execute();
                    $comment_result = $comment_stmt->get_result();
                    $comments = [];
                    while ($comment = $comment_result->fetch_assoc()) {
                        $comments[] = $comment;
                    }
            // echo "<script>alert('STEP 5');</script>";

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
                        $comment_class = $comment['user_id'] == $user_id ? 'current-user comment-card' : 'other-user comment-card';
                        $username_position = $comment['user_id'] == $user_id ? 'comment-username-right' : 'comment-username-left';
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
                echo '<p class="no-videos">No videos found. Please check back later!</p>';
            }
            $conn->close();
        ?>
    </div>
    <div id="loading-spinner" class="loading-spinner" style="display: none;">
        <img src="assets/images/loading-spinner.gif" alt="Loading...">
    </div>
</main>

<?php include('includes/footer.php'); ?>

<!-- Style Enhancements -->
<style>
    /* Main Layout Styling */
    .main-container {
        font-family: 'Arial', sans-serif;
        margin: 0 auto;
        padding: 20px;
        max-width: 1000px;
        background-color: #f5f5f5;
    }
    .page-title {
        text-align: center;
        color: #333;
        font-size: 36px;
        margin-bottom: 40px;
    }

    /* Video Grid Layout */
    #video-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Video Card Styling */
    .video-card {
        display: flex;
        flex-direction: row; /* Makes the comment section on the left and video on the right */
        width: 100%;
        max-width: 1000px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        border: 1px solid #ddd;
        transition: transform 0.3s ease;
        margin-bottom: 30px;
    }
    .video-card:hover {
        transform: translateY(-5px);
    }

    /* Comments Container */
    .comments-container {
        width: 40%; /* Ensures the comments section takes up 40% of the width */
        background-color: #fafafa;
        padding: 20px;
        border-right: 1px solid #ddd;
        display: flex;
        flex-direction: column;
    }
    .comments-section {
        margin-bottom: 20px;
        overflow-y: auto;
    }
    .comment-card {
        padding: 8px;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .comments-section .comment-card.current-user {
        background-color: #d0e7ff;
        text-align: right;
        align-self: flex-end; /* Align the entire comment card to the right */
        margin-left: auto; /* Ensure the card is pushed to the right */
    }
    .comments-section .comment-card.other-user {
        background-color: #d0ffd0;
        text-align: left;
        align-self: flex-start; /* Align the entire comment card to the left */
        margin-right: auto; /* Ensure the card is pushed to the left */
    }
    .comment-username-left {
        font-weight: bold;
        margin-bottom: 5px;
        text-align: left;
        color: #000; /* Ensure the color is set */
    }
    .comment-username-right {
        font-weight: bold;
        margin-bottom: 5px;
        text-align: right;
        color: #000; /* Ensure the color is set */
    }
    .comments-section .comment-card.current-user .comment-username-right {
        font-weight: bold;
        margin-bottom: 5px;
        text-align: right;
        color: #000; /* Ensure the color is set */
    }
    .comments-section .comment-card.other-user .comment-username-left {
        font-weight: bold;
        margin-bottom: 5px;
        text-align: left;
        color: #000; /* Ensure the color is set */
    }
    .comments-section .comment-card .comment-username-right {
        font-weight: bold;
        margin-bottom: 5px;
        text-align: right;
        color: #000; /* Ensure the color is set */
    }
    .comment-text {
        color: #555;
        font-size: 14px;
        line-height: 1.4;
    }
    .comments-section .comment-card.current-user .comment-text {
        text-align: right; /* Align current user comment text to the right */
    }
    .comment-input {
        width: 100%;
        padding: 8px;
        border-radius: 4px;
        border: 1px solid #ddd;
        margin-bottom: 10px;
        resize: vertical;
        font-size: 14px;
        overflow: hidden;
    }
    .comment-input:focus {
        height: auto;
    }
    .comment-button {
        background-color: #007BFF;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }
    .comment-button:hover {
        background-color: #0056b3;
    }

    /* Video Details */
    .video-details {
        width: 60%; /* Ensures the video section takes up 60% of the width */
        padding: 20px;
        text-align: center;
        background-color: #fff;
    }
    .video-title {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin: 10px 0;
    }
    .video-player {
        width: 100%;
        height: auto;
        border-radius: 8px;
        max-height: 400px;
    }

    /* Action Buttons */
    .action-buttons {
        margin-top: 10px;
    }
    .like-button, .dislike-button {
        background-color: #28a745;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .like-button:hover, .dislike-button:hover {
        background-color: #218838;
    }

    /* No Videos Message */
    .no-videos {
        font-size: 18px;
        color: #888;
        text-align: center;
        margin-top: 40px;
    }

    /* Loading Spinner */
    .loading-spinner {
        text-align: center;
        margin-top: 20px;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .video-card {
            flex-direction: column;
            max-width: 100%;
            margin: 10px 0;
        }
        .comments-container {
            width: 100%;
            padding: 15px;
        }
        .video-details {
            width: 100%;
            padding: 15px;
        }
        .video-player {
            max-height: 300px;
        }
    }

    @media (max-width: 480px) {
        .page-title {
            font-size: 28px;
        }
        .comment-input {
            font-size: 12px;
        }
        .comment-button {
            padding: 6px 12px;
            font-size: 12px;
        }
        .like-button, .dislike-button {
            font-size: 12px;
            padding: 6px 12px;
        }
    }
</style>

<!-- JavaScript to handle comment posting, like/dislike functionality, and pagination -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function attachEventListeners() {
            const commentButtons = document.querySelectorAll('.comment-button');
            commentButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const videoId = this.getAttribute('data-video-id');
                    const commentInput = this.previousElementSibling;
                    const commentText = commentInput.value;

                    if (commentText.trim() !== '') {
                        fetch('comment_video.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `video_id=${videoId}&comment_text=${encodeURIComponent(commentText)}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Comment posted successfully!');
                                location.reload();
                            } else {
                                alert('Failed to post comment.');
                            }
                        });
                    } else {
                        alert('Please enter a comment.');
                    }
                });
            });

            const likeButtons = document.querySelectorAll('.like-button, .dislike-button');
            likeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const videoId = this.getAttribute('data-video-id');
                    const action = this.classList.contains('like-button') ? 'like' : 'dislike';

                    fetch('like_video.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `video_id=${videoId}&action=${action}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Failed to update like status.');
                        }
                    });
                });
            });
        }

        attachEventListeners();

        let offset = 5; // Initial offset for pagination
        const limit = 5; // Number of videos to load per page
        let loading = false; // Flag to prevent multiple requests

        window.addEventListener('scroll', () => {
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 100 && !loading) {
                loadMoreVideos();
            }
        });

        function loadMoreVideos() {
            loading = true;
            document.getElementById('loading-spinner').style.display = 'block';
            fetch(`load_more_videos.php?offset=${offset}&limit=${limit}`)
                .then(response => response.text())
                .then(data => {
                    if (data.trim() !== '') {
                        document.getElementById('video-container').insertAdjacentHTML('beforeend', data);
                        offset += limit;
                        loading = false;
                        attachEventListeners(); // Reattach event listeners to newly loaded buttons
                    } else {
                        loading = true; // No more videos to load
                    }
                    document.getElementById('loading-spinner').style.display = 'none';
                });
        }
    });
</script>
