<!DOCTYPE html>
<html lang='en'>
<head>
    <title>BookNook</title>
    <meta charset='utf-8'>
    <meta name="description" content='BOOK NOOK'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans&amp;subset=greek-ext,greek,latin-ext' rel='stylesheet' type='text/css'>
    <script src="https://kit.fontawesome.com/7bce69507d.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css" type="text/css">
</head>

<body>
<header>
    <?php
    if (empty($_SESSION) || !isset($_SESSION["user_name"])) {
        $guest_msg = '<div id="header-links-container"><a class="header_login_link" href="login.php?redirect=' . $_SERVER["REQUEST_URI"] . '"><i class="fa-solid fa-lock"></i> Log in </a><a class="header-register-link" href="register.php"><i class="fa-solid fa-user-plus"></i> Register</a></div>';
        echo $guest_msg;
    } else {
        $user_name = $_SESSION["user_name"];
        $user_msg = '<div id="header-links-container"><a class="header_login_link" href="profile.php"><i class="fa-solid fa-user"></i> ' . htmlspecialchars($user_name) . '</a><a class="header-register-link" href="api/_logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a></div>';
        echo $user_msg;
    }
    ?>
    <a href="index.php"><img src="images/gui/logo.svg" alt="BookNook" id="logo"></a>
    <div id="navigation">
        <ul id="ul-navigation">
            <li class="li-navigation"><a class="navigation-item" href="index.php"><i class="fa fa-bookmark"></i> Main page</a></li>
            <li class="li-navigation"><a class="navigation-item" href="books.php"><i class="fa-solid fa-book-open"></i> Books</a></li>
            <li class="li-navigation"><a class="navigation-item" href="charts.php"><i class="fa-solid fa-chart-simple"></i> Charts</a></li>
            <li class="li-navigation"><a class="navigation-item" href="reviews.php"><i class="fa-solid fa-comment"></i> Reviews</a></li>
            <li class="li-navigation"><a class="navigation-item" href="profile.php"><i class="fa-solid fa-user"></i> My profile</a></li>
        </ul>
    </div>
    <div class="spacing"></div>
</header>