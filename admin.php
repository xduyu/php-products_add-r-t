<?php
session_start();
require './config.php';

$products_admin = [];
try {
    $q = $pdo->prepare('SELECT * FROM products ORDER BY product_id DESC');
    $q->execute();
    $products_admin = $q->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $pe) {
    echo $pe->getMessage();
}

// admin user: em: a@a.com, p: Delfa!333
// user: e: u@u.com, p: Delfa!333

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = trim($_POST['name']); // название товара
    $description = trim($_POST['description']); // описание товара
    $price = floatval($_POST['price']); // цена товара 
    $admin_id = $_SESSION['user_id']; // id админа

    $image_path = null; // путь к изображению
    $errors = [];


    if (isset($_FILES["image"])) {
        $uploadDir = 'upload/';

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir);
        }

        $image_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 1024 * 1024 * 10; // 10мб

        $image = $_FILES["image"];

        $fileName = $image['name'];
        $fileError = $image['error'];
        $tmpName = $image["tmp_name"];
        $size = $image['size'];
        $type = $image["type"];

        // Проверка ошибки загрузки
        if ($fileError !== UPLOAD_ERR_OK) {
            $errors[] =  "Ошибка загрузки файла. Код ошибка: $fileError";
        }

        // Проверяем тип файла
        if (!in_array($type, $image_types)) {
            $errors[] = "$type - недопустимый формат";
        }

        if ($size > $maxSize) {
            $errors[] = "Файл слишком большой (макс. 10Мб)";
        }

        // Создаем безопасное имя файла
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); // i.PNG => i.png
        $newName = uniqid() . "." . $extension; // avatar.png =>  53d352636.png
        $targetPath = $uploadDir . $newName; //   upload/53d352636.png 

        if (move_uploaded_file($tmpName, $targetPath)) {
            $image_path = $targetPath;
        } else {
            $errors[] = "Ошибка при сохранение";
        }
    } else {
        $errors[] = "Файл не выбран";
    }

    if (empty($name)) {
        $errors[] = "Название товара обязательно для заполнения";
    }

    if (empty($price)) {
        $errors[] = "Цена товара обязательно для заполнения";
    } else if (!is_numeric($price) && $price < 0) {
        $errors[] = "Некорректно введена цена товара";
    }

    if (empty($errors)) {
        try {
            $query = $pdo->prepare("INSERT INTO products
            (product_name, product_description, product_price, product_image_path, adminId)
            VALUES (?, ?, ?, ?, ?)
            ");
            $query->execute([$name, $description, $price, $image_path, $admin_id]);
            $success = "Товар $name успешно добавлен";

            header('Location: admin.php');
            exit();
        } catch (PDOException $e) {
            $errors[] = "Ошибка: " . $e->getMessage();

            if (file_exists($image_path)) {
                unlink($image_path);
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
    <link rel="stylesheet" href="/css/styles.css">
    <title>Document</title>
</head>

<body>
    <h1>Администратор</h1>
    <a href="../logout.php">Выйти из системы</a>

    <div class="product-container">
        <?php if (!empty($errors)): ?>
            <h2>Ошибки:</h2>
            <ul class="errors-list">
                <?php
                foreach ($errors as $e) {
                    echo "<li>" . $e . "</li>";
                }
                ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <h1><?php echo $success ?></h1>
            <?php
            $success = "";
            ?>
        <?php endif; ?>


        <h1>Добавить новый товар</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <p>Название товара:</p>
                <input type="text" name="name" required">
            </div>

            <div class="form-group">
                <p>Описание товара:</p>
                <textarea name="description"></textarea>
            </div>

            <div class="form-group">
                <p>Цена товара:</p>
                <input type="number" name="price" required">
            </div>

            <div class="form-group">
                <p>Изображение товара:</p>
                <input type="file" name="image" required">
                <br>
                <i>Разрешены: JPG, PNG, GIF, WEBP (макс. 10МБ)</i>
            </div>

            <button>Добавить товар</button>
        </form>
        <ul class="nav_list">
            <?php if (!empty($products_admin)): ?>
                <?php foreach ($products_admin as $product): ?>
                    <li class="product_item">
                        <img src="<?php echo $product['product_image_path'] ?>" alt="image" width="150">
                        <h3><?php echo ($product['product_name']) ?></h3>
                        <p><?php echo ($product['product_description']) ?></p>
                        <span class="price">Цена: <?php echo $product['product_price'] ?> руб.</span>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Товаров пока нет.</p>
            <?php endif; ?>
        </ul>
    </div>
</body>

</html>