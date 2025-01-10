document.addEventListener('DOMContentLoaded', function () {
    function loadReviews(page) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'api/fetch_reviews.php?page=' + page, true);
        xhr.onload = function () {
            if (this.status === 200) {
                document.getElementById('reviews-container').innerHTML = this.responseText;
            }
        };
        xhr.send();
    }

    // loadReviews (<?php echo $page; ?>);

    // Get the initial page value from a data attribute
    const initialPage = document.querySelector('.pagination').getAttribute('data-initial-page');
    loadReviews(initialPage);

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