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
$choice = isset($_GET['city_choice']) ? $_GET['city_choice'] : '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Работа 2. Массивы и функции — теория</title>
</head>
<body>
<label for="city">Сортировка:</label>
<form method="get">
    <select id="city" name="city_choice">
        <option value="" <?= $choice == '' ? 'selected' : '' ?>>Сделайте выбор</option>
        <option value="by_category" <?= $choice == 'by_category' ? 'selected' : '' ?>>По категории</option>
        <option value="by_cost" <?= $choice == 'by_cost' ? 'selected' : '' ?>>По цене</option>
        <option value="by_name" <?= $choice == 'by_name' ? 'selected' : '' ?>>По названнию</option>
    </select>
    <button type="submit">Показать</button>
</form>
<?php
function price_with_discount(int $price, int $discount): float
{
    $finalPrice = $price * (100 - $discount) / 100;
    return round($finalPrice, 2);
}

function stock_label(int $stock): string
{
    return match (true) {
        $stock === 0 => 'Нет в наличии',
        $stock <= 5 => 'Мало',
        default => 'В наличии',
    };
}

function sort_by_price(array $catalog, string $direction = 'asc'): array
{
    $sorted = $catalog;
    if ($direction === 'asc') {
        usort($sorted, function ($a, $b) {
            return $a['price'] <=> $b['price'];
        });
    } else {
        usort($sorted, function ($a, $b) {
            return $b['price'] <=> $a['price'];
        });
    }
    return $sorted;
}

function sort_by_title(array $catalog, string $direction = 'asc'): array
{
    $sorted = $catalog;
    if ($direction === 'asc') {
        usort($sorted, function ($a, $b) {
            return mb_strtolower($a['title']) <=> mb_strtolower($b['title']);
        });
    } else {
        usort($sorted, function ($a, $b) {
            return mb_strtolower($b['title']) <=> mb_strtolower($a['title']);
        });
    }
    return $sorted;
}

function sort_by_category(array $catalog): array
{
    $sorted = $catalog;
    usort($sorted, function ($a, $b) {
        return mb_strtolower($a['category']) <=> mb_strtolower($b['category']);
    });
    return $sorted;
}

function filter_by_category(array $catalog, ?string $category): array
{
    if ($category == null or $category == '' or $category == 'all') {
        return $catalog;
    }
    return array_values(array_filter($catalog, function ($item) use ($category) {
        return mb_strtolower($item['category']) == mb_strtolower($category);
    }));
}

function filter_by_price(array $catalog, float $min = 0.0, ?float $max = null): array
{
    return array_values(array_filter($catalog, function ($item) use ($min, $max) {
        return $item['price'] >= $min && ($max != null ? $item['price'] <= $max : true);
    }));
}

function search_by_name(array $catalog, string $query): array
{
    $ret = [];
    if (trim($query) == '') {
        return $catalog;
    }
    foreach ($catalog as $product) {
        if (str_contains(mb_strtolower($product['title']), mb_strtolower(trim($query)))) {
            $ret[] = $product;
        }
    }
    return $ret;
}

function paginate(array $items, int $page, int $perPage): array
{
    $totalPages = (int)ceil(count($items) / $perPage);
    if ($totalPages < 1) {
        $totalPages = 1;
    }
    if ($page < 1) {
        $page = 1;
    }
    if ($page > $totalPages) {
        $page = $totalPages;
    }
    return [
            'items' => array_slice($items, $perPage * ($page - 1), $perPage),
            'total' => count($items),
            'page' => $page,
            'totalPages' => $totalPages
    ];
}

function unique_categories(array $catalog): array
{
    $ret = [];
    foreach ($catalog as $item) {
        if (!in_array($item['category'], $ret)) {
            $ret[] = $item['category'];
        }
    }
    return $ret;
}

function render_catalog(array $catalog, ?array $fullCatalog = null, int $startNumber = 1): string
{
    if ($fullCatalog === null) {
        $fullCatalog = $catalog;
    }
    $output = "";
    $output .= "<br>=== Каталог товаров ===<br>";
    $catalogLength = count($catalog);

    for ($i = 0; $i < $catalogLength; $i++) {
        $product = $catalog[$i];

        $priceFormatted = number_format($product['price'], 0, ',', ' ');
        $discountFormatted = number_format($product['discount'], 0, ',', ' ');
        $discountPrice = price_with_discount($product['price'], $product['discount']);
        $discountPriceFormatted = number_format($discountPrice, 2, ',', ' ');
        $status = stock_label($product['stock']);

        $output .= sprintf(
                "%d. %s [%s] — %s руб. (Скидка: %s%%, Цена со скидкой: %s руб.) | Остаток: %d (%s)<br>",
                $startNumber + $i,
                $product['title'],
                $product['category'],
                $priceFormatted,
                $discountFormatted,
                $discountPriceFormatted,
                $product['stock'],
                $status
        );
    }

    $totalStockValue = 0.0;
    $discountedCount = 0;

    foreach ($fullCatalog as $product) {
        $discountPrice = price_with_discount($product['price'], $product['discount']);
        $totalStockValue += $discountPrice * $product['stock'];

        if ($product['discount'] > 0) {
            $discountedCount++;
        }
    }

    $totalStockValueFormatted = number_format(round($totalStockValue, 2), 2, ',', ' ');

    $output .= "<br>=== Итоги по складу ===<br>";
    $output .= "Итоговая стоимость склада: {$totalStockValueFormatted} руб.<br>";
    $output .= "Количество товаров со скидкой: {$discountedCount}<br>";

    return $output;
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    if ($choice === 'by_cost') {
        $catalog = sort_by_price($catalog);
    } elseif ($choice === 'by_name') {
        $catalog = sort_by_title($catalog);
    } elseif ($choice === 'by_category') {
        $catalog = sort_by_category($catalog);
    }
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $pageData = paginate($catalog, $page, 5);

    echo render_catalog($pageData['items'], $catalog, ($pageData['page'] - 1) * 5 + 1);

    echo '<br>Страницы: ';
    for ($i = 1; $i <= $pageData['totalPages']; $i++) {
        if ($i == $pageData['page']) {
            echo '<b>' . $i . '</b> ';
        } else {
            echo '<a href="?city_choice=' . urlencode($choice) . '&page=' . $i . '">' . $i . '</a> ';
        }
    }
    echo '<br>';

    echo 'Категории: ' . implode(', ', unique_categories($catalog)) . '<br>';
    echo 'Цена от 3000 до 30000: ' . count(filter_by_price($catalog, 3000, 30000)) . ' шт.<br>';

    $all = $catalog;
    echo '<br>Демонстрация функций<br>';
    echo 'filter_by_category (Техника): ' . count(filter_by_category($all, 'Техника')) . ' шт.<br>';
    echo 'search_by_name (приставка): ' . count(search_by_name($all, 'приставка')) . ' шт.<br>';
    echo 'sort_by_price (дешёвый): ' . sort_by_price($all)[0]['title'] . '<br>';
    echo 'sort_by_price desc (дорогой): ' . sort_by_price($all, 'desc')[0]['title'] . '<br>';
    echo 'sort_by_title (первый): ' . sort_by_title($all)[0]['title'] . '<br>';
    $demo = paginate($all, 2, 4);
    echo 'paginate (стр. 2 по 4): страница ' . $demo['page'] . ' из ' . $demo['totalPages'] . ', товаров ' . count($demo['items']) . ', всего ' . $demo['total'] . '<br>';
}
?>
</body>
</html>