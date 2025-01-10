<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION["user_id"])) {
    $_SESSION["redirect_to"] = $_SERVER["REQUEST_URI"];
    header("Location: login.php");
    exit();
}

require_once 'src/db_connection.php';

include 'header.php';

$limit = 3; // Number of reviews per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT reviews.id, reviews.book_id, reviews.user_id, reviews.rating, reviews.review_text, reviews.created_at, users.user_name, books.title AS book_title
        FROM reviews
        JOIN users ON reviews.user_id = users.id
        JOIN books ON reviews.book_id = books.id
        ORDER BY reviews.created_at DESC
        LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$reviews = $stmt->fetchAll();
?>

    <script src="js/load_more_admin.js" defer></script>
    <div id="content">
        <article id="main-widest">
            <h1>Admin panel</h1>
            <h2>All reviews</h2>

            <div id="review_container">
                <?php
                foreach ($reviews as $review) {
                    echo "<div class='review-index'>";
                    echo "<div class='review-time'>" . htmlspecialchars(date('m.d.Y H:i', strtotime($review["created_at"]))) . "</div>";
                    echo "<p>Book: <span class='review-book'><a href='book_detail.php?bookid=" . htmlspecialchars($review["book_id"]) . "' class='link-dark'>" . htmlspecialchars($review["book_title"]) . "</a></span></p>";
                    echo "<p>Review by: <strong class='review-user'>" . htmlspecialchars($review["user_name"]) . "</strong></p>";
                    echo "<p>Rating: <strong class='review-rating'>" . htmlspecialchars($review["rating"]) . "/5</strong></p>";
                    echo "<p class='review-text'>" . htmlspecialchars($review["review_text"]) . "</p>";
                    echo "<span class='review-edit-span'><a href='review_edit.php?review_id=" . htmlspecialchars($review["id"]) . "' class='button-edit'>Edit</a></span>";
                    echo "</div>";
                }
                ?>
            </div>

            <div class="button-container">
                <button class="load-more button" data-genre="review" data-offset="3">More</button>
            </div>

            <h2>Users</h2>

            <div id="users_container">
                <table>
                    <tr>
                        <th>User ID</th>
                        <th>Role</th>
                        <th>Username</th>
                        <th>First name</th>
                        <th>Last name</th>
                        <th>Email</th>
                    </tr>
                <?php
                    $sql = "SELECT id, user_name, first_name, last_name, email, role FROM users";
                    $stmt = $conn->query($sql);
                    $stmt->execute();
                    $users = $stmt->fetchAll();

                    foreach($users as $user){
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($user["id"]) . "</td>";
                        echo "<td>" . ($user["role"] == "admin" ? "Admin" : "User") . "</td>";
                        echo "<td> <a class='link-dark' href='profile-edit-admin.php?userId=" . $user["id"] . "'>" . htmlspecialchars($user["user_name"]) . "</a></td>";
                        echo "<td>" . htmlspecialchars($user["first_name"]) . "</td>";
                        echo "<td>" . htmlspecialchars($user["last_name"]) . "</td>";
                        echo "<td>" . htmlspecialchars($user["email"]) . "</td>";
                        echo "</tr>";
                    }
                ?>
                </table>
            </div>
        </article>
    </div>
<?php include 'footer.php'; ?>