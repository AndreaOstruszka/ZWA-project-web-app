<?php include 'header.php'; ?>

<div id="content">
    <article id="main-widest">
        <h1>Book detail</h1>
        <h2>Hobbit</h2>
        <div class="book-container">
            <div class="book-cover-div">
                <img src="images/covers/cover-hobbit.jpg" alt="Hobbit" class="book-cover">
            </div>
            <div class="book-info">
                <div class="rating">
                    <p>Rating: 4.8/5</p>
                </div>
                <p><strong>Author:</strong> J. R. R. Tolkien</p>
                <p><strong>ISBN:</strong> 496 660 164</p>
                <p><strong>Literary genre:</strong> prose</p>
                <p><strong>Fictional genre:</strong> fantasy</p>
                <p>Hobbit follows Bilbo Baggins, a hobbit who is thrust into an adventure by the wizard Gandalf and a group of dwarves. Their quest is to reclaim a homeland from the dragon Smaug. Along the way, Bilbo faces various creatures and finds a magical ring. The journey transforms Bilbo into a brave hero.</p>
            </div>
        </div>
        <br>

        <h2>Reviews</h2>
        <div class="review-container">
            <div class="review-index">
                    <span class="review-user">Karol</span>
                    <span class="review-rating">5/5</span>
                    <span class="review-time">29.11.2024 12:34</span>
                <p class="review_text_index">Velmi pekna kniha.</p>
            </div>
            <div class="review-index">
                <span class="review-user">User123</span>
                <span class="review-rating">5/5</span>
                <span class="review-time">29.11.2024 12:34</span>
                <p class="review_text_index">Doporucuji, hezky se to cetlo.</p>
            </div>
            <div class="review-index">
                    <span class="review-user">Petra</span>
                    <span class="review-rating">1/5</span>
                    <span class="review-time">29.11.2024 12:34</span>
                <p class="review_text_index">Malo obrazku. Nelibilo.</p>
            </div>
            <br><br>

            <div class="review_form framed-form">
                <form>
                    <form action="https://WHERE TO?" method="post" enctype="multipart/form-data" class="my_form">
                    <legend>How did you like this book?</legend>
                        <input type="text" id="review_user" name="review_user" value="NICKNAME DOPLNIT" hidden>     <!-- TO DO - je to takhle ok? -->
                        <input type="datetime-local" id="review_time" name="review_time" value="2024-11-29T12:34" hidden>

                        <label for="review_text">Review:</label>
                        <textarea class="form-input form-detail" id="review-text" name="review_text" placeholder="Is there anything you would like to say?"></textarea>

                        <label for="review_rating">Rating:</label>
                        <select class="form-input form-detail" id="review-rating" name="review_rating"><span>/5</span>
                            <option value="1">1/5</option>
                            <option value="2">2/5</option>
                            <option value="3">3/5</option>
                            <option value="4">4/5</option>
                            <option value="5">5/5</option>
                        </select>
                        <br><br>

                        <div class="button-container">
                            <button class="add-comment-button">Add your review</button>
                        </div>
            </div>
        </div>
    </article>
</div>
<?php include 'footer.php'; ?>
