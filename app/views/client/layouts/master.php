<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'FPT Shop'; ?></title>
    <script src="https://kit.fontawesome.com/1f55434e39.js" crossorigin="anonymous"></script>
    <link rel="icon" href="/public/assets/client/images/header/1.png">
    <link rel="stylesheet" href="/public/assets/client/css/main.css">
    <link rel="stylesheet" href="/public/assets/client/css/grid.css">
    <link rel="stylesheet" href="/public/assets/client/css/slider.css">
    <link rel="stylesheet" href="/public/assets/client/css/slider-card.css">
    <link rel="stylesheet" href="/public/assets/client/css/reponsive.css">
    <?php if (!empty($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body>
    <div class="wrapper">
        <?php require_once __DIR__ . '/header.php'; ?>

        <div class="main">
            <?php echo $content ?? ''; ?>
        </div>

        <?php require_once __DIR__ . '/footer.php'; ?>
    </div>

    <script src="/public/assets/client/js/main.js"></script>
    <script src="/public/assets/client/js/slider.js"></script>
    <script src="/public/assets/client/js/slider-card.js"></script>
    <?php if (!empty($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>

</html>