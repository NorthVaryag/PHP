<?php
function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function unique_categories($catalog)
{
    return array_values(array_unique(array_column($catalog, 'category')));
}

function search_by_name($catalog, $search)
{
    $search = trim(strtolower($search));
    $catalogLength = count($catalog);
    $result = [];

    for ($i = 0; $i < $catalogLength; $i++) {
        $product = $catalog[$i];
        if ($search === '' || str_contains(strtolower($product['title']), $search)) {
            $result[] = $product;
        }
    }

    return $result;
}

function filter_by_category($catalog, $category)
{
    $result = [];

    foreach ($catalog as $product) {
        if ($category === '' ||  $category === 'all' ||  $product['category'] === $category) {
            $result[] = $product;
        }
    }

    return $result;
}

function validate_product($data)
{
    $errors = [];

    $title = isset($data['title']) ? trim($data['title']) : '';
    if ($title === '') {
        $errors['title'] = 'Введите название';
    } elseif (strlen($title) < 3) {
        $errors['title'] = 'Название не короче 3 символов';
    }

    $category = isset($data['category']) ? trim($data['category']) : '';
    if ($category === '') {
        $errors['category'] = 'Выберите категорию';
    }

    $price = isset($data['price']) ? str_replace(',', '.', trim($data['price'])) : '';
    if (!is_numeric($price)) {
        $errors['price'] = 'Цена должна быть числом';
    } elseif ($price <= 0) {
        $errors['price'] = 'Цена должна быть больше нуля';
    }

    if (isset($data['stock'])) {
        $stock = trim($data['stock']);
        if (!is_numeric($stock)) {
            $errors['stock'] = 'Остаток должен быть числом';
        } elseif ($stock < 0) {
            $errors['stock'] = 'Остаток не может быть меньше нуля';
        }
    }

    if (isset($data['discount'])) {
        $discount = trim($data['discount']);
        if (!is_numeric($discount)) {
            $errors['discount'] = 'Скидка должна быть числом';
        } elseif ($discount < 0 || $discount > 100) {
            $errors['discount'] = 'Скидка от 0 до 100';
        }
    }

    return $errors;
}

function sanitize_product($data)
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