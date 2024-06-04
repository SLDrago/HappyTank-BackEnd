document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('.stars i');
    const ratingMessage = document.querySelector('.rating-message');
    const messages = [
        "I hate it 😠",
        "It's poor 😕",
        "It's okay 😐",
        "I like it 🙂",
        "I love it 😍"
    ];

    stars.forEach((star, index) => {
        star.addEventListener('click', () => {
            stars.forEach((s, i) => {
                if (i <= index) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
            ratingMessage.textContent = messages[stars.length - 1 - index];
        });
    });
});
document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('.stars i');
    const ratingMessage = document.querySelector('.rating-message');
    const reviewTextarea = document.querySelector('.textarea textarea');
    const postButton = document.querySelector('.btn button');
    const thankYouMessage = document.querySelector('.thank-you-message');
    const editButton = document.querySelector('.edit-review button');
    const messages = [
        "I hate it 😠",
        "It's poor 😕",
        "It's okay 😐",
        "I like it 🙂",
        "I love it 😍"
    ];
    let currentRating = 0;
    let currentReview = "";

    stars.forEach((star, index) => {
        star.addEventListener('click', () => {
            currentRating = stars.length - index;
            stars.forEach((s, i) => {
                if (i <= index) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
            ratingMessage.textContent = messages[index];
        });
    });

    postButton.addEventListener('click', (e) => {
        e.preventDefault();
        currentReview = reviewTextarea.value;
        if (currentRating > 0 && currentReview) {
            alert(`Review posted: ${currentReview} with rating: ${currentRating}`);
            thankYouMessage.style.display = 'block';
            ratingMessage.textContent = "";
            reviewTextarea.value = "";
            stars.forEach(star => star.classList.remove('active'));
        } else {
            alert("Please provide a rating and a review.");
        }
    });

    editButton.addEventListener('click', () => {
        if (currentRating > 0) {
            stars.forEach((star, index) => {
                if (index < stars.length - currentRating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
            ratingMessage.textContent = messages[stars.length - currentRating];
            reviewTextarea.value = currentReview;
            thankYouMessage.style.display = 'none';
        } else {
            alert("No review to edit.");
        }
    });
});
