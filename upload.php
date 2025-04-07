<?php
$allowedTypes = ["image/jpeg", "image/png", "image/gif"];  // Разрешённые типы
$maxSize = 2 * 1024 * 1024;  // 2 МБ

if (!in_array($_FILES["image"]["type"], $allowedTypes)) {
    echo "Ошибка: можно загружать только JPG, PNG, GIF!";
    exit;
}

if ($_FILES["image"]["size"] > $maxSize) {
    echo "Ошибка: максимальный размер файла 2 МБ!";
    exit;
}







// if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
//     $uploadDir = "uploads/";  // Папка для сохранения
//     $uploadFile = $uploadDir . basename($_FILES["image"]["name"]);  // Полный путь

//     if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploadFile)) {
//         echo "Файл успешно загружен: <a href='$uploadFile'>$uploadFile</a>";
//     } else {
//         echo "Ошибка загрузки файла!";
//     }
// }






if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $price = floatval($_POST["price"]);
    $description = nl2br(htmlspecialchars(trim($_POST["comment"])));

    // Создаём уникальную папку для товара
    $folder = preg_replace("/[^a-zA-Z0-9]/", "-", strtolower($name));
    $path = "products/$folder";

    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }

    // Сохраняем изображение
    $imagePath = "$path/image.jpg";
    move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);

    // Создаём JSON-данные для товара
    $productData = [
        "name" => $name,
        "price" => $price,
        "description" => $description,
        "folder" => $folder
    ];

    // Записываем JSON-файл в папку товара
    file_put_contents("$path/data.json", json_encode($productData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // Обновляем общий JSON-файл со всеми товарами
    $allProducts = [];
    if (file_exists("products.json")) {
        $allProducts = json_decode(file_get_contents("products.json"), true);
    }
    $allProducts[] = $productData;
    file_put_contents("products.json", json_encode($allProducts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));


    echo "<p>Данные отправленны успешно! Товар будет добавлен.</p>";
    header("Refresh: 5; URL=index.html");

    // Перенаправляем обратно на главную страницу 2
    // sleep(3)
    // header("Location: index.html");
    exit();
}
?>