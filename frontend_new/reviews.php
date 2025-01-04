<?php
session_start();

require_once 'db_connection.php';

$limit = 5; // Number of reviews per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql_total = "SELECT COUNT(*) FROM reviews";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->execute();
$total_reviews = $stmt_total->fetchColumn();
$total_pages = ceil($total_reviews / $limit);
?>

<?php include 'header.php'; ?>

<div id="content">
    <article id="main-widest">
        <h1>Recently posted reviews</h1>
        <h2>What do other users think?</h2>
        <br>

        <div id="reviews-container">
            <!-- Reviews will be loaded here via AJAX -->
        </div>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="#" data-page="1">&laquo; First</a>
                <a href="#" data-page="<?php echo $page - 1; ?>">&lt; Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="#" data-page="<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="#" data-page="<?php echo $total_pages; ?>">Last &raquo;</a>
            <?php endif; ?>
        </div>

        <div class="spacing"></div>
    </article>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function loadReviews(page) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_reviews.php?page=' + page, true);
            xhr.onload = function () {
                if (this.status === 200) {
                    document.getElementById('reviews-container').innerHTML = this.responseText;
                }
            };
            xhr.send();
        }

        loadReviews(<?php echo $page; ?>);

        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');
                loadReviews(page);
                document.querySelectorAll('.pagination a').forEach(a => a.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });
</script>

<?php include 'footer.php'; ?>