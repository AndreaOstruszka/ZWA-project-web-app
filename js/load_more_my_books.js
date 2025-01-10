document.addEventListener('DOMContentLoaded', function () {
    const loadMoreButton = document.querySelector('.load-more[data-genre="books"]');

    loadMoreButton.addEventListener('click', function () {
        const offset = parseInt(this.getAttribute('data-offset'));
        const container = document.querySelector('.book-container');

        fetch(`api/load_more_user_books.php?offset=${offset}`)
            .then(response => response.text())
            .then(data => {
                if (!data) {
                    this.style.display = 'none';
                    return;
                }
                container.insertAdjacentHTML('beforeend', data);
                this.setAttribute('data-offset', offset + 12);
            })
            .catch(error => console.error('Error fetching data:', error));
    });
});