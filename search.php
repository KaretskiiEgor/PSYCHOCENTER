<?php
$query = isset($_GET['query']) ? mb_strtolower(trim($_GET['query'])) : '';

$pages = [
  'index.html' => 'Главная',
  'about.html' => 'О нас',
  'services.html' => 'Услуги',
  'specialists.html' => 'Специалисты',
  'online-tests.html' => 'Онлайн-тесты',
  'faq.html' => 'Вопросы и ответы',
  'contact.html' => 'Контакты',
  'articles/relationships.html' => 'Психология отношений'
];

$results = [];

if ($query && strlen($query) >= 2) {
  foreach ($pages as $file => $title) {
    if (file_exists($file)) {
      $html = file_get_contents($file);
      $text = strip_tags($html);
      $textLower = mb_strtolower($text);

      if (mb_strpos($textLower, $query) !== false) {
        // Найдём фрагмент вокруг запроса
        $pos = mb_strpos($textLower, $query);
        $start = max(0, $pos - 60);
        $snippet = trim(mb_substr($text, $start, 160)) . '...';

        $results[] = [
          'title' => $title,
          'url' => $file,
          'snippet' => htmlspecialchars($snippet)
        ];
      }
    }
  }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($results);
