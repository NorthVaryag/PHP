<?php
function page_header($title)
{
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title><?= e($title) ?></title>
    </head>
    <body>

    <p><a href="index.php">Каталог</a> | <a href="add.php">Добавить товар</a></p>
    <h1><?= e($title) ?></h1>
    <?php
}

function page_footer()
{
    ?>
    </body>
    </html>
    <?php
}

function banner($type, $text)
{
    $color = $type == 'ok' ? 'green' : 'red';
    echo '<p style="color: ' . $color . '">' . e($text) . '</p>';
}

function product_form($old, $errors, $categories, $button)
{
    ?>
    <form method="post" novalidate>
        <p>
            <label for="title">Название:</label>
            <input type="text" id="title" name="title" value="<?= e(isset($old['title']) ? $old['title'] : '') ?>">
            <?php if (isset($errors['title'])): ?>
                <span style="color: red"><?= e($errors['title']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="category">Категория:</label>
            <select id="category" name="category">
                <option value="">— выберите —</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat) ?>"
                        <?= (isset($old['category']) && $old['category'] === $cat) ? 'selected' : '' ?>>
                        <?= e($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['category'])): ?>
                <span style="color: red"><?= e($errors['category']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="price">Цена:</label>
            <input type="text" id="price" name="price" value="<?= e(isset($old['price']) ? $old['price'] : '') ?>">
            <?php if (isset($errors['price'])): ?>
                <span style="color: red"><?= e($errors['price']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="stock">Остаток:</label>
            <input type="text" id="stock" name="stock" value="<?= e(isset($old['stock']) ? $old['stock'] : '') ?>">
            <?php if (isset($errors['stock'])): ?>
                <span style="color: red"><?= e($errors['stock']) ?></span>
            <?php endif; ?>
        </p>

        <p>
            <label for="discount">Скидка (%):</label>
            <input type="text" id="discount" name="discount" value="<?= e(isset($old['discount']) ? $old['discount'] : '') ?>">
            <?php if (isset($errors['discount'])): ?>
                <span style="color: red"><?= e($errors['discount']) ?></span>
            <?php endif; ?>
        </p>

        <button type="submit"><?= e($button) ?></button>
    </form>
    <?php
}