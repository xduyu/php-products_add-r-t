<?php
session_start();
require '../config.php';
require '../php/pattern.php';
$aerr = [];
if ($_SERVER['SERVER_METHOD'] = 'POST' && empty($aerr)) {
    $useremail = trim($_POST['useremail']);
    $userpassword = $_POST['userpassword'];
    // проверка email
    if (empty($useremail)) {
        $aerr[] = "Email обязательное для ввода";
    } elseif (!ValidationPatterns::isValidEmail($useremail)) {
        $aerr[] = "Ошибка в Email";
    }
    // проверка пароль
    if (empty($userpassword)) {
        $aerr[] = "password обязательное для ввода";
    } elseif (!ValidationPatterns::isValidStrongPassword($userpassword)) {
        $aerr[] = "Ошибка в пароле";
    }
    if (empty($aerr)) {
        try {
            $qu = $pdo->prepare("SELECT * FROM users_role WHERE UserEmail = ?");
            $qu->execute([$useremail]);
            $user = $qu->fetch();
            // print_r($user);
            // echo $user['UserPassword'];
            if ($user) {
                if (password_verify($userpassword, $user["UserPassword"])) {
                    $_SESSION['user_id'] = $user['UserId'];
                    $_SESSION['user_name'] = $user['UserName'];
                    $_SESSION['user_surname'] = $user['UserSurname'];
                    $_SESSION['user_email'] = $user['UserEmail'];
                    $_SESSION['user_regd'] = $user['RegDate'];
                    $_SESSION['user_roleid'] = $user['RoleId'];
                    $_SESSION['user_rolename'] = $user['RoleName'];
                    header('Location: ../index.php');
                } else {
                    $aerr[] = "Пароли не совпадают";
                }
            }
        } catch (PDOException $pdoerror) {
            echo $pdoerror;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
    <h1>Auth</h1>
    <?php if (!empty($aerr)): ?>
        <h2>Ошибки:</h2>
        <ul class="error_list">
            <?php foreach ($aerr as $error): ?>
                <li class="error_item error"><?php echo ($error) ?></li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
    <?php if (isset($_GET['success'])): ?>
        <div class="success">Регистрация успешна</div>
    <?php endif ?>
    <form action="" method="POST">
        <div class="form-group">
            <span>Email:*</span>
            <input type="email" id="useremail" name="useremail" required placeholder="Введите вашу почту">
        </div>
        <div class="form-group">
            <span>Пароль:*</span>
            <input type="password" id="userpassword" name="userpassword" required placeholder="Введите пароль">
        </div>
        <div class="password-visit">
            <label><input type="checkbox" id="showPass"><span>Показать пароль</span></label>
        </div>

        <button>Войти</button>
    </form>
    <div class="reg__link">
        <div class="">Don't Have account? <a href="registration.php">registration</a></div>
    </div>
    <script src="../scripts/show_password.js"></script>
</body>

</html>