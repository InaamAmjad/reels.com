// Handle like button click
document.querySelectorAll('.like-btn').forEach(button => {
    button.addEventListener('click', function() {
        const videoId = this.dataset.videoId;
        
        // Send like request to server using AJAX
        fetch('like_video.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `video_id=${videoId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the like count on success
                const likeButton = this;
                let currentLikes = parseInt(likeButton.textContent.match(/\d+/)[0]);
                likeButton.textContent = `Like (${currentLikes + 1})`;
            } else {
                alert('You have already liked this video.');
            }
        });
    });
});

// Handle comment button click
document.querySelectorAll('.comment-btn').forEach(button => {
    button.addEventListener('click', function() {
        const videoId = this.dataset.videoId;
        const commentText = this.previousElementSibling.value;

        if (commentText.trim() !== '') {
            // Send comment request to server using AJAX
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
                    // Add the comment to the page dynamically
                    const commentContainer = this.previousElementSibling.parentNode;
                    const newComment = document.createElement('p');
                    newComment.textContent = commentText;
                    commentContainer.insertBefore(newComment, this.previousElementSibling);
                    this.previousElementSibling.value = ''; // Clear textarea
                }
            });
        } else {
            alert('Please enter a comment.');
        }
    });
});
 
