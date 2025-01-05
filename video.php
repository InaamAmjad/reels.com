<?php include('includes/header.php');
ini_set('session.cookie_lifetime', 86400); // Set session cookie lifetime to 1 day (86400 seconds)
ini_set('session.cookie_secure', 1);      // Ensure cookie is sent over secure (https) connections
ini_set('session.cookie_httponly', 1);    // Prevent JavaScript access to session cookies
session_start();

session_start();

    if (!isset($_SESSION['role']) || $_SESSION['role'] === 'consumer') {
    echo "<script>alert('You do not have permission to upload videos:" . $_SESSION['role'] . ".'); window.location.href='index.php';</script>";
    exit();
}

?>
<main>
    <h1>Upload a Video</h1>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['title']) && isset($_FILES['video'])) {
            include('db_config.php');

            $title = $_POST['title'];
            $video = $_FILES['video'];
            $user_id = $_SESSION['user_id']; // Get the user ID from the session

            // Check if the video file is valid
            if ($video['error'] === UPLOAD_ERR_OK) {
                $video_tmp_path = $video['tmp_name'];
                $video_name = basename($video['name']);
                $upload_dir = 'uploads/videos/';
                $video_path = $upload_dir . $video_name;

                // Create the uploads/videos directory if it doesn't exist
                if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
                echo "<script>alert('Failed to create upload directory.');</script>";
                exit();
            }

                // Move the uploaded file to the uploads directory
                if (move_uploaded_file($video_tmp_path, $video_path)) {
                    // Insert video details into the database
                    $query = "INSERT INTO db_reels.videos (title, video_url, user_id) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ssi", $title, $video_path, $user_id);

                    if ($stmt->execute()) {
                        echo "<script>alert('Video uploaded successfully!'); window.location.href='index.php';</script>";
                    } else {
                        echo "<script>alert('Error: " . $stmt->error . "');</script>";
                    }

                    $stmt->close();
                } else {
                    echo "<script>alert('Failed to move uploaded file.');</script>";
                }
            } else {
                echo "<script>alert('Error uploading file.');</script>";
            }

            $conn->close();
        } else {
            echo "<script>alert('Form data is missing.');</script>";
        }
    }
    ?>
    <form action="video.php" method="post" enctype="multipart/form-data">
        <label for="title">Video Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="video">Select Video:</label>
        <input type="file" id="video" name="video" accept="video/*" required>

        <button type="submit">Upload</button>
    </form>
</main>

<?php include('includes/footer.php'); ?>


 
