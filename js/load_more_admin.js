    document.addEventListener('DOMContentLoaded', function () {
    const loadMoreButton = document.querySelector('.load-more');

    loadMoreButton.addEventListener('click', function () {
    const offset = parseInt(this.getAttribute('data-offset'));
    const container = document.getElementById('review_container');

    fetch(`api/fetch_reviews_admin.php?offset=${offset}`)
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