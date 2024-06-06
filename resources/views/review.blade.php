<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Star Rating in HTML CSS & JavaScript</title>
    <link rel="stylesheet" href="Review.css" />
    <!-- Fontawesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <link rel="stylesheet" href="../css/">
    <script src="Review.js" defer></script>

</head>

<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <a href="#">HappyTank</a>
            </div>
            <ul class="nav-links">
                <li><a href="#">Home</a> </li>
                <li><a href="#">About</a> </li>
                <li><a href="#">Service</a> </li>
                <li><a href="#">Contact</a> </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <div class="rating-box">
            <header>How was your experience?</header>
            <div class="stars">
                <i class="fa-solid fa-star" id="rate-5"></i>
                <i class="fa-solid fa-star" id="rate-4"></i>
                <i class="fa-solid fa-star" id="rate-3"></i>
                <i class="fa-solid fa-star" id="rate-2"></i>
                <i class="fa-solid fa-star" id="rate-1"></i>
            </div>
            <form action="#">
                <header class="rating-message"></header>
                <div class="textarea">
                    <textarea cols="30" placeholder="Write your review here..."></textarea>
                </div>
                <div class="btn">
                    <button type="submit">Post</button>
                </div>
            </form>
            <div class="thank-you-message">
                <p>Thank you for Rating</p>
                <div class="edit-review">
                    <button>Edit Review</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/review.js" defer></script>
</body>

</html>
