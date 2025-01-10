// General email validation
function validateEmail() {
    let email_input = document.getElementById("email");
    let err_msg = document.getElementById("email_error");
    let email = email_input.value;

    if (email.length > 0 && !email.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/)) {
        email_input.classList.add("error-border");
        err_msg.textContent = "Invalid email address";
    } else {
        email_input.classList.remove("error-border");
        err_msg.textContent = "";
    }
}

// Add event listeners
document.getElementById("email").addEventListener("keyup", () => {
    validateEmail();
});

// Password length validation
function validatePassword() {
    let password_input = document.getElementById("password");
    let err_msg = document.getElementById("password_error");
    let password = password_input.value;

    if (password.length > 0 && password.length < 6) {
        password_input.classList.add("error-border");
        err_msg.textContent = "Password must be at least 6 characters long";
    } else {
        password_input.classList.remove("error-border");
        err_msg.textContent = "";
    }
}

// Password match validation
function validateRepassword() {
    let repassword_input = document.getElementById("repassword");
    let password_input = document.getElementById("password");
    let err_msg = document.getElementById("repassword_error");
    let repassword = repassword_input.value;
    let password = password_input.value;

    if (repassword.length > 0 && repassword !== password) {
        repassword_input.classList.add("error-border");
        err_msg.textContent = "Passwords do not match";
    } else {
        repassword_input.classList.remove("error-border");
        err_msg.textContent = "";
    }
}

// Username already exists validation
function checkUsername() {
    let user_name_input = document.getElementById("user_name");
    let user_name_error = document.getElementById("user_name_error");
    let username = user_name_input.value;

    if (username.length > 0) {
        let xhr = new XMLHttpRequest();
        xhr.open("GET", "api/check_username.php?username=" + encodeURIComponent(username), true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                let response = xhr.responseText.trim();
                if (response === "true") {
                    user_name_input.classList.add("error-border");
                    user_name_error.textContent = "Username is already in use";
                } else {
                    user_name_input.classList.remove("error-border");
                    user_name_error.textContent = "";
                }
            }
        };
        xhr.send();
    } else {
        user_name_input.classList.remove("error-border");
        user_name_error.textContent = "";
    }
}

// Add event listeners
document.getElementById("email").addEventListener("keyup", validateEmail);
document.getElementById("password").addEventListener("keyup", validatePassword);
document.getElementById("repassword").addEventListener("keyup", validateRepassword);
document.getElementById("user_name").addEventListener("keyup", checkUsername);

// Form validation on submit
document.querySelector(".my_form").addEventListener("submit", (e) => {
    let isValid = true;

    // List of fields to validate
    let fields = [
        { id: "first_name", message: "First name is required" },
        { id: "last_name", message: "Last name is required" },
        { id: "user_name", message: "User name is required" },
        { id: "email", message: "Email is required" }
    ];

    // Validate each field
    fields.forEach(field => {
        let input = document.getElementById(field.id);
        if (input.value.trim() === "") {
            input.classList.add("error-border");
            document.getElementById(field.id + "_error").textContent = field.message;
            isValid = false;
        } else {
            input.classList.remove("error-border");
            document.getElementById(field.id + "_error").textContent = "";
        }
    });

    // Prevent form submission if any field is invalid
    if (!isValid) {
        e.preventDefault();
    }
});

// Validate user role
function validateUserRole() {
    let role_input = document.getElementById("role");
    let err_msg = document.getElementById("role_error");
    let role = role_input.value;

    if (role !== "registered_user" && role !== "admin") {
        role_input.classList.add("error-border");
        err_msg.textContent = "Invalid role selected";
    } else {
        role_input.classList.remove("error-border");
        err_msg.textContent = "";
    }
}

// Add event listener for role validation
document.getElementById("role").addEventListener("change", validateUserRole);

// Form validation on submit
document.querySelector(".my_form").addEventListener("submit", (e) => {
    let isValid = true;

    // Validate user role
    validateUserRole();
    let role_error = document.getElementById("role_error").textContent;
    if (role_error !== "") {
        isValid = false;
    }

    // Prevent form submission if any field is invalid
    if (!isValid) {
        e.preventDefault();
    }
});