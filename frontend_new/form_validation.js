let timer = null;
//General email validation
let email_input = document.getElementById("email") ?? new EventTarget();
email_input.addEventListener("keyup", (e) =>{
    let email = e.target.value;
    let err_msg = document.getElementById("email_error");

    if (email.length > 0 && !email.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/)) {
        email_input.classList.add("error-border");
        err_msg.textContent = "Invalid email address";
    } else {
        email_input.classList.remove("error-border");
        err_msg.textContent = "";
    }
});

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


// Password lenght validation
let password_input = document.getElementById("password") ?? new EventTarget();
password_input.addEventListener("keyup", (e) => {
    let password = e.target.value;
    let err_msg = document.getElementById("password_error");

    if (password.length > 0 && password.length < 6) {
        password_input.classList.add("error-border");
        err_msg.textContent = "Password must be at least 6 characters long";
    } else {
        password_input.classList.remove("error-border");
        err_msg.textContent = "";
    }
});

// Password match validation
let repassword_input = document.getElementById("repassword") ?? new EventTarget();
repassword_input.addEventListener("keyup", (e) => {
    let repassword = e.target.value;
    let password = document.getElementById("password").value;
    let err_msg = document.getElementById("repassword_error");

    if (repassword.length > 0 && repassword !== password) {
        repassword_input.classList.add("error-border");
        err_msg.textContent = "Passwords do not match";
    } else {
        repassword_input.classList.remove("error-border");
        err_msg.textContent = "";
    }
});

// Acceptable values for genres
const acceptableLiteraryGenres = ['prose', 'poetry', 'drama'];
const acceptableFictionGenres = ['romance', 'scifi', 'fantasy', 'horror', 'other'];
// Literary genre validation
let literaryGenreInput = document.getElementById("lGenre") ?? new EventTarget();
literaryGenreInput.addEventListener("change", (e) => {
    let literaryGenre = e.target.value;
    let err_msg = document.getElementById("literary_genre_error");

    if (!acceptableLiteraryGenres.includes(literaryGenre)) {
        literaryGenreInput.classList.add("error-border");
        err_msg.textContent = "Invalid literary genre.";
    } else {
        literaryGenreInput.classList.remove("error-border");
        err_msg.textContent = "";
    }
});

// Fiction genre validation
let fictionGenreInput = document.getElementById("fGenre") ?? new EventTarget();
fictionGenreInput.addEventListener("change", (e) => {
    let fictionGenre = e.target.value;
    let err_msg = document.getElementById("fiction_genre_error");

    if (!acceptableFictionGenres.includes(fictionGenre)) {
        fictionGenreInput.classList.add("error-border");
        err_msg.textContent = "Invalid fiction genre.";
    } else {
        fictionGenreInput.classList.remove("error-border");
        err_msg.textContent = "";
    }
});


//------------------Form fields filled in validation------------------//
// Function to check if a field is empty
function isFieldEmpty(field) {
    return field.value.trim() === "";
}

// Function to display error message
function displayError(field, message) {
    field.classList.add("error-border");
    let err_msg = document.getElementById(field.id + "_error");
    err_msg.textContent = message;
}

// Function to clear error message
function clearError(field) {
    field.classList.remove("error-border");
    let err_msg = document.getElementById(field.id + "_error");
    err_msg.textContent = "";
}

// Form validation on submit
let form = document.getElementById("edit_books") ?? new EventTarget();
form.addEventListener("submit", (e) => {
    let isValid = true;

    // List of fields to validate
    let fields = [
        { id: "title", message: "Title is required." },
        { id: "isbn", message: "ISBN is required." },
        { id: "author", message: "Author is required." },
        { id: "lGenre", message: "Literary genre is required." },
        { id: "fGenre", message: "Fiction genre is required." },
        { id: "sDesc", message: "Short description is required." },
        { id: "lDesc", message: "Long description is required." },
        { id: "date", message: "Release date is required." },
        { id: "cover", message: "Book cover is required." }
    ];

    // Validate each field
    fields.forEach(field => {
        let input = document.getElementById(field.id) ?? new EventTarget();
        if (isFieldEmpty(input)) {
            displayError(input, field.message);
            isValid = false;
        } else {
            clearError(input);
        }
    });

    // Prevent form submission if any field is invalid
    if (!isValid) {
        e.preventDefault();
    }
});