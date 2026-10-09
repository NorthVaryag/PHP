<?php

declare(strict_types=1);
require '../vendor/autoload.php';
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

require __DIR__ . '/functions.php';
$catalog = require __DIR__ . '/data.php';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$categories = get_categories($catalog);
$products = filter_products($catalog, $search, $category);
$errors = array();
$success = false;
$old = array('name' => '', 'category' => '','price' => '', 'discount' => '', 'stock' => '');
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $old = $_POST;
    $errors = validate_product($_POST);
    if (empty($errors))
    {
        $newProduct = sanitize_product($_POST);
        array_unshift($catalog, $newProduct);
        save_catalog($catalog);
        $products = filter_products($catalog, $search, $category);
        $success = true;
        $old = array('name' => '', 'category' => '','price' => '', 'discount' => '', 'stock' => '');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Catalog</title>
</head>
<body>

<h1>Catalog:</h1>

<form method="get">
    <p>
        <label for="search">Search name:</label>
        <input type="text" id="search" name="search" value="<?= e($search) ?>">
    </p>
    <p>
        <label for="category">Category:</label>
        <select id="category" name="category">
            <option value="all" <?= $category === 'all' ? 'selected' : '' ?>>All</option>>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= e($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                    <?= e($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <button type="submit">Apply</button>
</form>

<hr>

<h2>Add product</h2>

<?php if ($success): ?>
    <p>Product validate</p>
<?php endif; ?>

<form method="post" novalidate>
    <p>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= e(isset($old['name']) ? $old['name'] : '') ?>">
        <?php if (isset($errors['name'])): ?>
            <span><?= e($errors['name']) ?></span>
        <?php endif; ?>
    </p>

    <p>
        <label for="new_category">Category:</label>
        <select id="new_category" name="category">
            <option value="">— select —</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= e($cat) ?>"
                    <?= (isset($old['category']) && $old['category'] === $cat) ? 'selected' : '' ?>>
                    <?= e($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['category'])): ?>
            <span><?= e($errors['category']) ?></span>
        <?php endif; ?>
    </p>

    <p>
        <label for="price">Cost:</label>
        <input type="text" id="price" name="price" value="<?= e(isset($old['price']) ? $old['price'] : '') ?>">
        <?php if (isset($errors['price'])): ?>
            <span><?= e($errors['price']) ?></span>
        <?php endif; ?>
    </p>

    <p>
        <label for="stock">Residue:</label>
        <input type="text" id="stock" name="stock" value="<?= e(isset($old['stock']) ? $old['stock'] : '') ?>">
        <?php if (isset($errors['stock'])): ?>
            <span><?= e($errors['stock']) ?></span>
        <?php endif; ?>
    </p>

    <p>
        <label for="discount">Discount (%):</label>
        <input type="text" id="discount" name="discount" value="<?= e(isset($old['discount']) ? $old['discount'] : '') ?>">
        <?php if (isset($errors['discount'])): ?>
            <span><?= e($errors['discount']) ?></span>
        <?php endif; ?>
    </p>

    <button type="submit">Add</button>
</form>

<hr>

<h2>Table catalog</h2>

<table border="1">
    <thead>
    <tr>
        <th>Name</th>
        <th>Category</th>
        <th>Count</th>
        <th>Discount</th>
        <th>Count with discount</th>
        <th>Existance</th>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($products)): ?>
        <tr><td colspan="6">Dont found.</td></tr>
    <?php else: ?>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?= e($p['title']) ?></td>
                <td><?= e($p['category']) ?></td>
                <td><?= e($p['price']) ?></td>
                <td><?= e($p['discount']) ?>%</td>
                <td><?= e(price_without_discount($p['price'], $p['discount'])) ?></td>
                <td><?= $p['stock'] > 0 ? 'В наличии' : 'Нет в наличии' ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>
