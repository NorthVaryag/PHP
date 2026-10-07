<?php
require __DIR__ . '/functions.php';
require __DIR__ . '/layout.php';

$catalog = load_catalog();
$categories = unique_categories($catalog);
$errors = [];
$old = ['title' => '', 'category' => '', 'price' => '', 'stock' => '', 'discount' => '0'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old = $_POST;
    $errors = validate_product($_POST);
    if (empty($errors)) {
        $newId = next_id($catalog);
        $catalog = add_product($catalog, $_POST);
        save_catalog($catalog);
        header('Location: index.php?added=' . $newId);
        exit;
    }
}

page_header('Добавить товар');
if (!empty($errors)) {
    banner('error', 'Исправьте ошибки в форме');
}
product_form($old, $errors, $categories, 'Добавить');
page_footer();