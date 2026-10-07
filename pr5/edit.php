<?php
require __DIR__ . '/functions.php';
require __DIR__ . '/layout.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$catalog = load_catalog();
$product = find_product($catalog, $id);

if ($product === null) {
    http_response_code(404);
    page_header('Товар не найден');
    echo '<p>Товар не найден</p>';
    page_footer();
    exit;
}

$categories = unique_categories($catalog);
$errors = [];
$old = $product;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old = $_POST;
    $errors = validate_product($_POST);
    if (empty($errors)) {
        $catalog = update_product($catalog, $id, $_POST);
        save_catalog($catalog);
        header('Location: index.php?updated=' . $id);
        exit;
    }
}

page_header('Изменить товар #' . $id);
if (!empty($errors)) {
    banner('error', 'Исправьте ошибки в форме');
}
product_form($old, $errors, $categories, 'Сохранить');
page_footer();