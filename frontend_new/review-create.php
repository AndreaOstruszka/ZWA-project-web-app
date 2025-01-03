<div class="review_form framed-form">
    <form action="#" method="post" enctype="multipart/form-data" class="my_form">
        <legend>How did you like this book?</legend>
        <input type="text" id="review_user" name="user_id" value="<?php echo $_SESSION['user_id']; ?>" hidden>
        <input type="text" id="book_id" name="book_id" value="<?php echo htmlspecialchars($book_id); ?>" hidden>

        <label for="review_text">* Review:</label>
        <textarea class="form-input form-detail" id="review-text" name="review_text" placeholder="Is there anything you would like to say?"></textarea>

        <label for="review_rating">* Rating:</label>
        <select class="form-input form-detail" id="review-rating" name="rating">
            <option value="1">1/5</option>
            <option value="2">2/5</option>
            <option value="3">3/5</option>
            <option value="4">4/5</option>
            <option value="5">5/5</option>
        </select>
        <br>
        <p>* mandatory field</p>
        <br><br>

        <div class="button-container">
            <button class="button" type="submit">Add your review</button>
        </div>
    </form>
</div>