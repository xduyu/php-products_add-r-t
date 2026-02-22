<?php
require('./config.php');
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ./pages/login.php');
}
$products = [];
try {
    $q = $pdo->prepare('SELECT * FROM products ORDER BY product_id DESC');
    $q->execute();
    $products = $q->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $pe) {
    echo $pe->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <header class="header">
        <nav class="nav w-full ">
            <a href="" class="web_shop">Web Shop</a>
            <?php
            if (isset($_SESSION['user_id'])) {
                echo "welcome";
            }
            ?>
            <?php
            if ($_SESSION['user_roleid'] == 2) {
                header('Location: ./admin.php');
                exit();
            }
            ?>
            <a href="./php/logout.php">Выйти из системы</a>
            <ul class="nav_list flex gap-3 ">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <li class="product_item border gap-2 border-black flex rounded-lg flex-col p-3">
                            <img src="<?php echo $product['product_image_path'] ?>" alt="image" width="150">
                            <h3 class="text-xl font-bold"><?php echo ($product['product_name']) ?></h3>
                            <p class=""><?php echo ($product['product_description']) ?></p>
                            <span class="price">Цена: <?php echo $product['product_price'] ?> руб.</span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Товаров пока нет.</p>
                <?php endif; ?>
            </ul>

        </nav>
    </header>
</body>

</html>