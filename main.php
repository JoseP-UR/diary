<?php
session_start();

$link = mysqli_connect('localhost', 'root', '', 'diaryapp');
if (mysqli_connect_error()) {
    die('error: ' + mysqli_connect_error());
}

if ((!isset($_SESSION['user']) && !isset($_SESSION['pass'])) || (!isset($_COOKIE['diaryuser']) && !isset($_COOKIE['diarypass']))) {
    header('Location: index.php');
} else {
    $query = "SELECT * FROM user WHERE name='" . $_COOKIE['diaryuser'] . "' AND password='" . $_COOKIE['diarypass'] . "'";
    $result = mysqli_query($link, $query);
    $rows = mysqli_fetch_array($result);

    if (!mysqli_num_rows($result)) {
        header('Location: index.php');
    }
}



if (isset($_POST['diary'])) {
    $query = "UPDATE user SET diary = '" . mysqli_escape_string($link, $_POST['diary']) . "' WHERE user.name='" . mysqli_escape_string($link, $_SESSION['user']) . "'";
    mysqli_query($link, $query);
}

$query = "SELECT * FROM user WHERE name='" . $_SESSION['user'] . "' AND password='" . $_SESSION['pass'] . "'";
$result = mysqli_query($link, $query);
$rows = mysqli_fetch_array($result);

$_SESSION['diary'] = htmlspecialchars($rows['diary']);

if (isset($_GET['logout'])) {
    setcookie('diaryuser', null, time() * (-30));
    setcookie('diarypass', null, time() * (-30));
    session_destroy();
    header('Location: index.php');
}
?>


<html>

<head>
    <title>My Diary</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1 class="title">Welcome to your diary, <?php print_r($_SESSION['user']); ?>:</h1>
    <div class="form-container -diary">
        <form method="post">
            <textarea name="diary"><?php echo htmlspecialchars($_SESSION['diary']); ?></textarea>
            <input type="submit" value="save">
        </form>
    </div>
    <a class="button -logout" href="?logout">logout</a>
</body>

</html>
