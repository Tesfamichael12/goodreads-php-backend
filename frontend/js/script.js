document.addEventListener("DOMContentLoaded", function () {
  // Show more button functionality
  const showMoreBtn = document.querySelector(".show-more");
  const authorBio = document.querySelector(".author-bio");

  if (showMoreBtn && authorBio) {
    showMoreBtn.addEventListener("click", function () {
      authorBio.classList.toggle("expanded");
      showMoreBtn.textContent = authorBio.classList.contains("expanded")
        ? "Show less"
        : "Show more";
    });
  }

  // Dropdown functionality
  const detailsDropdown = document.querySelector(".details-dropdown");

  if (detailsDropdown) {
    detailsDropdown.addEventListener("click", function () {
      this.classList.toggle("active");
      // Add logic here to show/hide additional details
    });
  }

  // Rating functionality
  const stars = document.querySelectorAll(".stars .star");

  stars.forEach((star, index) => {
    star.addEventListener("click", () => {
      stars.forEach((s, i) => {
        if (i <= index) {
          s.classList.remove("empty");
        } else {
          s.classList.add("empty");
        }
      });
      // Add logic here to submit the rating
    });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const video = document.querySelector(".book-video");
  const playPauseBtn = document.getElementById("playPauseBtn");
  const muteBtn = document.getElementById("muteBtn");
  const progressBar = document.querySelector(".book-video-progress-bar");
  const videoOverlay = document.querySelector(".book-video-overlay");
  const timeDisplay = document.querySelector(".book-video-time");

  // Set video duration (5:10)
  video.addEventListener("loadedmetadata", function () {
    video.duration = 310; // 5 minutes and 10 seconds
  });

  // Video player controls
  playPauseBtn.addEventListener("click", togglePlayPause);
  videoOverlay.addEventListener("click", togglePlayPause);

  function togglePlayPause() {
    if (video.paused) {
      video.play();
      updatePlayPauseButton(true);
    } else {
      video.pause();
      updatePlayPauseButton(false);
    }
  }

  function updatePlayPauseButton(isPlaying) {
    playPauseBtn.innerHTML = isPlaying
      ? `
            <svg viewBox="0 0 24 24" class="book-video-control-icon">
                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" fill="currentColor"/>
            </svg>
        `
      : `
            <svg viewBox="0 0 24 24" class="book-video-control-icon">
                <path d="M8 5v14l11-7z" fill="currentColor"/>
            </svg>
        `;
  }

  muteBtn.addEventListener("click", () => {
    video.muted = !video.muted;
    muteBtn.innerHTML = video.muted
      ? `
            <svg viewBox="0 0 24 24" class="book-video-control-icon">
                <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z" fill="currentColor"/>
            </svg>
        `
      : `
            <svg viewBox="0 0 24 24" class="book-video-control-icon">
                <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02z" fill="currentColor"/>
            </svg>
        `;
  });

  // Update progress bar as video plays
  video.addEventListener("timeupdate", updateProgressAndTime);

  function updateProgressAndTime() {
    const progress = (video.currentTime / video.duration) * 100;
    progressBar.style.width = `${progress}%`;
    timeDisplay.textContent = `${formatTime(video.currentTime)} / 5:10`;
  }

  // Allow seeking when clicking on the progress bar
  const progressContainer = document.querySelector(".book-video-progress");
  progressContainer.addEventListener("click", seek);

  function seek(e) {
    const rect = progressContainer.getBoundingClientRect();
    const pos = (e.pageX - rect.left) / progressContainer.offsetWidth;
    video.currentTime = pos * video.duration;
  }

  function formatTime(timeInSeconds) {
    const minutes = Math.floor(timeInSeconds / 60);
    const seconds = Math.floor(timeInSeconds % 60);
    return `${minutes}:${seconds.toString().padStart(2, "0")}`;
  }
});

document.addEventListener("DOMContentLoaded", function () {
  // Select all like buttons for reviews
  document.querySelectorAll(".book-review-action-btn[data-review-id]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      const reviewId = btn.getAttribute("data-review-id");
      const likeCountElem = btn.querySelector(".like-count");
      let count = parseInt(likeCountElem.textContent.replace(/,/g, "")) || 0;
      const liked = btn.classList.toggle("liked");
      const action = liked ? "like" : "unlike";
      fetch("http://localhost/goodreads-php-backend/backend/likes/review.php", {
        method: "POST",
        credentials: "include",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
          review_id: reviewId,
          action: action
        })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            // Update like count if backend returns it, else update locally
            if (typeof data.like_count !== "undefined") {
              likeCountElem.textContent = data.like_count;
            } else {
              likeCountElem.textContent = liked ? count + 1 : count - 1;
            }
            btn.querySelector(".like-label").textContent = liked ? "Liked" : "Like";
          } else {
            alert(data.message || "Error updating like.");
            btn.classList.toggle("liked"); // revert UI if error
          }
        });
    });
  });
});

// === Book Reviews, Comments, and Rating Logic ===
document.addEventListener("DOMContentLoaded", function () {
  // Set your bookId here (replace with dynamic value if needed)
  const bookId = 1;

  // --- 1. Render Average Rating ---
  function renderAverageRating() {
    fetch(`/backend/reviews/average.php?book_id=${bookId}`)
      .then(res => res.json())
      .then(data => {
        if (data.avg_rating !== undefined) {
          document.querySelectorAll('.rating-number').forEach(el => el.textContent = data.avg_rating);
        }
        if (data.count !== undefined) {
          document.querySelectorAll('.rating-count').forEach(el => el.textContent = `${data.count} ratings`);
        }
      });
  }

  // --- 2. Render Reviews ---
  function renderReviews() {
    fetch(`/backend/reviews/list.php?book_id=${bookId}`)
      .then(res => res.json())
      .then(data => {
        const container = document.querySelector('.book-review-container');
        if (!container) return;
        container.innerHTML = '';
        data.reviews.forEach(review => {
          const reviewDiv = document.createElement('div');
          reviewDiv.className = 'book-review-item';
          reviewDiv.innerHTML = `
            <div class="book-review-header">
              <div class="book-review-user">
                <img src="../assets/image/user-and-author-profile/${review.profile_pic}" class="book-review-avatar" />
                <div class="book-review-user-info">
                  <div class="book-review-username">${review.name}</div>
                </div>
              </div>
            </div>
            <div class="book-review-content">
              <div class="book-review-rating">
                <div class="book-review-stars">
                  ${[1,2,3,4,5].map(i => `<span class="book-review-star${i <= review.rating ? ' filled' : ''}">★</span>`).join('')}
                </div>
              </div>
              <p class="book-review-text">${review.comment}</p>
              <div class="book-review-actions">
                <span>${review.like_count} likes</span>
                <span>${review.comment_count} comments</span>
              </div>
              <div class="review-comments" data-review-id="${review.id}">
                <div class="comments-list"></div>
                <form class="comment-form">
                  <input type="text" class="comment-input" placeholder="Add a comment..." required>
                  <button type="submit">Post</button>
                </form>
              </div>
            </div>
          `;
          container.appendChild(reviewDiv);
          renderComments(review.id);
          setupCommentForm(review.id);
        });
      });
  }

  // --- 3. Render Comments for a Review ---
  function renderComments(reviewId) {
    fetch(`/backend/comments/list.php?review_id=${reviewId}`)
      .then(res => res.json())
      .then(data => {
        const commentsList = document.querySelector(`.review-comments[data-review-id="${reviewId}"] .comments-list`);
        if (!commentsList) return;
        commentsList.innerHTML = data.comments.map(comment => `
          <div class="comment-item">
            <img src="../assets/image/user-and-author-profile/${comment.profile_pic}" class="comment-avatar" />
            <span class="comment-user">${comment.name}</span>
            <span class="comment-content">${comment.content}</span>
          </div>
        `).join('');
      });
  }

  // --- 4. Submit a Comment ---
  function setupCommentForm(reviewId) {
    const form = document.querySelector(`.review-comments[data-review-id="${reviewId}"] .comment-form`);
    if (!form) return;
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const input = form.querySelector('.comment-input');
      fetch('/backend/comments/submit.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ review_id: reviewId, content: input.value })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          input.value = '';
          renderComments(reviewId);
        }
      });
    });
  }

  // --- 5. Submit Book Rating (Stars) ---
  document.querySelectorAll('.book-cover-section .stars .star').forEach((star, idx, starsArr) => {
    star.addEventListener('click', function () {
      const rating = idx + 1;
      fetch('/backend/reviews/submit.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ book_id: bookId, rating: rating, comment: '' })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          // Update UI: fill stars up to selected
          starsArr.forEach((s, i) => {
            if (i < rating) s.classList.remove('empty');
            else s.classList.add('empty');
          });
          renderAverageRating();
        }
      });
    });
  });

  // --- 6. Initial Render ---
  renderAverageRating();
  renderReviews();
});
