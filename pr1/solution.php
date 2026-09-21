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

function price_with_discount(int $price, int $discount): float
{
    $finalPrice = $price * (100 - $discount) / 100;
    return round($finalPrice, 2);
}

function stock_label(int $stock): string
{
    return match (true) {
        $stock === 0 => 'Нет в наличии',
        $stock <= 5  => 'Мало',
        default      => 'В наличии',
    };
}

function render_catalog(array $catalog): string
{
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
            $i + 1,
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

    foreach ($catalog as $product) {
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
    echo render_catalog($catalog);
}