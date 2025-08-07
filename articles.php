<?php
// Параметры подключения к MySQL
$servername = "localhost";
$username = "root";
$password = ""; // Заменить на свой пароль
$dbname = "HarmonyDB";

// Создаем подключение
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Проверяем, существует ли поле CreatedAt или Created_At
$createdAtField = null;
$resultCheck = $conn->query("SHOW COLUMNS FROM Articles LIKE 'CreatedAt'");
if ($resultCheck && $resultCheck->num_rows > 0) {
    $createdAtField = "CreatedAt";
} else {
    $resultCheck2 = $conn->query("SHOW COLUMNS FROM Articles LIKE 'Created_At'");
    if ($resultCheck2 && $resultCheck2->num_rows > 0) {
        $createdAtField = "Created_At";
    }
}

// Формируем SQL-запрос с учетом наличия поля даты
$sql = "SELECT id, Title, Summary FROM Articles";
if ($createdAtField) {
    $sql .= " ORDER BY $createdAtField DESC";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Статьи — Гармония</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
  <style>
    /* CSS без изменений + добавлен стиль модалки */
    body {
      font-family: 'Roboto', sans-serif;
      margin: 0;
      padding: 0;
      background: #f7f7f7;
      color: #333;
      padding-bottom: 60px;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    header {
      background: #4a90e2;
      color: white;
      padding: 20px 0;
      text-align: center;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    header h1 {
      margin: 0;
      font-size: 28px;
    }
    nav {
      margin-top: 20px;
    }
    nav ul {
      list-style: none;
      padding: 0;
      display: flex;
      justify-content: center;
      gap: 15px;
      flex-wrap: wrap;
    }
    nav li a {
      text-decoration: none;
      padding: 10px 20px;
      background-color: white;
      color: #4a90e2;
      border-radius: 5px;
      border: 2px solid white;
      font-weight: bold;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    nav li a:hover {
      background-color: #e1ecf7;
      border-color: #e1ecf7;
    }
    main {
      padding: 40px 36px;
      flex: 1;
    }
    h2 {
      text-align: center;
      color: #4a90e2;
      font-size: 32px;
      margin-bottom: 30px;
    }
    .content {
      max-width: 800px;
      margin: 0 auto;
    }
    .article-card {
      background: #ffffff;
      border: 1px solid #d0dbe9;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
      transition: box-shadow 0.3s ease;
    }
    .article-card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .article-card h3 {
      margin-top: 0;
      color: #4a90e2;
    }
    .article-card p {
      margin-bottom: 10px;
    }
    .article-card a {
      text-decoration: none;
      color: #4a90e2;
      font-weight: bold;
      cursor: pointer;
    }
    .add-button {
      display: inline-block;
      background-color: #4a90e2;
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.3s ease;
      margin-bottom: 30px;
    }
    .add-button:hover {
      background-color: #357ABD;
    }
    footer {
      background: #333;
      color: white;
      text-align: center;
      padding: 15px 0;
      position: fixed;
      bottom: 0;
      width: 100%;
    }

    /* Модальное окно */
    #modal {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.6);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      padding: 20px;
    }
    #modal.active {
      display: flex;
    }
    #modal .modal-content {
      background: white;
      border-radius: 8px;
      max-width: 800px;
      width: 100%;
      max-height: 80vh;
      overflow-y: auto;
      padding: 25px 30px;
      position: relative;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    #modal .modal-content h2 {
      color: #4a90e2;
      margin-top: 0;
      margin-bottom: 15px;
    }
    #modal .modal-content p.summary {
      font-style: italic;
      margin-bottom: 20px;
      color: #666;
    }
    #modal .modal-content .content {
      white-space: pre-wrap;
      line-height: 1.6;
      color: #333;
    }
    #modal .close-btn {
      position: absolute;
      top: 10px; right: 10px;
      background: #4a90e2;
      color: white;
      border: none;
      border-radius: 50%;
      width: 30px;
      height: 30px;
      font-size: 20px;
      line-height: 28px;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s ease;
    }
    #modal .close-btn:hover {
      background: #357ABD;
    }
  </style>
</head>
<body>

<header>
  <h1>Психологический центр</h1>
  <nav>
    <ul>
      <li><a href="index.html">Главная</a></li>
      <li><a href="about.html">О нас</a></li>
      <li><a href="services.html">Услуги</a></li>
      <li><a href="specialists.html">Специалисты</a></li>
      <li><a href="online-tests.html">Онлайн-тесты</a></li>
      <li><a href="faq.html">Вопросы и ответы</a></li>
      <li><a href="articles.php">Статьи</a></li>
      <li><a href="contact.html">Контакты</a></li>
    </ul>
  </nav>
</header>

<main>
  <h2>Статьи</h2>
  
  <div style="text-align: center;">
    <a href="add-article.php" class="add-button" style="display: none;">+ Добавить статью</a>
  </div>

  <div class="content">
    <?php
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<div class="article-card">';
            echo '<h3>' . htmlspecialchars($row['Title']) . '</h3>';
            echo '<p>' . htmlspecialchars($row['Summary']) . '</p>';
            // Кнопка для открытия модалки с data-id
            echo '<a href="#" class="open-article" data-id="' . $row['id'] . '">Читать статью →</a>';
            echo '</div>';
        }
    } else {
        echo '<p>Статей пока нет.</p>';
    }
    ?>
  </div>
</main>

<footer>
  <p>&copy; 2025 Психологический центр. Все права защищены.</p>
</footer>

<!-- Модальное окно -->
<div id="modal">
  <div class="modal-content">
    <button class="close-btn" title="Закрыть">&times;</button>
    <h2 id="modal-title"></h2>
    <p class="summary" id="modal-summary"></p>
    <div class="content" id="modal-content"></div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal');
    const modalTitle = document.getElementById('modal-title');
    const modalSummary = document.getElementById('modal-summary');
    const modalContent = document.getElementById('modal-content');
    const closeBtn = modal.querySelector('.close-btn');

    // Закрыть модалку
    closeBtn.addEventListener('click', () => {
      modal.classList.remove('active');
      modalTitle.textContent = '';
      modalSummary.textContent = '';
      modalContent.textContent = '';
    });

    // Закрыть по клику вне модального контента
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        closeBtn.click();
      }
    });

    // Обработчик клика по ссылкам "Читать статью"
    document.querySelectorAll('.open-article').forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const articleId = e.currentTarget.getAttribute('data-id');
        if (!articleId) return;

        // Запрос к серверу для получения статьи
        fetch(`get-article.php?id=${encodeURIComponent(articleId)}`)
          .then(res => {
            if (!res.ok) throw new Error('Ошибка сети');
            return res.json();
          })
          .then(data => {
            if (data.error) {
              alert(data.error);
              return;
            }
            // Заполняем модальное окно
            modalTitle.textContent = data.Title;
            modalSummary.textContent = data.Summary;
            modalContent.innerHTML = data.Content.replace(/\n/g, "<br>");
            modal.classList.add('active');
          })
          .catch(err => {
            alert('Не удалось загрузить статью.');
            console.error(err);
          });
      });
    });
  });
</script>

</body>
</html>

<?php $conn->close(); ?>
