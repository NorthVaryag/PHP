<?php
function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function get_categories($products)
{
    return array_values(array_unique(array_column($products, 'category' )));
}

function filter_products($products, $search, $category)
{
    $search = trim(strtolower($search));
    $category = trim($category);

    $result = [];
    foreach ($products as $p)
    {
        $matchSearch = $search === ''|| strpos(strtolower($p['title']) ,$search) !== false;
        $matchCategory = $category === ''|| $category === 'all' || $p['category'] === $category;
        if ($matchCategory && $matchSearch)
        {
            $result[] = $p;
        }
    }
    return $result;
}
function validate_product($data)
{
    $errors = [];
    $name = isset($data['name']) ? trim($data['name']) : '';
    if ($name === '')
    {
        $errors['name'] = 'Name cant is null.';
    }
    elseif (mb_strlen($name) < 3)
    {
        $errors['name'] = 'Name can not shorter the 3 character.';
    }

    $category = isset($data['category']) ? trim($data['category']) : '';
    if ($category === '')
    {
        $errors['category'] = 'Select category';
    }

    $stock = isset($data['stock']) ? ($data['stock']) : '';
    if (!is_numeric($stock))
    {
        $errors['stock'] = 'Existence dont number';
    }
    elseif ((int)$stock < 0)
    {
        $errors['stock'] = 'Остаток не может быть меньше нуля.';
    }

    $price = isset($data['price']) ? ($data['price']) : '';
    if (!is_numeric($price))
    {
        $errors['price'] = 'dont number';
    }
    elseif ((int)$price < 0)
    {
        $errors['price'] = '>0.';
    }

    $discount = isset($data['discount']) ? ($data['discount']) : '';
    if (!is_numeric($discount))
    {
        $errors['discount'] = 'Dont number';
    }
    elseif ((float)$discount < 0 || (float)$discount > 100)
    {
        $errors['discount'] = '0 > Discount < 100.';
    }

    return $errors;
}
function sanitize_product($data)
{
    return [
        'title' => trim($data['name']),
        'category' => trim($data['category']),
        'price' => (float)$data['price'],
        'stock' => (int)$data['stock'],
        'discount' => (float)$data['discount'],
    ];
}
function price_without_discount($price, $discount)
{
    return round($price * ($discount / 100), 2);
}
function save_catalog($catalog)
{
    $lines = array();
    $lines[] = '<?php';
    $lines[] = 'return ' . var_export($catalog, true) . ';';
    $content = implode(PHP_EOL, $lines) . PHP_EOL;
    file_put_contents(__DIR__.'/data.php', $content);
}
