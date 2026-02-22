<?php
// Настройки подключения к базе данных
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "market";

try {
    // Подключение через PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    // Устанавливаем режим ошибок и кодировку
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");

    // Для отладки - раскомментируйте следующую строку
    // echo "Подключение к базе данных успешно!";
} catch (PDOException $e) {
    // Выводим понятное сообщение об ошибке
    die(" Ошибка подключения к базе данных: " . $e->getMessage() .
        "<br>Проверьте:<br>" .
        "1. Сервер: $servername<br>" .
        "2. Имя БД: $dbname<br>" .
        "3. Пользователь: $username<br>" .
        "4. Пароль: указан");
}
