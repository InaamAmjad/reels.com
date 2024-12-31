<?php include('includes/header.php'); ?>

<main>
    <h1>Upload a Video</h1>
    <form action="upload_video_action.php" method="post" enctype="multipart/form-data">
        <label for="title">Video Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="video">Select Video:</label>
        <input type="file" id="video" name="video" accept="video/*" required>

        <button type="submit">Upload</button>
    </form>
</main>

<?php include('includes/footer.php'); ?>
 
