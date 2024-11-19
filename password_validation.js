// password validation with error message
    function validatePassword() {
    const password = document.querySelector('input[name="password"]').value;
    const passwordConfirm = document.querySelector('input[name="password_confirm"]').value;
    const errorMessage = document.getElementById('password-error');

    if (password !== passwordConfirm) {
    errorMessage.textContent = "Passwords do not match.";
}
    else {
    errorMessage.textContent = "";          // Clear error message if passwords match
}
}
// addEventListener starts when user starts typing into the password field
    document.querySelector('input[name="password"]').addEventListener('input', validatePassword);
    document.querySelector('input[name="password_confirm"]').addEventListener('input', validatePassword);