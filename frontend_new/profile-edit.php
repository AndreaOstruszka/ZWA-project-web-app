<?php include 'header.php'; ?>

<div id="content">
    <article id="main-widest">
        <h1>Edit Profile</h1>
        <br>
        <div class="form-wrapper">
            <form action="https://WHERE TO?" method="post" enctype="multipart/form-data" class="my_form">
                <fieldset>
                    <legend>Please update your info:</legend>
                    <br>
                    <label for="fName">First name:</label>
                    <input class="form-input" id="fName" type="text" name="fName" placeholder="John" required>

                    <label for="lName">Last name:</label>
                    <input class="form-input" id="lName" type="text" name="lName" placeholder="Smith" required>

                    <label for="nickname">Nickname:</label>
                    <input class="form-input" id="nickname" type="text" name="nickname" placeholder="BookWorm125" required>

                    <label for="email">Email:</label>
                    <input class="form-input" id="email" type="email" name="email" required value="@">

                    <label for="password">New Password:</label>
                    <input class="form-input" id="password" type="password" name="password" placeholder="at least 6 characters">
                    <label for="repassword">Re-enter New Password:</label>
                    <input class="form-input" id="repassword" type="password" name="repassword">

                    <div class="button-container">
                        <button class="button" type="submit">Save changes</button>
                        <button class="button" type="reset">Reset form</button>
                    </div>
                </fieldset>
            </form>
            <br><br>
        </div>
    </article>
</div>

<?php include 'footer.php'; ?>
