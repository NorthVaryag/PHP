<?php
define('CATALOG_FILE', __DIR__ . '/catalog.json');

function default_catalog(): array
{
    return [
        ['id' => 1, 'title' => 'Grand Theft Auto VI', 'category' => 'Game', 'price' => 9000, 'stock' => 15, 'discount' => 0],
        ['id' => 2, 'title' => 'Grand Theft Auto VI Full Edition', 'category' => 'Game', 'price' => 15000, 'stock' => 25, 'discount' => 5],
        ['id' => 3, 'title' => 'The Witcher IV', 'category' => 'Game', 'price' => 6500, 'stock' => 50, 'discount' => 15],
        ['id' => 4, 'title' => 'Grdariki', 'category' => 'Game', 'price' => 5800, 'stock' => 36, 'discount' => 25],
        ['id' => 5, 'title' => 'Steam Deck', 'category' => 'Console', 'price' => 58000, 'stock' => 12, 'discount' => 0],
        ['id' => 6, 'title' => 'PlayStation', 'category' => 'Console', 'price' => 65000, 'stock' => 8, 'discount' => 3],
        ['id' => 7, 'title' => 'Apple', 'category' => 'Parte', 'price' => 9999999999, 'stock' => 1, 'discount' => 0],
        ['id' => 8, 'title' => 'Rofl', 'category' => 'PHPHPHPHPHPHP', 'price' => 999999, 'stock' => 1, 'discount' => 0]
    ];
}

function save_catalog(array $catalog, string $file = CATALOG_FILE): bool
{
    $json = json_encode($catalog, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $result = @file_put_contents($file, $json, LOCK_EX);
    if ($result === false) {
        return false;
    }
    return true;
}

function load_catalog(string $file = CATALOG_FILE): array
{
    if (is_file($file) && is_readable($file)) {
        $json = file_get_contents($file);
        if ($json !== false && json_validate($json)) {
            $catalog = json_decode($json, true);
            if (is_array($catalog) && array_is_list($catalog)) {
                return $catalog;
            }
        }
        rename($file, $file . '.broken.' . date('YmdHis'));
    }

    $catalog = default_catalog();
    save_catalog($catalog, $file);
    return $catalog;
}

function catalog_stats(array $catalog): array
{
    $catalogLength = count($catalog);
    $totalStock = 0;
    $totalStockValue = 0;

    foreach ($catalog as $product) {
        $totalStock += $product['stock'];
        $totalStockValue += $product['price'] * $product['stock'];
    }

    return [
        'products' => $catalogLength,
        'stock' => $totalStock,
        'value' => $totalStockValue,
    ];
}

function next_id(array $catalog): int
{
    $maxId = 0;

    foreach ($catalog as $product) {
        if ($product['id'] > $maxId) {
            $maxId = $product['id'];
        }
    }

    return $maxId + 1;
}