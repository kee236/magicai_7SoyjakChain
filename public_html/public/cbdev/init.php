<?php
use Web3p\RLP\Types\Str;

/*
 * ==========================================================
 * INIT.PHP
 * ==========================================================
 *
 * This file loads and initilizes the payments
 *
 */

if (!file_exists(__DIR__ . '/config.php')) {
    die();
}
require_once(__DIR__ . '/functions.php');
if (isset($_POST['data'])) {
    $_POST = json_decode($_POST['data'], true);
}
if (BXC_CLOUD) {
    bxc_cloud_load();
    if (floatval(bxc_settings_db('credit_balance')) < 0) {
        bxc_cloud_credit_email(-1);
        die('no-credit-balance');
    }
}
if (isset($_POST['init'])) {
    bxc_checkout_init();
}
if (isset($_POST['checkout'])) {
    bxc_checkout($_POST['checkout']);
}
if (isset($_POST['init_exchange'])) {
    require_once(__DIR__ . '/apps/exchange/exchange_code.php');
    bxc_exchange_init();
}

function bxc_checkout_init() {
    $qr_color = bxc_settings_get('color-2');
    if ($qr_color) {
        if (strpos('#', $qr_color) !== false) {
            $qr_color = substr($qr_color, 1);
        } else {
            $qr_color = str_replace(['rgb(', ')', ',', ' '], ['', '', '-', ''], $qr_color);
        }
    } else {
        $qr_color = '23413e';
    }
    $language = bxc_language();
    $translations = $language ? file_get_contents(__DIR__ . '/resources/languages/client/' . $language . '.json') : '{}';
    $settings = [
        'qr_code_color' => $qr_color,
        'countdown' => bxc_settings_get('refresh-interval', 60),
        'webhook' => bxc_settings_get('webhook-url'),
        'redirect' => bxc_settings_get('payment-redirect'),
        'vat_validation' => bxc_settings_get('vat-validation'),
        'names' => bxc_crypto_name(),
        'cryptocurrencies' => bxc_get_cryptocurrency_codes()
    ];
    if (defined('BXC_EXCHANGE')) {
        $settings['exchange'] = [
            'identity_type' => bxc_settings_get('exchange-identity-type'),
            'email_verification' => bxc_settings_get('exchange-email-verification'),
            'testnet_btc' => bxc_settings_get('testnet-btc'),
            'testnet_eth' => bxc_settings_get('testnet-eth'),
            'texts' => [bxc_(bxc_settings_get('exchange-manual-payments-text-send-complete', 'The payment of {amount} will be sent to the provided payment details:'))],
            'url_rewrite_checkout' => BXC_CLOUD ? 'checkout/' : bxc_settings_get('url-rewrite-checkout')
        ];
    }
    if (defined('BXC_SHOP')) {
        $settings['shop'] = true;
    }
    echo 'var BXC_TRANSLATIONS = ' . ($translations ? $translations : '{}') . '; var BXC_URL = "' . BXC_URL . '"; var BXC_SETTINGS = ' . json_encode($settings, JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE) . ';';
}

function bxc_checkout_custom_fields() {
    $code = '';
    $custom_fields = bxc_settings_get_repeater(['checkout-custom-field-type', 'checkout-custom-field-name', 'checkout-custom-field-required']);
    for ($i = 0; $i < count($custom_fields); $i++) {
        $type = $custom_fields[$i][0];
        $field_name = $custom_fields[$i][1];
        if ($field_name) {
            if ($type != 'select') {
                $code .= '<div class="bxc-input bxc-input-' . $type . '"><span>' . bxc_($field_name) . '</span>';
            }
            switch ($type) {
                case 'text':
                    $code .= '<input name="' . $field_name . '" type="text"' . ($custom_fields[$i][2] ? ' required' : '') . '>';
                    break;
                case 'checkbox':
                    $code .= '<input name="' . $field_name . '" type="checkbox"' . ($custom_fields[$i][2] ? ' required' : '') . '>';
                    break;
                case 'textarea':
                    $code .= '<textarea name="' . $field_name . '"' . ($custom_fields[$i][2] ? ' required' : '') . '></textarea>';
                    break;
                case 'select':
                    $select_values = explode(':', $field_name);
                    $code .= '<div class="bxc-input bxc-input-select"><span>' . bxc_($select_values[0]) . '</span><select name="' . $select_values[0] . '"' . ($custom_fields[$i][2] ? ' required' : '') . '><option value=""></option>';
                    for ($j = 1; $j < count($select_values); $j++) {
                        $code .= '<option value="' . str_replace('"', '\'', $select_values[$j]) . '">' . bxc_($select_values[$j]) . '</option>';
                    }
                    $code .= '</select>';
                    break;
            }
            $code .= '</div>';
        }
    }
    if ($code) {
        echo '<div class="bxc-custom-fields"><div class="bxc-title">' . bxc_(bxc_settings_get('checkout-custom-fields-title', 'Information')) . '</div>' . $code . '</div>';
    }
}

function bxc_checkout($settings) {
    $checkout_id = $settings['checkout_id'];
    $custom = strpos($checkout_id, 'custom') !== false;
    $cryptocurrencies = bxc_get_cryptocurrency_codes();
    $cryptocurrencies_code = '';
    $collapse = bxc_settings_get('collapse');
    $body_classes = '';
    if (!$custom) {
        $settings = bxc_checkout_get($checkout_id);
    }
    if (!$settings) {
        die();
    }
    $title = ($custom && !empty($settings['title'])) || (empty($settings['hide_title']) && !bxc_settings_get('hide-title'));
    $image = bxc_isset($settings, 'image');
    $currency_code = empty($settings['currency']) ? bxc_settings_get('currency', 'USD') : $settings['currency'];
    if (bxc_settings_get('stripe-active') || bxc_settings_get('verifone-active')) {
        $cryptocurrencies_code .= '<div data-cryptocurrency="' . (bxc_settings_get('stripe-active') ? 'stripe' : 'verifone') . '" class="bxc-flex"><img src="' . BXC_URL . 'media/icon-cc.svg" alt="' . bxc_('Credit or debit card') . '" /><span>' . bxc_('Credit or debit card') . '</span></div>';
    }
    if (bxc_settings_get('paypal-active')) {
        $cryptocurrencies_code .= '<div data-cryptocurrency="paypal" class="bxc-flex"><img src="' . BXC_URL . 'media/icon-pp-2.svg" alt="PayPal" /><span>PayPal</span></div>';
    }
    if (bxc_settings_get('ln-node-active')) {
        $cryptocurrencies_code .= '<div data-cryptocurrency="btc_ln" class="bxc-flex"><img src="' . BXC_URL . 'media/icon-btc_ln.svg" alt="Bitcoin Lightning Network" /><span>Bitcoin<span class="bxc-label">Lightning <div>Network</div></span></span><span>BTC</span></div>';
    }
    if ($title) {
        $title = bxc_isset($settings, 'title', bxc_settings_get('form-title'));
        if ($title) {
            $title = '<div class="bxc-top bxc-checkout-top"><div><div class="bxc-title">' . bxc_($title) . '</div><div class="bxc-text">' . bxc_render_text(empty($settings['description']) ? bxc_settings_get('form-description', '') : $settings['description']) . '</div></div></div>';
        }
    }
    if ($image) {
        $image = '<div class="bxc-background-image" style="background-image: url(\'' . BXC_URL . 'uploads' . bxc_cloud_path_part() . '/' . $image . '\')"></div>';
    }
    foreach ($cryptocurrencies as $value) {
        for ($i = 0; $i < count($value); $i++) {
            $cryptocurrency_code = $value[$i];
            if (bxc_settings_get_address($cryptocurrency_code)) {
                $cryptocurrencies_code .= '<div data-cryptocurrency="' . $cryptocurrency_code . '"' . (bxc_crypto_is_custom_token($cryptocurrency_code) ? ' data-custom-coin="' . bxc_get_custom_tokens()[$cryptocurrency_code]['type'] . '"' : '') . ' class="bxc-flex"><img src="' . bxc_crypto_get_image($cryptocurrency_code) . '" alt="' . strtoupper($cryptocurrency_code) . '" /><span>' . bxc_crypto_name($cryptocurrency_code, true) . bxc_crypto_get_network($cryptocurrency_code, true, true) . '</span><span>' . strtoupper(bxc_crypto_get_base_code($cryptocurrency_code)) . '</span></div>';
            }
        }
    }
    $checkout_price = floatval($settings['price']);
    if ($checkout_price == -1) {
        $checkout_price = '';
    }
    $checkout_start_price = $checkout_price;
    $checkout_type = empty($_POST['payment_page']) ? bxc_isset($settings, 'type', 'I') : 'I';
    $checkout_type = bxc_isset(['I' => 'inline', 'L' => 'link', 'P' => 'popup', 'H' => 'hidden'], $checkout_type, $checkout_type);
    echo '<!-- Boxcoin - https://boxcoin.dev -->';
    if ($checkout_type == 'popup') {
        echo '<div class="bxc-btn bxc-btn-popup"><img src="' . BXC_URL . 'media/icon-cryptos.svg" alt="" />' . bxc_(bxc_settings_get('button-text', 'Pay now')) . '</div><div class="bxc-popup-overlay"></div>';
    }
    $css = false;
    $color_1 = bxc_settings_get('color-1');
    $color_2 = bxc_settings_get('color-2');
    $color_3 = bxc_settings_get('color-3');
    $vat = bxc_settings_get('vat');
    if ($vat && $checkout_price) {
        $vat_details = bxc_vat($checkout_price, false, $currency_code);
        $checkout_price = $vat_details[0];
        $vat = '<span class="bxc-vat" data-country="' . $vat_details[3] . '" data-country-code="' . $vat_details[2] . '" data-amount="' . $vat_details[1] . '" data-percentage="' . $vat_details[5] . '">' . $vat_details[4] . '</span>';
    }
    if ($color_1) {
        $css = '.bxc-payment-methods>div:hover .bxc-label,.bxc-payment-methods>div:hover,.bxc-btn.bxc-btn-border:hover, .bxc-btn.bxc-btn-border:active { border-color: ' . $color_1 . '; color: ' . $color_1 . '; }';
        $css .= '.bxc-complete-cnt>i, .bxc-failed-cnt>i,.bxc-payment-methods>div:hover span+span,.bxc-clipboard:hover,.bxc-tx-cnt .bxc-loading:before,.bxc-input-btn .bxc-btn.bxc-loading:before,.bxc-loading:before,.bxc-btn-text:hover,.bxc-input input[type="checkbox"]:checked:before,.bxc-link:hover,.bxc-link:active,.bxc-underline:hover,.bxc-underline:active,.bxc-complete-cnt .bxc-link:hover { color: ' . $color_1 . '; }';
        $css .= '.bxc-tx-status,.bxc-select ul li:hover,.bxc-underline:hover:after { background-color: ' . $color_1 . '; }';
        $css .= '.bxc-input input:focus, .bxc-input input.bxc-focus, .bxc-input select:focus, .bxc-input select.bxc-focus, .bxc-input textarea:focus, .bxc-input textarea.bxc-focus { border-color: ' . $color_1 . '; box-shadow: 0 0 5px rgb(0 0 0 / 20%);';
    }
    if ($color_2) {
        $css .= '.bxc-box { color: ' . $color_2 . '; }';
    }
    if ($color_3) {
        $css .= '.bxc-text,.bxc-payment-methods>div span+span { color: ' . $color_3 . '; }';
        $css .= '.bxc-btn.bxc-btn-border { border-color: ' . $color_3 . '; color: ' . $color_3 . '; }';
    }
    if ($css) {
        echo '<style>' . $css . '</style>';
    }
    $shop_code = defined('BXC_SHOP') ? bxc_shop_checkout_init($checkout_id) : '';
    if (bxc_is_rtl(bxc_language())) {
        $body_classes .= ' bxc-rtl';
    }
    ?>
    <div class="bxc-main bxc-start bxc-<?php echo $checkout_type . $body_classes ?>" data-checkout-id="<?php echo $checkout_id ?>" data-currency="<?php echo $currency_code ?>" data-price="<?php echo $checkout_price ?>" data-external-reference="<?php echo bxc_isset($settings, 'external_reference', bxc_isset($settings, 'external-reference', '')) ?>" data-title="<?php echo str_replace('"', '', bxc_isset($settings, 'title', '')) ?>" data-note="<?php echo str_replace('"', '', bxc_isset($settings, 'note', '')) ?>" data-redirect="<?php echo bxc_isset($settings, 'redirect', '') ?>" data-start-price="<?php echo $checkout_start_price ?>">
        <?php
        if ($checkout_type == 'popup') {
            echo '<i class="bxc-popup-close bxc-icon-close"></i>';
        }
        ?>
        <div class="bxc-cnt bxc-box">
            <?php echo $image . $title ?>
            <div class="bxc-body">
                <div id="bxc-error-message" class="bxc-error bxc-text"></div>
                <div class="bxc-flex bxc-amount-fiat<?php echo $checkout_price ? '' : ' bxc-donation' ?>">
                    <div class="bxc-title">
                        <?php
                        bxc_e($checkout_price ? 'Total' : 'Amount');
                        if (!$checkout_price) {
                            echo '<div class="bxc-text">' . bxc_(bxc_settings_get('user-amount-text', 'Pay what you want')) . '</div>';
                        }
                        ?>
                    </div>
                    <div class="bxc-title">
                        <?php echo $checkout_price ? strtoupper($currency_code) . ' <span class="bxc-amount-fiat-total">' . bxc_decimal_number($checkout_price) . '</span>' . $vat : '<div class="bxc-input" id="user-amount"><span>' . strtoupper($currency_code) . '</span><input type="number" min="0" /></div>' ?>
                    </div>
                </div>
                <?php
                echo $shop_code;
                if (!$custom || strpos($checkout_id, 'custom-ex') === false) {
                    bxc_checkout_custom_fields();
                }
                if (bxc_settings_get('invoice-active')) {
                    echo bxc_transactions_invoice_form();
                }
                ?>
                <div class="bxc-payment-methods-cnt">
                    <div <?php echo $collapse ? 'class="bxc-collapse"' : '' ?>>
                        <div class="bxc-payment-methods">
                            <?php echo $cryptocurrencies_code ?>
                        </div>
                        <?php
                        if ($collapse) {
                            echo '<div class="bxc-btn-text bxc-collapse-btn"><i class="bxc-icon-arrow-down"></i>' . bxc_('All cryptocurrencies') . '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="bxc-pay-cnt bxc-box">
            <div class="bxc-top">
                <div class="bxc-pay-top-main">
                    <div class="bxc-title">
                        <?php bxc_e(bxc_settings_get('form-payment-title', 'Send payment')) ?>
                        <div class="bxc-flex">
                            <div class="bxc-countdown bxc-toolip-cnt">
                                <div data-countdown="<?php bxc_settings_get('refresh-interval', 60) ?>"></div>
                                <span class="bxc-toolip">
                                    <?php bxc_e('Checkout timeout') ?>
                                </span>
                            </div>
                            <div class="bxc-btn bxc-btn-border bxc-back">
                                <i class="bxc-icon-back"></i>
                                <?php bxc_e('Back') ?>
                            </div>
                        </div>
                    </div>
                    <?php echo '<div class="bxc-text">' . bxc_render_text(bxc_settings_get('form-payment-description', '')) . '</div>' ?>
                </div>
                <div class="bxc-pay-top-back">
                    <div class="bxc-title">
                        <?php bxc_e('Are you sure?') ?>
                    </div>
                    <div class="bxc-text">
                        <?php bxc_e('This transaction will be cancelled. If you already sent the payment please wait.') ?>
                    </div>
                    <div id="bxc-confirm-cancel" class="bxc-btn bxc-btn-border bxc-btn-red">
                        <?php bxc_e('Yes, I\'m sure') ?>
                    </div>
                    <div id="bxc-abort-cancel" class="bxc-btn bxc-btn-border bxc-back">
                        <?php bxc_e('Cancel') ?>
                    </div>
                </div>
            </div>
            <div class="bxc-body">
                <div class="bxc-flex">
                    <?php
                    if (!bxc_settings_get('disable-qrcode')) {
                        echo '<a class="bxc-qrcode-link bxc-toolip-cnt"><img class="bxc-qrcode" src="" alt="QR code" /><span class="bxc-toolip">' . bxc_('Open in wallet') . '</span></a>';
                    }
                    ?>
                    <div class="bxc-flex bxc-qrcode-text">
                        <img src="" alt="" />
                        <div class="bxc-text"></div>
                    </div>
                </div>
                <div class="bxc-flex bxc-pay-address">
                    <div>
                        <div class="bxc-text"></div>
                        <div class="bxc-title"></div>
                    </div>
                    <i class="bxc-icon-copy bxc-clipboard bxc-toolip-cnt">
                        <span class="bxc-toolip">
                            <?php bxc_e('Copy to clipboard') ?>
                        </span>
                    </i>
                </div>
                <div class="bxc-flex bxc-pay-amount">
                    <div>
                        <div class="bxc-text">
                            <?php bxc_e('Total amount') ?>
                        </div>
                        <div class="bxc-title"></div>
                    </div>
                    <div class="bxc-flex">
                        <?php
                        if (bxc_settings_get('metamask')) {
                            echo '<div id="metamask" class="bxc-btn bxc-btn-img bxc-hidden"><img src="' . BXC_URL . 'media/metamask.svg" alt="MetaMask" />MetaMask</div>';
                        }
                        ?>
                        <i class="bxc-icon-copy bxc-clipboard bxc-toolip-cnt">
                            <span class="bxc-toolip">
                                <?php bxc_e('Copy to clipboard') ?>
                            </span>
                        </i>
                    </div>
                </div>
            </div>
        </div>
        <div class="bxc-tx-cnt bxc-box">
            <div class="bxc-loading"></div>
            <div class="bxc-title">
                <?php bxc_e('Payment received') ?>
            </div>
            <div class="bxc-flex">
                <div class="bxc-tx-status"></div>
                <div class="bxc-tx-confirmations">
                    <span></span>
                    /
                </div>
                <div>
                    <?php bxc_e('confirmations') ?>
                </div>
            </div>
        </div>
        <div class="bxc-complete-cnt bxc-box">
            <i class="bxc-icon-check"></i>
            <div class="bxc-title">
                <?php bxc_e(bxc_settings_get('success-title', 'Payment completed')) ?>
            </div>
            <div class="bxc-text">
                <span>
                    <?php echo bxc_render_text(bxc_settings_get('success-description', 'Thank you for your payment')) ?>
                </span>
                <span>
                    <?php echo bxc_render_text(bxc_settings_get('order-processing-text', 'We are processing the order, please wait...')) ?>
                </span>
            </div>
        </div>
        <div class="bxc-failed-cnt bxc-box">
            <i class="bxc-icon-close"></i>
            <div class="bxc-title">
                <?php bxc_e(bxc_settings_get('failed-title', 'No payment')) ?>
            </div>
            <div class="bxc-text">
                <?php echo bxc_render_text(bxc_settings_get('failed-text', 'We didn\'t detect a payment. If you have already paid, please contact us.')) ?>
            </div>
            <div class="bxc-text">
                <?php bxc_e('Your transaction ID is:') ?>
                <span id="bxc-expired-tx-id"></span>
            </div>
            <div class="bxc-btn bxc-btn-border ">
                <?php bxc_e('Retry') ?>
            </div>
        </div>
        <div class="bxc-underpayment-cnt bxc-box">
            <i class="bxc-icon-close"></i>
            <div class="bxc-title">
                <?php bxc_e(bxc_settings_get('underpayment-title', 'Underpayment')) ?>
            </div>
            <div class="bxc-text">
                <?php echo bxc_render_text(bxc_settings_get('underpayment-description', 'We have detected your payment but the amount is less than requested and the transaction cannot be completed, please contact us.')) ?>
                <?php bxc_e('Your transaction ID is:') ?><span id="bxc-underpaid-tx-id"></span>
            </div>
        </div>
        <?php
        if (BXC_CLOUD) {
            echo '<a href="' . CLOUD_POWERED_BY[0] . '" target="_blank" class="bxc-cloud-branding" style="display:flex !important;"><span style="display:block !important;">Powered by</span><img style="display:block !important;" src="' . CLOUD_POWERED_BY[1] . '" alt="" /></a>';
        }
        ?>
    </div>
<?php } ?>