<?php
define('CATALOG_FILE', __DIR__ . '/Catalog.php');
define('PER_PAGE', 5);

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function price_with_discount($price, $discount)
{
    return round($price - $price * ($discount / 100), 2);
}

function stock_label($stock)
{
    return match (true) {
        $stock === 0 => 'Нет в наличии',
        $stock <= 5 => 'Мало',
        default => 'В наличии',
    };
}

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
        ['id' => 8, 'title' => 'Rofl', 'category' => 'PHPHPHPHPHPHP', 'price' => 999999, 'stock' => 1, 'discount' => 0],
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

function catalog_stats($catalog)
{
    $catalogLength = count($catalog);
    $totalStock = 0;
    $totalStockValue = 0;

    foreach ($catalog as $product) {
        $totalStock += $product['stock'];
        $totalStockValue += price_with_discount($product['price'], $product['discount']) * $product['stock'];
    }

    return [
        'products' => $catalogLength,
        'stock' => $totalStock,
        'value' => $totalStockValue,
    ];
}

function find_product($catalog, $id)
{
    foreach ($catalog as $product) {
        if ((int)$product['id'] === $id) {
            return $product;
        }
    }
    return null;
}

function next_id($catalog)
{
    $maxId = 0;

    foreach ($catalog as $product) {
        if ($product['id'] > $maxId) {
            $maxId = $product['id'];
        }
    }

    return $maxId + 1;
}

function make_product($data)
{
    $price = str_replace(',', '.', trim($data['price']));

    return [
        'title' => trim($data['title']),
        'category' => trim($data['category']),
        'price' => $price + 0,
        'stock' => (int)$data['stock'],
        'discount' => (int)$data['discount'],
    ];
}

function add_product($catalog, $data)
{
    $newProduct = ['id' => next_id($catalog)] + make_product($data);
    $catalog[] = $newProduct;
    return $catalog;
}

function update_product($catalog, $id, $data)
{
    $catalogLength = count($catalog);

    for ($i = 0; $i < $catalogLength; $i++) {
        if ((int)$catalog[$i]['id'] === $id) {
            $catalog[$i] = ['id' => $catalog[$i]['id']] + make_product($data);
        }
    }

    return $catalog;
}

function delete_product($catalog, $id)
{
    $ret = [];

    foreach ($catalog as $product) {
        if ((int)$product['id'] !== $id) {
            $ret[] = $product;
        }
    }

    return $ret;
}

function unique_categories($catalog)
{
    $ret = [];

    foreach ($catalog as $product) {
        if (!in_array($product['category'], $ret)) {
            $ret[] = $product['category'];
        }
    }

    return $ret;
}

function search_by_name($catalog, $query)
{
    $search = mb_strtolower(trim($query));
    $ret = [];

    foreach ($catalog as $product) {
        if ($search === '' || str_contains(mb_strtolower($product['title']), $search)) {
            $ret[] = $product;
        }
    }

    return $ret;
}

function filter_by_category($catalog, $category)
{
    $ret = [];

    foreach ($catalog as $product) {
        if ($category === '' || $category === 'all' || $product['category'] === $category) {
            $ret[] = $product;
        }
    }

    return $ret;
}

function sort_by_price($catalog, $direction = 'asc')
{
    $ret = $catalog;
    if ($direction == 'asc') {
        usort($ret, function ($a, $b) {
            return $a['price'] <=> $b['price'];
        });
    } else {
        usort($ret, function ($a, $b) {
            return $b['price'] <=> $a['price'];
        });
    }
    return $ret;
}

function sort_by_title($catalog, $direction = 'asc')
{
    $ret = $catalog;
    if ($direction == 'asc') {
        usort($ret, function ($a, $b) {
            return mb_strtolower($a['title']) <=> mb_strtolower($b['title']);
        });
    } else {
        usort($ret, function ($a, $b) {
            return mb_strtolower($b['title']) <=> mb_strtolower($a['title']);
        });
    }
    return $ret;
}

function sort_by_stock($catalog, $direction = 'asc')
{
    $ret = $catalog;
    if ($direction == 'asc') {
        usort($ret, function ($a, $b) {
            return $a['stock'] <=> $b['stock'];
        });
    } else {
        usort($ret, function ($a, $b) {
            return $b['stock'] <=> $a['stock'];
        });
    }
    return $ret;
}

function paginate($items, $page, $perPage)
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
        'totalPages' => $totalPages,
    ];
}

function validate_product($data)
{
    $errors = [];

    $title = isset($data['title']) ? trim($data['title']) : '';
    if ($title === '') {
        $errors['title'] = 'Введите название';
    } elseif (mb_strlen($title) < 3) {
        $errors['title'] = 'Название не короче 3 символов';
    }

    $category = isset($data['category']) ? trim($data['category']) : '';
    if ($category === '') {
        $errors['category'] = 'Выберите категорию';
    }

    $priceText = isset($data['price']) ? str_replace(',', '.', trim($data['price'])) : '';
    if (!is_numeric($priceText)) {
        $errors['price'] = 'Цена должна быть числом';
    } elseif ($priceText <= 0) {
        $errors['price'] = 'Цена должна быть больше нуля';
    }

    if (isset($data['stock'])) {
        $stockText = trim($data['stock']);
        if (!is_numeric($stockText)) {
            $errors['stock'] = 'Остаток должен быть числом';
        } elseif ($stockText < 0) {
            $errors['stock'] = 'Остаток не может быть меньше нуля';
        }
    }

    if (isset($data['discount'])) {
        $discountText = trim($data['discount']);
        if (!is_numeric($discountText)) {
            $errors['discount'] = 'Скидка должна быть числом';
        } elseif ($discountText < 0 || $discountText > 100) {
            $errors['discount'] = 'Скидка от 0 до 100';
        }
    }

    return $errors;
}

function query_url($params)
{
    $all = array_merge($_GET, $params);
    unset($all['added'], $all['updated'], $all['deleted']);
    return 'index.php?' . http_build_query($all);
}