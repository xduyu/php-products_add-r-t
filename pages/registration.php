<?php
require '../config.php';
require_once '../php/pattern.php';
$arrErr = [];
$arrRoles = [];

try {
    $stmt_roles = $pdo->query("SELECT RoleId, RoleName FROM role");
    $stmt_roles->execute();
    $arrRoles = $stmt_roles->fetchAll();
    // print_r($arrRoles);
} catch (PDOException $err) {
    echo ($err);
    $arrErr[] = $err;
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && empty($arrErr)) {
    $username = trim($_POST['username']);
    $usersurname = trim($_POST['usersurname']);
    $useremail = trim($_POST['useremail']);
    $userpassword = $_POST['userpassword'];
    $confirm_password = $_POST['confirm_password'];
    $userrole = $_POST['userrole'] ?? "0";

    // проверка имени
    if (empty($username)) {
        $arrErr[] = "Имя обязательное для ввода";
    } elseif (!ValidationPatterns::isValidName($username)) {
        $arrErr[] = "Ошибка в имени";
    }
    // проверка фамилии
    if (empty($usersurname)) {
        $arrErr[] = "Фамилия обязательное для ввода";
    } elseif (!ValidationPatterns::isValidName($usersurname)) {
        $arrErr[] = "Ошибка в фамилии";
    }
    // проверка email
    if (empty($useremail)) {
        $arrErr[] = "Email обязательное для ввода";
    } elseif (!ValidationPatterns::isValidEmail($useremail)) {
        $arrErr[] = "Ошибка в Email";
    }
    // проверка пароль
    if ((empty($userpassword) || empty($confirm_password))) {
        $arrErr[] = "password обязательное для ввода";
    } elseif (!ValidationPatterns::isValidStrongPassword($userpassword)) {
        $arrErr[] = "Ошибка в password";
    }
    if ($userpassword != $confirm_password) $arrErr[] = "Пароли не совпадают";

    // проверка на роли
    if (empty($userrole)) {
        $arrErr[] = "Необходимо выбрать роль";
    } else {
        $flag = false;
        foreach ($arrRoles as $role) {
            if ($role["RoleId"] == $userrole) {
                $flag = true;
                break;
            }
            if ($flag == false) {
                $arrErr[] = "Выбрано недопустимое значение";
            }
        }
    }
    if (empty($arrErr)) {
        $checkEmail = $pdo->prepare("SELECT userID FROM users Where UserEmail=?");
        $checkEmail->execute([$useremail]);
        if ($checkEmail->rowCount() > 0) {
            $arrErr[] = "Пользователь с таким Email уже зарегестрирован!";
        } else {
            $hashedPass = password_hash($userpassword, PASSWORD_BCRYPT);
            if (!$hashedPass) {
                $arrErr[] = "Ошибка хеширования";
            } else {
                try {
                    $quary = $pdo->prepare("
                    INSERT INTO users(UserName,UserSurname,UserEmail,UserPassword,UserRole)
                    VALUES(?, ?, ?, ?, ?)
                    ");
                    $quary->execute([$username, $usersurname, $useremail, $hashedPass, $userrole]);
                    header('Location: ./login.php?success=1');
                } catch (PDOException $pdoe) {
                    echo $pdoe;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>registration</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>

    <?php if (!empty($arrErr)): ?>
        <h2>Ошибки:</h2>
        <ul class="error_list">
            <?php foreach ($arrErr as $error): ?>
                <li class="error_item"><?php echo ($error) ?></li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>

    <div class="container">
        <h1>registration</h1>
        <form method="POST" id="regForm">
            <div class="form-group">
                <span>Имя:*</span>
                <input type="text" id="username" name="username" required placeholder="Введите ваше имя">
            </div>
            <div class="form-group">
                <span>Фамилия:*</span>
                <input type="text" id="usersurname" name="usersurname" required placeholder="Введите вашу фамилию">
            </div>
            <div class="form-group">
                <span>Email:*</span>
                <input type="email" id="useremail" name="useremail" required placeholder="Введите вашу почту">
            </div>

            <div class="form-group">
                <span>Пароль:*</span>
                <input type="password" id="userpassword" name="userpassword" required placeholder="Введите пароль">
            </div>

            <div class="from-group">
                <span>Роль:</span>
                <select name="userrole" id="userrole" required>
                    <option value="" disabled selected>Роли</option>
                    <?php foreach ($arrRoles as $role): ?>
                        <option value="<?php echo $role["RoleId"] ?>">
                            <?php echo $role["RoleName"] ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="form-group">
                <span>Подтверждение пароля:*</span>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Повторите пароль">
            </div>

            <div class="password-visit">
                <label><input type="checkbox" id="showPass"><span>Показать пароль</span></label>
            </div>

            <button>Зарегистрироваться</button>
            <a href="./login.php" class="link">Войти в систему</a>
        </form>
    </div>
    <script src="../scripts/show_password.js"></script>
</body>

</html>