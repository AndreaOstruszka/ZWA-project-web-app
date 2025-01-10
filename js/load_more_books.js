document.addEventListener('DOMContentLoaded', function () {
    const loadMoreButtons = document.querySelectorAll('.load-more');

    loadMoreButtons.forEach(button => {
        button.addEventListener('click', function () {
            console.log("Clicked " + button.getAttribute("data-genre"));
            const genre = this.getAttribute('data-genre');
            const offset = parseInt(this.getAttribute('data-offset'));
            const container = document.getElementById(genre + '_books');

            fetch(`api/load_more_books.php?genre=${genre}&offset=${offset}`)
                .then(response => response.text())
                .then(data => {
                    if (!data) {
                        this.style.display = 'none';
                        return;
                    }
                    container.insertAdjacentHTML('beforeend', data);
                    this.setAttribute('data-offset', offset + 5);
                })
                .catch(error => console.error('Error fetching data:', error));
        });
    });
});