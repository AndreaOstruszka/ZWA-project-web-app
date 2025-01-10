// Smooth scroll to the top
document.addEventListener('DOMContentLoaded', function() {
    const returnButton = document.querySelector('.return');

    returnButton.addEventListener('click', function(event) {
        event.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});