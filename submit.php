<?php
// Параметры подключения к MySQL
$servername = "localhost";
$username = "root";       // Ваш логин MySQL
$password = "";   // Ваш пароль MySQL
$dbname = "HarmonyDB";

// Создаем подключение
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверяем соединение
if ($conn->connect_error) {
    http_response_code(500);
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получаем данные из POST
$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';

// Проверяем на пустоту
if (empty($name) || empty($phone)) {
    http_response_code(400);
    echo "Пожалуйста, заполните все поля.";
    exit();
}

// Подготавливаем и выполняем запрос (используем подготовленное выражение для безопасности)
$stmt = $conn->prepare("INSERT INTO Appointment (name, phone, created_at) VALUES (?, ?, NOW())");
if ($stmt === false) {
    http_response_code(500);
    die("Ошибка подготовки запроса: " . $conn->error);
}

$stmt->bind_param("ss", $name, $phone);

if (!$stmt->execute()) {
    http_response_code(500);
    die("Ошибка выполнения запроса: " . $stmt->error);
} else {
    http_response_code(200);
    echo "Заявка успешно отправлена.";
}

$stmt->close();
$conn->close();
?>
