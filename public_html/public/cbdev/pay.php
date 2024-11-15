<?php

/*
 * ==========================================================
 * PAY.PHP
 * ==========================================================
 *
 * Payment page
 *
 */

if (!file_exists(__DIR__ . '/config.php')) {
    die();
}
require_once(__DIR__ . '/functions.php');
if (BXC_CLOUD) {
    bxc_cloud_load();
}
$logo = bxc_settings_get('logo-pay');
$minify = isset($_GET['debug']) ? false : (BXC_CLOUD || bxc_settings_get('minify'));
$invoice_form = false;
$invoice_get = bxc_sanatize_string(bxc_isset($_GET, 'invoice'));
if ($invoice_get) {
    $transaction_id = bxc_encryption($invoice_get, false);
    if ($transaction_id) {
        if (isset($_GET['download'])) {
            $invoice = bxc_transactions_invoice($transaction_id);
            die($invoice ? '<script>document.location = "' . $invoice . '"</script>' : 'Transaction not found or not confirmed.');
        }
        $transaction = bxc_transactions_get($transaction_id);
        if ($transaction) {
            if ($transaction['status'] != 'P') {
                $invoice_form = '<div class="bxc-main bxc-billing-page">' . bxc_transactions_invoice_form(true) . '<div id="bxc-generate-invoice" class="bxc-btn">' . bxc_('View Invoice') . '</div></div>';
                $invoice_form .= '<style>.bxc-main { text-align: center; } .bxc-main > #bxc-generate-invoice.bxc-btn { margin-top: 30px !important; }</style>';
                $invoice_form .= '<script>
                             (function () {
                                 let _ = window._query;
                                 let billing = ' . ($transaction['billing'] ? $transaction['billing'] : '_.storage(\'bxc-billing\')') . ';
                                 _(\'body\').on(\'click\', \'#bxc-generate-invoice\', function () {
                                     let billing_details = BOXCoin.checkout.getBillingDetails(_(\'#bxc-billing\'));
                                     if (billing_details) {
                                        BOXCoin.ajax(\'invoice-user\', {
                                            encrypted_transaction_id: "' . $invoice_get . '",
                                            billing_details: billing_details
                                        }, (response) => {
                                            if (response && response.includes(\'http\')) {
                                                _.storage(\'bxc-billing\', billing_details);
                                                window.open(response);
                                            } else {
                                                alert(JSON.stringify(response));
                                            }
                                        });
                                     }
                                 });
                                 if (billing) {
                                    for (var key in billing) {
                                        _(\'#bxc-billing\').find(`#bxc-billing [name="${key}"]`).val(billing[key]);
                                    }
                                 }
                             }());
                             </script>';
            } else {
                die('Transaction not confirmed.');
            }
        } else {
            die('Transaction not found.');
        }
    } else {
        die('Transaction ID not found.');
    }
}
if (defined('BXC_SHOP')) {
    if (isset($_GET['downloads'])) {
        bxc_shop_downloads(bxc_sanatize_string($_GET['downloads']));
    }
}

$code_transaction = '';
if (isset($_GET['id']) && !isset($_GET['demo'])) {
    $transaction = bxc_transactions_get(bxc_encryption(bxc_sanatize_string($_GET['id']), false));
    $fiat_redirect = '';
    if (!$transaction) {
        die('Transaction not found.');
    }
    if ($transaction['status'] != 'P') {
        die('Transaction not in pending status.');
    }
    if (bxc_crypto_is_fiat($transaction['cryptocurrency'])) {
        $fiat_redirect = 'document.location = "' . bxc_checkout_url($transaction['amount_fiat'], $transaction['cryptocurrency'], $transaction['currency'], BXC_URL . 'pay.php?id=' . bxc_sanatize_string($_GET['id']), $transaction['id'], $transaction['title']) . '";';
    }
    $_GET['checkout_id'] = 'custom-pay-page';
    $code_transaction = '<script>BOXCoin.checkout.storageTransaction("custom-pay-page", "delete"); BOXCoin.checkout.storageTransaction("custom-pay-page", { id: ' . $transaction['id'] . ', amount: "' . $transaction['amount'] . '", to: "' . $transaction['to'] . '", cryptocurrency: "' . $transaction['cryptocurrency'] . '", external_reference: "' . $transaction['external_reference'] . '", vat: "' . $transaction['vat'] . '", encrypted: "' . bxc_encryption($transaction) . '", min_confirmations: ' . bxc_settings_get_confirmations($transaction['cryptocurrency'], $transaction['amount']) . ', prevent_cancel: true });' . $fiat_redirect . '</script>';
}
if (bxc_settings_get('css-pay')) {
    $code_transaction .= PHP_EOL . '<link rel="stylesheet" href="' . bxc_settings_get('css-pay') . '?v=' . BXC_VERSION . '" media="all" />';
}
$favicon = BXC_CLOUD ? CLOUD_ICON : ($logo ? bxc_settings_get('logo-icon-url', BXC_URL . 'media/icon.svg') : BXC_URL . 'media/icon.svg');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <title>
        <?php bxc_e(bxc_settings_get('form-title', 'Payment method')) ?>
    </title>
    <?php
    if (isset($_GET['lang'])) {
        echo '<script>var BXC_LANGUAGE = "' . substr(bxc_sanatize_string($_GET['lang']), 0, 2) . '";</script>';
    }
    ?>
    <link rel="shortcut icon" type="image/<?php strpos($favicon, '.png') ? 'png' : 'svg' ?>" href="<?php echo $favicon ?>" />
    <script id="boxcoin" src="<?php echo BXC_URL . 'js/client' . ($minify ? '.min' : '') ?>.js?v=<?php echo BXC_VERSION ?>"></script>
    <?php
    if (BXC_CLOUD) {
        bxc_cloud_front();
    }
    echo $code_transaction;
    ?>
    <style>
        body {
            text-align: center;
            padding: 100px 0;
        }

        .bxc-main {
            text-align: left;
            margin: auto;
        }

        .bxc-pay-logo {
            text-align: center;
        }

        .bxc-pay-logo img {
            margin: 0 auto 30px auto;
            max-width: 200px;
        }
    </style>
</head>
<body style="display: none">
    <script>(function () { setTimeout(() => { document.body.style.removeProperty('display') }, 500) }())</script>
    <?php
    if ($logo) {
        echo '<div class="bxc-pay-logo"><img src="' . bxc_settings_get('logo-url') . '" alt="" /></div>';
    }
    if ($invoice_form) {
        echo $invoice_form;
    } else {
        bxc_checkout_direct();
        echo bxc_settings_get('pay-text');
    }
    ?>
</body>
</html>