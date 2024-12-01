<?php include 'header.php'; ?>

<div id="content">
    <article id="main-widest">
        <h1>Registration</h1>
        <br>
        <div class="form-wrapper">
            <form action="https://WHERE TO?" method="post" enctype="multipart/form-data" class="my_form">
                <legend>Please fill in info about yourself:</legend>
                <label for="fName">First name:</label>
                <input class="form-input" id="fName" type="text" name="fName" placeholder="John" required>

                <label for="lName">Last name:</label>
                <input class="form-input" id="lName" type="text" name="lName" placeholder="Doe" required>

                <label for="nickname">Nickname:</label>
                <input class="form-input" id="nickname" type="text" name="nickname" placeholder="BookWorm125" required>

                <label for="email">Email:</label>
                <input class="form-input" id="email" type="email" name="email" value="@" required>

                <label for="password">Password:</label>
                <input class="form-input" id="password" type="password" name="password" placeholder="at least 6 characters" required>

                <label for="repassword">Re-enter password:</label>
                <input class="form-input" id="repassword" type="password" name="repassword" required>

                <label class="checkbox-label">
                    <input type="checkbox" name="agreed" value="yes" checked required>
                    I agree to the BookNook Terms of Service and Privacy Policy
                </label>

                <div class="button-container">
                    <button class="button-submit" type="submit">Register</button>
                    <button class="button-reset" type="reset">Reset form</button>
                </div>
            </form>
            <div class="link-div">
                <p>Already have an account? Click <a href="login.php">here!</a></p>
            </div>
            <br><br>
        </div>
    </article>
</div>

<?php include 'header.php'; ?>
