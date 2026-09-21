<?php

// Наш каталог товаров
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
        'title' => 'Ноутбук',
        'category' => 'Техника',
        'price' => 54990,
        'stock' => 12,
        'discount' => 10,
    ],
    [
        'title' => 'Смартфон',
        'category' => 'Техника',
        'price' => 21990,
        'stock' => 5,
        'discount' => 5,
    ],
    [
        'title' => 'Беспроводные наушники',
        'category' => 'Аксессуары',
        'price' => 4990,
        'stock' => 0,
        'discount' => 0,
    ],
    [
        'title' => 'Механическая клавиатура',
        'category' => 'Аксессуары',
        'price' => 7500,
        'stock' => 8,
        'discount' => 0,
    ],
    [
        'title' => 'Компьютерная мышь',
        'category' => 'Аксессуары',
        'price' => 2500,
        'stock' => 1,
        'discount' => 20,
    ]
];

/**
1. Фильтрация по категории
 */
function filter_by_category(array $catalog, ?string $category): array
{
    if ($category === null || $category === '' || $category === 'all') {
        return $catalog;
    }

    return array_values(array_filter($catalog, function ($item) use ($category) {
        return isset($item['category']) && $item['category'] === $category;
    }));
}

/**
2. Фильтрация по цене [min, max]
 */
function filter_by_price(array $catalog, float $min = 0.0, ?float $max = null): array
{
    return array_values(array_filter($catalog, function ($item) use ($min, $max) {
        $price = $item['price'] ?? 0.0;

        if ($price < $min) {
            return false;
        }

        if ($max !== null && $price > $max) {
            return false;
        }

        return true;
    }));
}

/**
3. Поиск по названию без учёта регистра
 */
function search_by_name(array $catalog, string $query): array
{
    $query = trim($query);
    if ($query === '') {
        return $catalog;
    }

    $lowerQuery = strtolower($query);

    return array_values(array_filter($catalog, function ($item) use ($lowerQuery) {
        $title = $item['title'] ?? '';
        return str_contains(strtolower($title), $lowerQuery);
    }));
}

/**
4. Сортировка по цене
 */
function sort_by_price(array $catalog, string $direction = 'asc'): array
{
    $items = $catalog; // Копируем, чтобы не портить исходный массив

    usort($items, function ($a, $b) use ($direction) {
        $priceA = $a['price'] ?? 0.0;
        $priceB = $b['price'] ?? 0.0;

        return $direction === 'desc'
            ? $priceB <=> $priceA
            : $priceA <=> $priceB;
    });

    return $items;
}

/**
5. Сортировка по названию
 */
function sort_by_title(array $catalog, string $direction = 'asc'): array
{
    $items = $catalog;

    usort($items, function ($a, $b) use ($direction) {
        $titleA = strtolower($a['title'] ?? '');
        $titleB = strtolower($b['title'] ?? '');

        $cmp = $titleA <=> $titleB;

        return $direction === 'desc' ? -$cmp : $cmp;
    });

    return $items;
}

/**
6. Пагинация (разбивка на страницы)
 */
function paginate(array $items, int $page, int $perPage): array
{
    $total = count($items);
    $perPage = max(1, $perPage);

    $totalPages = (int) ceil($total / $perPage);
    if ($totalPages < 1) {
        $totalPages = 1;
    }

    // Защита: зажимаем страницу в допустимые рамки
    if ($page < 1) {
        $page = 1;
    } elseif ($page > $totalPages) {
        $page = $totalPages;
    }

    $offset = ($page - 1) * $perPage;
    $pagedItems = array_values(array_slice($items, $offset, $perPage));

    return [
        'items' => $pagedItems,
        'total' => $total,
        'page' => $page,
        'totalPages' => $totalPages,
    ];
}

/**
7. Получение уникальных категорий
 */
function unique_categories(array $catalog): array
{
    $categories = [];
    foreach ($catalog as $item) {
        if (isset($item['category'])) {
            $categories[$item['category']] = true;
        }
    }

    return array_keys($categories);
}

// --- Демонстрация работы с красивым выводом ---
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    echo "<h1>Демонстрация работы каталога</h1>";

    echo "<h3>1. Уникальные категории:</h3>";
    echo "<pre>";
    print_r(unique_categories($catalog));
    echo "</pre>";

    echo "<h3>2. Товары в категории «Аксессуары»:</h3>";
    echo "<pre>";
    print_r(filter_by_category($catalog, 'Аксессуары'));
    echo "</pre>";

    echo "<h3>3. Поиск по запросу «Grand»:</h3>";
    echo "<pre>";
    print_r(search_by_name($catalog, 'Grand'));
    echo "</pre>";

    echo "<h3>4. Пагинация (Страница 1, по 3 товара на странице):</h3>";
    $paginationResult = paginate($catalog, 1, 3);
    echo "Текущая страница: {$paginationResult['page']} из {$paginationResult['totalPages']} (Всего товаров: {$paginationResult['total']})<br>";
    echo "<pre>";
    print_r($paginationResult['items']);
    echo "</pre>";
}