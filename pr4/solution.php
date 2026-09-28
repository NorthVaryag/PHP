<?php
$catalog = [
        [
                'title' => 'Grand Theft Auto VI',
                'category' => 'Game',
                'price' => 9000,
                'stock' => 15,
                'discount' => 0
        ],
        [
                'title' => 'Grand Theft Auto VI Full Edition',
                'category' => 'Game',
                'price' => 15000,
                'stock' => 25,
                'discount' => 5
        ],
        [
                'title' => 'The Witcher IV',
                'category' => 'Game',
                'price' => 6500,
                'stock' => 50,
                'discount' => 15
        ],
        [
                'title' => 'Grdariki',
                'category' => 'Game',
                'price' => 5800,
                'stock' => 36,
                'discount' => 25
        ],
        [
                'title' => 'Steam Deck',
                'category' => 'Console',
                'price' => 58000,
                'stock' => 12,
                'discount' => 0
        ],
        [
                'title' => 'PlayStation',
                'category' => 'Console',
                'price' => 65000,
                'stock' => 8,
                'discount' => 3
        ],
        [
                'title' => 'Apple',
                'category' => 'Parte',
                'price' => 9999999999,
                'stock' => 1,
                'discount' => 0
        ],
        [
                'title' => 'Rofl',
                'category' => 'PHPHPHPHPHPHP',
                'price' => 999999,
                'stock' => 1,
                'discount' => 0
        ],
        [
                'title'    => 'Ноутбук',
                'category' => 'Техника',
                'price'    => 54990,
                'stock'    => 12,
                'discount' => 10,
        ],
        [
                'title'    => 'Смартфон',
                'category' => 'Техника',
                'price'    => 21990,
                'stock'    => 5,
                'discount' => 5,
        ],
        [
                'title'    => 'Беспроводные наушники',
                'category' => 'Аксессуары',
                'price'    => 4990,
                'stock'    => 0,
                'discount' => 0,
        ],
        [
                'title'    => 'Игровая консоль',
                'category' => 'Игры',
                'price'    => 45000,
                'stock'    => 3,
                'discount' => 15,
        ],
        [
                'title'    => 'Механическая клавиатура',
                'category' => 'Аксессуары',
                'price'    => 7500,
                'stock'    => 8,
                'discount' => 0,
        ],
        [
                'title'    => 'Компьютерная мышь',
                'category' => 'Аксессуары',
                'price'    => 2500,
                'stock'    => 1,
                'discount' => 20,
        ]
];


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог товаров</title>
</head>
<body>
<form method="GET">
    <input type="text" name="query" placeholder="Search" value="<?= htmlspecialchars($_GET['query'] ?? '') ?>">
    <button type="submit">Enter</button>
</form>

<?php
$searchQuery = trim($_GET['query'] ?? '');

foreach ($catalog as $product) {
    if ($searchQuery === '' || str_contains(strtolower($product['title']), strtolower($searchQuery))) {
        echo '<div>' . htmlspecialchars($product['title']) . ' - ' . $product['price'] . ' руб.</div>';
    }
}
?>
</body>
</html>