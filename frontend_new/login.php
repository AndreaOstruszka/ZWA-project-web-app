<?php include 'header.php'; ?>

<div id="content">
    <article id="main-widest">
        <h1>Log in</h1>
        <br>
        <div class="form-wrapper">
            <div class="form-container">
                <form action="https://WHERE TO?" method="post" enctype="multipart/form-data" class="my_form">
                    <fieldset>
                        <legend>Please fill in your login info:</legend>
                        <br>
                        <label for="nickname">Nickname:</label>
                        <input class="form-input" id="nickname" type="text" name="name" placeholder="Bookworm125" required>
                        <br><br>
                        <label for="password">Password:</label>
                        <input class="form-input" id="password" type="password" name="password" placeholder="*******" required>
                        <br><br>
                        <div class="button-container">
                            <button class='button' type="submit">Log in</button>
                            <button class='button' type="reset">Reset form</button>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="link-div">
                <p>Don't have an account yet? Click <a href="register.php">here!</a></p>
            </div>
            <br><br>
        </div>
    </article>
</div>

<?php include 'footer.php'; ?>
