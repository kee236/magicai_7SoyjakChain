<?php
require_once(__DIR__ . '/functions.php');
$minify = isset($_GET['debug']) ? false : (BXC_CLOUD || bxc_settings_get('minify'));
bxc_cloud_load();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no" />
    <title>
        <?php echo bxc_settings_get('shop-page-name', BXC_CLOUD ? CLOUD_NAME : 'Boxcoin') ?>
    </title>
    <script src="<?php echo BXC_URL . 'js/client' . ($minify ? '.min' : '') . '.js?v=' . BXC_VERSION ?>"></script>
    <script src="<?php echo BXC_URL . 'apps/shop/shop.admin' . ($minify ? '.min' : '') . '.js?v=' . BXC_VERSION ?>"></script>
    <link rel="stylesheet" href="<?php echo BXC_URL . 'css/client.css?v=' . BXC_VERSION ?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo BXC_URL . 'apps/shop/shop.css?v=' . BXC_VERSION ?>" media="all" />
    <link rel="shortcut icon" type="image/svg" href="<?php echo (bxc_settings_get('logo-icon-url', BXC_CLOUD ? CLOUD_ICON : BXC_URL . 'media/icon.svg')) ?>" />
    <?php echo bxc_settings_get('color-1') ? '<style>#bxc-shop-grid > div:hover { border-color: ' . bxc_settings_get('color-1') . '; } </style>' : '' ?>
</head>
<body>
    <?php echo bxc_shop_page() ?>
</body>
</html>
