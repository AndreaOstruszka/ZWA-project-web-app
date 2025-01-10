document.addEventListener('DOMContentLoaded', function () {
    const loadMoreButton = document.querySelector('.load-more');

    loadMoreButton.addEventListener('click', function () {
        const offset = parseInt(this.getAttribute('data-offset'));
        const container = document.getElementById('review_container');

        fetch(`api/load_more_reviews.php?offset=${offset}`)
            .then(response => response.text())
            .then(data => {
                if (!data) {
                    this.style.display = 'none';
                    return;
                }
                container.insertAdjacentHTML('beforeend', data);
                this.setAttribute('data-offset', offset + 3);
            })
            .catch(error => console.error('Error fetching data:', error));
    });
});