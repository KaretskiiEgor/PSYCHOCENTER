<?php
header('Content-Type: application/json; charset=utf-8');

// Параметры подключения (те же, что в articles.php)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "HarmonyDB";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Ошибка подключения к базе данных']);
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'Неверный ID статьи']);
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT Title, Summary, Content FROM Articles WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['error' => 'Статья не найдена']);
    exit;
}

$article = $res->fetch_assoc();

// Кодируем текст как есть, чтобы JavaScript мог заменить \n на <br>
echo json_encode($article, JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();
