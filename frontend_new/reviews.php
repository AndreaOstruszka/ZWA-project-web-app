<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'src/db_connection.php';

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
    <script src="js/load_reviews.js" defer></script>
    <div id="content">
        <article id="main-widest">
            <h1>Recently posted reviews</h1>
            <h2>What do other users think?</h2>
            <br>

            <div id="reviews-container">
                <!-- Reviews will be loaded here via AJAX -->
            </div>

            <div class="pagination" data-initial-page="<?php echo $page; ?>">
                <?php if ($page > 1): ?>
                    <a href="#" data-page="1">&laquo; First</a>
                    <a href="#" data-page="<?php echo $page - 1; ?>">&lt; Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="#"
                       data-page="<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="#" data-page="<?php echo $total_pages; ?>">Last &raquo;</a>
                <?php endif; ?>
            </div>

            <div class="spacing"></div>
        </article>
    </div>
<?php include 'footer.php'; ?>