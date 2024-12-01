<?php include 'header.php'; ?>

<div id="content">
    <article id="main-widest">
        <h1>Registration</h1>
        <br>
        <div class="form-wrapper">
            <form id="add_books" class="my_form">
                <legend>Please fill in book info:</legend>
                <div class="form_group">
                    <label for="book_name">Name of the Book:</label>
                    <input class="form-input" type="text" id="book_name" name="book_name" required>
                </div>
                <div class="form_group">
                    <label for="isbn">ISBN (10 or 13 digits):</label>
                    <input class="form-input" type="text" id="isbn" name="isbn" pattern="\d{10}|\d{13}" required>
                </div>
                <div class="form_group">
                    <label for="author_name">Name of the Author:</label>
                    <input class="form-input" type="text" id="author_name" name="author_name" required>
                </div>
                <div class="form_group">
                    <label for="literary_genre">Literary Genre:</label>
                    <select class="form-select" id="literary_genre" name="literary_genre" required>
                        <option value="prose">Prose</option>
                        <option value="poetry">Poetry</option>
                        <option value="drama">Drama</option>
                    </select>
                </div>
                <div class="form_group">
                    <label for="fiction_genre">Fiction Genre:</label>
                    <select class="form-select" id="fiction_genre" name="fiction_genre" required>
                        <option value="romance">Romance</option>
                        <option value="scifi">Sci-Fi</option>
                        <option value="fantasy">Fantasy</option>
                        <option value="thriller">Thriller</option>
                        <option value="horror">Horror</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form_group">
                    <label for="book_cover">Upload Book Cover:</label>
                    <input type="file" class="form-file" id="book_cover" name="book_cover" accept="image/*" required>
                </div>
                <button type="submit" class="button-submit">Add Book</button>
            </form>n
        </div>
    </article>
</div>

<?php include 'footer.php'; ?>
