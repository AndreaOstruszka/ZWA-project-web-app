let timer = null;

//General isbn validation
let isbn_input = document.getElementById("isbn") ?? new EventTarget();
isbn_input.addEventListener("keyup", (e) => {
    let isbn = e.target;
    let err_msg = document.getElementById("isbn_error");

    if(timer != null){
        clearTimeout(timer);
        timer = null;
    }

    if (isbn.value.length === 0) {
        isbn_input.classList.add("error-border");
        err_msg.textContent = "ISBN is required";
    } else if (!isbn.value.match(/^\d{10}$|^\d{13}$/)) {
        isbn_input.classList.add("error-border");
        err_msg.textContent = "ISBN must be 10 or 13 numeric characters";
    } else {
        isbn_input.classList.remove("error-border");
        err_msg.textContent = "";
        if(isbn.getAttribute("allowed") != isbn.value)
            timer = setTimeout(check_isbn(isbn.value, e.target, err_msg), 500);
    }
});

// ISBN already exists validation
function check_isbn(isbn, input, error){
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "api/check_isbn.php?isbn="+isbn, true);
    xhr.onreadystatechange = function(){
        if (xhr.readyState == 4 && xhr.status == 200){
            let response = xhr.responseText;
            if(response === "true"){
                input.classList.add("error-border");
                error.textContent = "ISBN is already in use";
            } else {
                input.classList.remove("error-border");
                error.textContent = "";
            }
        }
    }
    xhr.send();
}

document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("add_books") || document.getElementById("edit_books");
    const acceptableLiteraryGenres = ['prose', 'poetry', 'drama'];
    const acceptableFictionGenres = ['romance', 'scifi', 'fantasy', 'horror', 'other'];

    form.addEventListener("submit", function(event) {
        let isValid = true;

        // Clear previous error messages
        document.querySelectorAll(".error").forEach(function(errorElement) {
            errorElement.textContent = "";
        });

        // Validate Title
        const title = document.getElementById("title");
        if (title.value.trim() === "") {
            document.getElementById("title_error").textContent = "Title is required";
            isValid = false;
        }

        // Validate ISBN
        const isbn = document.getElementById("isbn");
        if (isbn.value.trim() === "") {
            document.getElementById("isbn_error").textContent = "ISBN is required JS";
            isValid = false;
        }

        // Validate Author
        const author = document.getElementById("author");
        if (author.value.trim() === "") {
            document.getElementById("author_error").textContent = "Author is required JS";
            isValid = false;
        }

        // Validate Literary Genre
        const literaryGenre = document.getElementById("literary_genre");
        if (literaryGenre.value.trim() === "") {
            document.getElementById("literary_genre_error").textContent = "Literary genre is required";
            isValid = false;
        } else if (!acceptableLiteraryGenres.includes(literaryGenre.value)) {
            document.getElementById("literary_genre_error").textContent = "Invalid literary genre";
            isValid = false;
        }

        // Validate Fiction Genre
        const fictionalGenre = document.getElementById("fictional_genre");
        if (fictionalGenre.value.trim() === "") {
            document.getElementById("fiction_genre_error").textContent = "Fiction genre is required";
            isValid = false;
        } else if (!acceptableFictionGenres.includes(fictionalGenre.value)) {
            document.getElementById("fiction_genre_error").textContent = "Invalid fiction genre";
            isValid = false;
        }

        // Validate Short Description
        const sDesc = document.getElementById("sDesc");
        if (sDesc.value.trim() === "") {
            document.getElementById("sDesc_error").textContent = "Short description is required";
            isValid = false;
        }

        // Validate Long Description
        const lDesc = document.getElementById("lDesc");
        if (lDesc.value.trim() === "") {
            document.getElementById("lDesc_error").textContent = "Long description is required";
            isValid = false;
        }

        // Validate Release Date
        const date = document.getElementById("date");
        if (date.value.trim() === "") {
            document.getElementById("date_error").textContent = "Release date is required";
            isValid = false;
        }

        // Prevent form submission if validation fails
        if (!isValid) {
            event.preventDefault();
        }
    });
});