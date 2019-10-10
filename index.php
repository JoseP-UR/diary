<?php
session_start();
session_reset();
$link = mysqli_connect('localhost', 'root', '', 'diaryapp');
if (mysqli_connect_error()) {
    die('error: ' + mysqli_connect_error());
}

if (isset($_COOKIE['diaryuser'])) {
    $query = "SELECT * FROM user WHERE name='" . mysqli_escape_string($link, $_COOKIE['diaryuser']) . "'";

    $result = mysqli_query($link, $query);

    if (!mysqli_num_rows($result)) {
        $_SESSION['loginError'] = 'Bad user cookie';
    } else {
        $rows = mysqli_fetch_array($result);

        if (mysqli_escape_string($link, $_COOKIE['diarypass']) == mysqli_escape_string($link, $rows['password'])) {
            $_SESSION['user'] = $rows['name'];
            $_SESSION['pass'] = $rows['password'];

            header('Location: main.php');
        } else {
            $_SESSION['loginError'] = 'Bad password cookie';
            setcookie('diaryuser', $rows['name'], time() * (-30));
            setcookie('diarypass', $rows['password'], time() * (-30));
        }
    }
}

if ($_POST) {

    setcookie('diaryuser', null, time() * (-30));
    setcookie('diarypass', null, time() * (-30));

    if (isset($_POST['rnome'])) {
        $query = "SELECT * FROM user WHERE name='" . mysqli_escape_string($link, $_POST['rnome']) . "'";

        $result = mysqli_query($link, $query);

        if (mysqli_num_rows($result)) {
            print_r('<p class="-warning">'.$_POST['rnome'].' is already registered</p>');
        } else {
            if (strlen($_POST['rnome']) < 4 || strlen($_POST['rsenha']) < 4){
                $_SESSION['insertError'] = 'Some fields are invalid';
            }
            else {
                $encpass = password_hash($_POST['rsenha'], PASSWORD_DEFAULT);
                $query = "INSERT INTO user(name,password) VALUES ('" . mysqli_escape_string($link, $_POST['rnome']) . "','" . mysqli_escape_string($link, $encpass) . "')";

                mysqli_query($link, $query);
                print_r('<p class="-success">'.$_POST['rnome'].' registered successfuly</p>');
            }
           
        }
    }
    

    if (isset($_POST['nome'])) {
        $query = "SELECT * FROM user WHERE name='" . mysqli_escape_string($link, $_POST['nome']) . "'";

        $result = mysqli_query($link, $query);

        if (!mysqli_num_rows($result)) {
            $_SESSION['loginError'] = 'User not registered';
        } else {
            $rows = mysqli_fetch_array($result);

            if (password_verify(mysqli_escape_string($link, $_POST['senha']), $rows['password'])) {
                setcookie('diaryuser', $rows['name']);
                setcookie('diarypass', $rows['password']);
                $_SESSION['user'] = $rows['name'];
                $_SESSION['pass'] = $rows['password'];
                header('Location: main.php');
            } else {
                $_SESSION['loginError'] = 'Incorrect Password';
            }
        }
    }
}
?>


<html>

<head>
    <title>My Diary</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    
</head>

<body>
    <div class="form-container -login">
        <?php
        if(isset($_SESSION['loginError'])) {
            echo '<p class="-error">'.htmlspecialchars($_SESSION['loginError']).'</p>';
        }
        ?>
    <h1>login:</h1>
    <form method="post">
        <input type="text" name="nome" placeholder="name">
        <input type="password" name="senha" placeholder="password">
        <input type="submit" value="login">
    </form>
</div>
<button class="button" id="register-toggle">Register</button>
<div class="form-container -register">
    <?php
        if(isset($_SESSION['insertError'])) {
            echo '<p class="-error">'.htmlspecialchars($_SESSION['insertError']).'</p>';
        }
        ?>
    <h1>register:</h1>
    <form method="post">
        <input type="text" name="rnome" placeholder="name">
        <input type="password" name="rsenha" placeholder="password">
        <input type="submit" value="register">
    </form>
</div>
<script src="main.js"></script>

</body>
</html>