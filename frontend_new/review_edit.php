<?php include 'header.php'; ?>

<div id="content">
    <article id="main-widest">
        <h1>Edit your review</h1>
        <br>
        <div class="form-wrapper">
            <form action="https://WHERE TO?" method="post" enctype="multipart/form-data" class="my_form">
                <legend>How did you like this book?</legend>
                <br>
                <input type="text" id="review_user" name="review_user" value="NICKNAME DOPLNIT" hidden>
                <!-- TO DO - je to takhle ok? -->
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
                    <button class="button">Edit</button>
                    <button class="button">Delete</button>
                </div>
            </form>
        </div>
    </article>
</div>

<?php include 'footer.php'; ?>