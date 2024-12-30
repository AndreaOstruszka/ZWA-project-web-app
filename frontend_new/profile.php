<?php include 'header.php'; ?>

<div id="content">
    <article id="main-widest">
        <h1>My profile</h1>
        <h2>Profile details</h2>

        <div class="profile-container">
            <div class="profile-info">
                <p><strong>Nickname:</strong> andy</p>
                <p><strong>First Name:</strong> A</p>
                <p><strong>Last Name:</strong> O</p>
                <p><strong>Email:</strong> andy@mail.cz</p>

                <div class="profile-links">
                    <a href="profile-edit.php">Edit profile</a>
                </div>
            </div>
        </div>

        <div>
            <h2>My reviews</h2>
            <div id="review_container">
                <div class="review-index">
                    <p class="review-book">How to Train Your Dragon</p>
                    <p class="review-rating">4/5</p>
                    <span class="review-time">29.11.2024 12:34</span>
                    <p class="review_text_index">I tried to train my cat instead. Did not work. Good book tho.</p>
                    <span class="review-edit-span"><a href="review_edit.php"><button class="button-edit">edit</button></a></span>
                </div>
                <div class="review-index">
                    <p class="review-book">The Hitchhiker's Guide to the Galaxy</p>
                    <p class="review-rating">5/5</p>
                    <span class="review-time">29.11.2024 12:34</span>
                    <p class="review_text_index">Great book, but now I'm worried about always having a towel with me. Thanks, Adams!</p>
                    <span class="review-edit-span"><a href="review_edit.php"><button class="button-edit">edit</button></a></span>
                </div>
                <div class="review-index">
                    <p class="review-book">Pride and Prejudice</p>
                    <p class="review-rating">1/5</p>
                    <span class="review-time">29.11.2024 12:34</span>
                    <p class="review_text_index">I thought this was a self-help book.</p>
                    <span class="review-edit-span"><a href="review_edit.php"><button class="button-edit">edit</button></a></span>
                </div>
        </div>

        <div>
            <h2>Books inserted by me</h2>
            <a href=book-detail.php title="Book Title"><div class="book-cover-image-wrapper"><img src="images/covers/cover-placeholder-small.jpg" alt="" height="225" width="150">Book Title</div></a>
            <a href=book-detail.php title="Book Title"><div class="book-cover-image-wrapper"><img src="images/covers/cover-placeholder-small.jpg" alt="" height="225" width="150">Book Title</div></a>
            <a href=book-detail.php title="Book Title"><div class="book-cover-image-wrapper"><img src="images/covers/cover-placeholder-small.jpg" alt="" height="225" width="150">Book Title</div></a>
            <div class="spacing"></div>
        </div>
    </article>
</div>

<?php include 'footer.php'; ?>
