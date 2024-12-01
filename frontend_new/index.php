<?php include 'header.php'; ?>

<div id="content">
    <article id="main-widest">
        <h1>Welcome to BookNook!</h1>
        <div class="main-container">
            <div class="section welcome">
                <h2>Everything about your favourite books</h2>
                <p>Welcome to BookNook! Here, you can explore a vast collection of books across various genres,
                    including popular titles, fantasy, sci-fi, and more. Discover detailed information, read reviews,
                    and easily keep track of newly released books all in one place.
                    <br>
                </p>
            </div>

            <div class="section new_release">
                <h2>New release</h2>
                <div class="new-release-container">
                    <div class="book-cover-div">
                        <img src="images/covers/cover-hobbit.jpg" alt="Hobbit" class="book-cover-mini">
                    </div>
                    <div class="book-info-mini">
                        <p>Name: Hobbit</p>
                        <p>Author: J. R. R. Tolkien</p>
                        <p>Release date: 31.12.2024</p>
                        <p>An adventurous journey of Bilbo Baggins as he seeks to help a group of dwarves
                            reclaim their homeland from the dragon Smaug.</p>
                    </div>
                </div>
            </div>

            <div class="section most_popular">
                <h2>Most popular</h2>
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Author</th>
                        <th>Rating</th>
                    </tr>
                    <tr>
                        <td>Harry Potter</td>
                        <td>J.K. Rowling</td>
                        <td>5</td>
                    </tr>
                    <tr>
                        <td>Hobbit</td>
                        <td>J. R. R. Tolkien</td>
                        <td>4.8</td>
                    </tr>
                    <tr>
                        <td>1984</td>
                        <td>George Orwell</td>
                        <td>4.7</td>
                    </tr>
                    <tr>
                        <td>The Great Gatsby</td>
                        <td>F. Scott Fitzgerald</td>
                        <td>4.5</td>
                    </tr>
                    <tr>
                        <td>Pride and Prejudice</td>
                        <td>Jane Austen</td>
                        <td>4.6</td>
                    </tr>
                    <tr>
                        <td>To Kill a Mockingbird</td>
                        <td>Harper Lee</td>
                        <td>4.9</td>
                    </tr>
                    <tr>
                        <td>The Catcher in the Rye</td>
                        <td>J.D. Salinger</td>
                        <td>4.3</td>
                    </tr>
                    <tr>
                        <td>The Lord of the Rings</td>
                        <td>J.R.R. Tolkien</td>
                        <td>4.9</td>
                    </tr>
                    <tr>
                        <td>The Chronicles of Narnia</td>
                        <td>C.S. Lewis</td>
                        <td>4.7</td>
                    </tr>
                    <tr>
                        <td>Brave New World</td>
                        <td>Aldous Huxley</td>
                        <td>4.6</td>
                    </tr>
                </table>

            </div>
            <div class="section new_reviews">
                <h2>New reviews</h2>
                <div class="review-index">
                    <div class="review-time">30.11.2024 16:10</div>
                    <p>Book: <span class="review-book">The Hobbit</span></p>
                    <p>Review by: <strong class="review-user">John Doe</strong></p>
                    <p>Rating: <strong class="review-rating">5/5</strong></p>
                    <p class="review-text">This book was an amazing journey. I couldn't put it down!</p>
                </div>
                <div class="review-index">
                    <div class="review-time">30.11.2024 16:10</div>
                    <p>Book: <span class="review-book">1984</span></p>
                    <p>Review by: <strong class="review-user">Jane Smith</strong></p>
                    <p>Rating: <strong class="review-rating">4/5</strong></p>
                    <p class="review-text">A bit slow at the start, but worth it in the end. Loved the
                        characters!</p>
                </div>
                <div class="review-index">
                    <div class="review-time">30.11.2024 16:10</div>
                    <p>Book: <span class="review-book">The Catcher in the Rye</span></p>
                    <p>Review by: <strong class="review-user">Alice Johnson</strong></p>
                    <p>Rating: <strong class="review-rating">5/5</strong></p>
                    <p class="review-text">A thought-provoking read about adolescence and the struggle with
                        identity. A
                        must-read for fans of classic literature.</p>
                </div>
            </div>
        </div>
    </article>
</div>

<?php include 'footer.php'; ?>
