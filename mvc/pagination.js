// pagination.js
function fetchBooks(page = 1) {
    fetch(`BookController.php?page=${page}`)
        .then(response => response.json())
        .then(data => {
            displayBooks(data.books);
            displayPagination(data.total_pages, data.current_page);
        })
        .catch(error => console.error('Error:', error));
}

function displayBooks(books) {
    const bookList = document.getElementById('book-list');
    bookList.innerHTML = '';

    if (books.length === 0) {
        bookList.innerHTML = '<p>No results found</p>';
    } else {
        books.forEach(book => {
            const bookItem = document.createElement('div');
            bookItem.innerHTML = `
                <strong>Name:</strong> ${book.name} <br>
                <strong>ISBN:</strong> ${book.isbn} <br>
                <strong>Literary Genre:</strong> ${book.literary_genre} <br>
                <strong>Fiction Genre:</strong> ${book.fiction_genre} <br><br>
            `;
            bookList.appendChild(bookItem);
        });
    }
}

function displayPagination(totalPages, currentPage) {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const pageLink = document.createElement('a');
        pageLink.href = '#';
        pageLink.textContent = i;
        pageLink.style.margin = '0 5px';

        if (i === currentPage) {
            pageLink.style.fontWeight = 'bold';
        }

        pageLink.addEventListener('click', (event) => {
            event.preventDefault();
            fetchBooks(i);
        });

        pagination.appendChild(pageLink);
    }
}

// Initial fetch
document.addEventListener('DOMContentLoaded', () => {
    fetchBooks();
});