<?php

/*
 * ==========================================================
 * FUNCTIONS.PHP
 * ==========================================================
 *
 * Admin and client side functions.
 * You can not use Boxcoin to create a SAAS or Boxcoin-like business. For more details, visit https://boxcoin.dev/terms-of-service (see 5. Intellectual Property and Content Ownership).
 * © 2022-2024 boxcoin.dev. All rights reserved.
 *
 */

use Firebase\JWT\JWT;

define('BXC_VERSION', '1.2.7');
if (!defined('BXC_CLOUD')) {
    define('BXC_CLOUD', file_exists(__DIR__ . '/cloud'));
}
require(__DIR__ . '/config.php');
global $BXC_LOGIN;
global $BXC_LANGUAGE;
global $BXC_TRANSLATIONS;
global $BXC_TRANSLATIONS_2;
global $BXC_APPS;
$BXC_APPS = ['wordpress', 'exchange', 'shop'];
for ($i = 0; $i < count($BXC_APPS); $i++) {
    $file = __DIR__ . '/apps/' . $BXC_APPS[$i] . '/functions.php';
    if (file_exists($file)) {
        require_once($file);
    }
}

/*
 * -----------------------------------------------------------
 * TRANSACTIONS
 * -----------------------------------------------------------
 *
 * 1. Get all transactions
 * 2. Get a single transaction
 * 3. Create a transaction
 * 4. Generate a random cryptcurrency amount
 * 5. Delete pending transactions older than 48h
 * 6. Check the transactions of an address
 * 7. Check a single transaction
 * 8. Check pending transactions
 * 9. Finalize a confirmed transaction
 * 10 Update a transaction
 * 11. Send the webhook for a specific transaction
 * 12. Download transactions in CSV format
 * 13. Generate an invoice
 * 14. Update a transaction
 * 15. Decrypt a transaction securely
 * 16. Generate a payment link for a transaction
 * 17. Get the transaction description array
 * 18. Cancel a transaction
 * 19. Refund a transaction
 * 20. Delete a transaction
 *
 */

function bxc_transactions_get_all($pagination = 0, $search = false, $status = false, $currency = false, $date_range = false, $checkout_id = false, $extra = false) {
    $where = '';
    if ($search) {
        $search = bxc_db_escape(trim($search));
        $where = '(' . (is_numeric($search) ? 'amount LIKE "%' . $search . '%" OR amount_fiat LIKE "%' . $search . '%" OR ' : '') . 'title LIKE "%' . $search . '%" OR description LIKE "%' . $search . '%" OR cryptocurrency LIKE "%' . $search . '%" OR currency LIKE "%' . $search . '%" OR `from` LIKE "%' . $search . '%" OR `to` LIKE "%' . $search . '%" OR hash LIKE "%' . $search . '%" OR external_reference LIKE "%' . $search . '%")';
        if (defined('BXC_SHOP')) {
            if (bxc_settings_get('shop-license-key')) {
                $where .= ' OR id IN (SELECT transaction_id FROM bxc_license_keys WHERE license_key = "' . $search . '")';
            }
            $where .= ' OR customer_id IN (SELECT id FROM bxc_customers WHERE email LIKE "%' . $search . '%" OR phone LIKE "%' . $search . '%" OR country LIKE "%' . $search . '%" OR first_name LIKE "%' . $search . '%" OR last_name LIKE "%' . $search . '%")';
        }
    }
    if ($status) {
        $where .= ($where ? ' AND ' : '') . ' status = "' . bxc_db_escape($status) . '"';
    }
    if ($currency) {
        $where .= ($where ? ' AND ' : '') . (bxc_crypto_is($currency) ? ' cryptocurrency = "' . bxc_db_escape($currency) . '"' : ' (currency = "' . bxc_db_escape($currency) . '" AND (cryptocurrency = "stripe" OR cryptocurrency = "paypal" OR cryptocurrency = "verifone"))');
    }
    if ($date_range && $date_range[0]) {
        $where .= ($where ? ' AND ' : '') . ' creation_time >= "' . bxc_db_escape($date_range[0]) . '" AND creation_time <= "' . bxc_db_escape($date_range[1]) . '"';
    }
    if ($checkout_id) {
        $where .= ($where ? ' AND ' : '') . ' checkout_id = ' . bxc_db_escape($checkout_id, true);
    }
    $transactions = bxc_db_get('SELECT * FROM bxc_transactions' . ($where ? ' WHERE ' . $where : '') . ' ORDER BY id DESC' . ($pagination != -1 ? ' LIMIT ' . intval(bxc_db_escape($pagination, true)) * 100 . ',100' : ''), false);
    if (defined('BXC_SHOP') && bxc_settings_get('shop-license-key') && (!$extra || $extra != 'no_shop')) {
        $license_keys = bxc_db_get('SELECT license_key, status, transaction_id FROM bxc_license_keys', false);
        for ($i = 0; $i < count($license_keys); $i++) {
            $transaction_id = $license_keys[$i]['transaction_id'];
            for ($j = 0; $j < count($transactions); $j++) {
                if ($transaction_id == $transactions[$j]['id']) {
                    $transactions[$j]['license_key'] = $license_keys[$i]['license_key'];
                    $transactions[$j]['license_key_status'] = $license_keys[$i]['status'];
                    break;
                }
            }
        }
    }
    for ($i = 0; $i < count($transactions); $i++) {
        $notes = json_decode(bxc_isset($transactions[$i], 'description', '[]'), true);
        $transactions[$i]['description'] = empty($notes) ? [$transactions[$i]['description']] : $notes;
    }

    return $transactions;
}

function bxc_transactions_get($transaction_id) {
    $name = 'BXC_TRANSACTION_' . $transaction_id;
    $transaction = bxc_isset($GLOBALS, $name);
    if ($transaction) {
        return $transaction;
    }
    $transaction = bxc_db_get('SELECT * FROM bxc_transactions WHERE id = ' . bxc_db_escape($transaction_id, true));
    if ($transaction) {
        $GLOBALS[$name] = $transaction;
        return $transaction;
    }
    return false;
}

function bxc_transactions_create($amount, $cryptocurrency_code, $currency_code = false, $external_reference = '', $title = '', $description = '', $url = false, $billing = '', $vat = false, $checkout_id = false, $user_details = false, $discount_code = false, $type = 1) {
    $fee = bxc_settings_get('payment-fee', 0);
    if ($fee) {
        $amount += $amount * ($fee / 100);
    }
    if ($checkout_id && !is_numeric($checkout_id)) {
        $checkout_id = bxc_isset(bxc_db_get('SELECT id FROM bxc_checkouts WHERE slug = "' . bxc_db_escape($checkout_id) . '"'), 'id');
    }
    $query_parts = ['INSERT INTO bxc_transactions(title, description, `from`, `to`, hash, amount, amount_fiat, cryptocurrency, currency, external_reference, creation_time, status, webhook, billing, vat, vat_details, checkout_id, type) VALUES ("' . bxc_db_escape($title) . '", "' . ($description ? bxc_db_json_escape([urldecode($description)]) : '') . '", "",', ', "' . bxc_db_escape($currency_code) . '", "' . bxc_db_escape($external_reference) . '", "' . gmdate('Y-m-d H:i:s') . '", "P", 0, "' . bxc_db_escape($billing) . '", "' . bxc_isset($vat, 'amount', 0) . '", "' . ($vat && !empty($vat['amount']) ? bxc_db_json_escape($vat) : '') . '", ' . ($checkout_id && is_numeric($checkout_id) ? bxc_db_escape($checkout_id, true) : 'NULL') . ',' . bxc_db_escape($type, true) . ' )'];
    $hash = '';
    $address = false;
    if (!$currency_code) {
        $currency_code = bxc_settings_get('currency', 'USD');
    }
    if (bxc_crypto_is_fiat($cryptocurrency_code)) {
        $transaction_id = bxc_db_query($query_parts[0] . '"", "", "", "' . bxc_db_escape($amount, true) . '", "' . bxc_db_escape($cryptocurrency_code) . '"' . $query_parts[1], true);
        if (defined('BXC_SHOP')) {
            $response = bxc_shop_create_transaction_part($transaction_id, $discount_code, $amount, $user_details);
            if ($response !== true) {
                return $response;
            }
        }
        $payment_description = $title;
        if ($vat && $vat['amount']) {
            $payment_description .= ' (' . bxc_('VAT') . ' ' . $vat['amount'] . strtoupper($currency_code) . ($vat['country_code'] ? ' ' . $vat['country_code'] : '') . ')';
        }
        return [$transaction_id, $cryptocurrency_code, bxc_checkout_url($amount, $cryptocurrency_code, $currency_code, $url, $transaction_id, $title, $payment_description), bxc_encryption(bxc_transactions_get($transaction_id))];
    }
    if ($cryptocurrency_code === 'btc_ln' && bxc_settings_get('ln-node-active')) {
        require_once(__DIR__ . '/bitcoin.php');
        $amount_cryptocurrency = $amount_cryptocurrency_string = bxc_crypto_get_cryptocurrency_value($amount, 'btc', $currency_code);
        $invoice = bxc_btc_ln_create_invoice($amount_cryptocurrency);
        $address = bxc_isset($invoice, 'payment_request');
        if ($address) {
            $hash = $invoice['r_hash'];
        } else {
            return ['error', 'btc-ln'];
        }
    }
    if (!$address) {
        $decimals = bxc_crypto_get_decimals($cryptocurrency_code);
        $custom_token = bxc_isset(bxc_get_custom_tokens(), $cryptocurrency_code);
        $address = $custom_token ? $custom_token['address'] : bxc_crypto_get_address($cryptocurrency_code);
        $amount_cryptocurrency = $currency_code == 'crypto' ? [$amount, ''] : explode('.', strval(bxc_crypto_get_cryptocurrency_value($amount, $cryptocurrency_code, $currency_code)));
        if (bxc_crypto_whitelist_invalid($address, true, $cryptocurrency_code)) {
            return 'whitelist-invalid';
        }
        if ($amount_cryptocurrency && !isset($amount_cryptocurrency[1])) {
            array_push($amount_cryptocurrency, '');
        }
        if ($custom_token) {
            if ($custom_token['rate_url']) {
                $custom_token['rate'] = floatval(bxc_curl($custom_token['rate_url']));
            }
            if (!empty($custom_token['rate'])) {
                $amount_cryptocurrency = explode('.', $amount * (1 / (floatval(bxc_isset($custom_token, 'rate', 1)) * ($currency_code == 'USD' ? 1 : bxc_usd_rates($currency_code)))));
                if (!isset($amount_cryptocurrency[1])) {
                    array_push($amount_cryptocurrency, '');
                }
            }
            $decimals = $custom_token['decimals'];
        }
        if (strlen($amount_cryptocurrency[1]) > $decimals) {
            $amount_cryptocurrency[1] = substr($amount_cryptocurrency[1], 0, $decimals);
        }
        $amount_cryptocurrency_string = $amount_cryptocurrency[0] . ($amount_cryptocurrency[1] ? '.' . $amount_cryptocurrency[1] : '');
        if ($address == bxc_settings_get_address($cryptocurrency_code)) {
            $temp = bxc_db_get('SELECT amount FROM bxc_transactions WHERE cryptocurrency = "' . bxc_db_escape($cryptocurrency_code) . '"', false);
            $existing_amounts = [];
            $i = 0;
            for ($i = 0; $i < count($temp); $i++) {
                array_push($existing_amounts, $temp[$i]['amount']);
            }
            while (in_array($amount_cryptocurrency_string, $existing_amounts) && $i < 1000) {
                $amount_cryptocurrency_string = bxc_transactions_random_amount($amount_cryptocurrency, $decimals);
                $i++;
            }
        }
    }
    $transaction_id = bxc_db_query($query_parts[0] . '"' . $address . '", "' . $hash . '", "' . $amount_cryptocurrency_string . '", "' . bxc_db_escape($amount, true) . '", "' . bxc_db_escape($cryptocurrency_code) . '"' . $query_parts[1], true);
    if (defined('BXC_SHOP')) {
        $response = bxc_shop_create_transaction_part($transaction_id, $discount_code, $amount, $user_details);
        if ($response !== true) {
            return $response;
        }
    }
    $url = bxc_is_demo(true);
    if ($url) {
        $amount_cryptocurrency_string = $url['amount'];
        $transaction_id = $url['id'];
    }
    if (in_array(bxc_crypto_get_base_code($cryptocurrency_code), ['usdt', 'usdc', 'busd']) && bxc_is_address_generation($cryptocurrency_code)) {
        $amount_cryptocurrency_string_split = explode('.', $amount_cryptocurrency_string);
        if (count($amount_cryptocurrency_string_split) > 1 && strlen($amount_cryptocurrency_string_split[1]) > 2) {
            $amount_cryptocurrency_string = $amount_cryptocurrency_string_split[0] . '.' . substr($amount_cryptocurrency_string_split[1], 0, 2);
            bxc_transactions_update($transaction_id, ['amount' => $amount_cryptocurrency_string], false);
        }
    }
    return [$transaction_id, $amount_cryptocurrency_string, $address, bxc_settings_get_confirmations($cryptocurrency_code, $amount), bxc_encryption(bxc_transactions_get($transaction_id))];
}

function bxc_transactions_random_amount($amount, $decimals) {
    $amount = bxc_decimal_number(floatval($amount[0] . ($amount[1] && $amount[1] != '0' ? '.' . $amount[1] : '')) * floatval('1.000' . rand(99, 9999)));
    if (strpos($amount, '.')) {
        $amount = explode('.', $amount);
        while (strlen($amount[1]) > $decimals) {
            $amount[1] = substr($amount[1], 0, $decimals);
        }
        $amount = $amount[0] . ($amount[1] && $amount[1] != '0' ? '.' . $amount[1] : '');
    }
    return $amount;
}

function bxc_transactions_delete_pending() {
    $query = 'FROM bxc_transactions WHERE status = "P" AND creation_time < "' . gmdate('Y-m-d H:i:s', time() - intval(bxc_settings_get('delete-pending-interval', 48)) * 3600) . '"';
    $transactions = bxc_db_get('SELECT `to`, `cryptocurrency` ' . $query, false);
    $response = bxc_db_query('DELETE ' . $query);
    if ($response === true) {
        $addresses = [];
        for ($i = 0; $i < count($transactions); $i++) {
            $to = $transactions[$i]['to'];
            $slug = $transactions[$i]['cryptocurrency'] . '-manual-addresses';
            if (!isset($addresses[$slug])) {
                $addresses[$slug] = json_decode(bxc_settings_db($slug, false, '{}'), true);
            }
            if (isset($addresses[$slug][$to])) {
                unset($addresses[$slug][$to]);
                bxc_settings_db($slug, $addresses[$slug]);
            }
        }
    }
    return $response;
}

function bxc_transactions_check($transaction_id) {
    $boxcoin_transaction = bxc_transactions_get($transaction_id);
    if (!$boxcoin_transaction) {
        return bxc_error('Transaction ' . $transaction_id . ' not found.', 'bxc_transactions_check', true);
    }
    $refresh_interval = intval(bxc_settings_get('refresh-interval', 60)) * 60;
    $time = time();
    $transaction_creation_time = strtotime($boxcoin_transaction['creation_time'] . ' UTC');
    if ((($transaction_creation_time + $refresh_interval) <= $time) && !bxc_is_demo()) {
        return 'expired';
    }
    if ($boxcoin_transaction) {
        $cryptocurrency_code = $boxcoin_transaction['cryptocurrency'];
        if ($cryptocurrency_code === 'btc_ln') {
            require_once(__DIR__ . '/bitcoin.php');
            $invoice = bxc_btc_ln_get_invoice($boxcoin_transaction['hash']);
            return $invoice && bxc_isset($invoice, 'state') === 'SETTLED' ? bxc_transactions_check_single(bxc_encryption($boxcoin_transaction)) : $invoice;
        } else {
            $to = $boxcoin_transaction['to'];
            $address_generation = $to != bxc_settings_get_address($cryptocurrency_code) && !bxc_is_demo();
            if (bxc_crypto_whitelist_invalid($to, true, $cryptocurrency_code)) {
                return false;
            }
            $transactions = bxc_blockchain($cryptocurrency_code, 'transactions', false, $to);
            if (is_array($transactions)) {
                for ($i = 0; $i < count($transactions); $i++) {
                    $transaction = $transactions[$i];
                    $transaction_time = bxc_isset($transaction, 'time');
                    $transaction_hash = bxc_isset($transaction, 'hash');
                    if ((!$transaction_hash || (bxc_is_demo() || !bxc_db_get('SELECT id FROM bxc_transactions WHERE hash = "' . bxc_db_escape($transaction['hash']) . '" LIMIT 1'))) && (empty($transaction['address']) || strtolower($transaction['address']) != strtolower($to)) && (!$transaction_time || $transaction_time > $transaction_creation_time) && ($address_generation || $boxcoin_transaction['amount'] == $transaction['value'] || strpos($transaction['value'], $boxcoin_transaction['amount']) === 0)) {
                        if ($address_generation && empty($transaction_time)) {
                            $transaction = bxc_blockchain($cryptocurrency_code, 'transaction', $transaction_hash, $transaction['address']);
                            if (bxc_isset($transaction, 'time') < $transaction_creation_time) {
                                return false;
                            }
                        }
                        return bxc_encryption(array_merge($boxcoin_transaction, ['hash' => $transaction_hash, 'id' => $transaction_id, 'cryptocurrency' => $cryptocurrency_code, 'to' => $to]));
                    }
                }
            } else {
                return ['error', $transactions];
            }
        }
    }
    return false;
}

function bxc_transactions_check_single($transaction) {
    $transaction = bxc_transactions_decrypt($transaction);
    $cryptocurrency_code = $transaction['cryptocurrency'];
    $is_crypto = !bxc_crypto_is_fiat($cryptocurrency_code);
    if (!$is_crypto) {
        $transaction = bxc_transactions_get($transaction['id']);
    }
    $invoice = bxc_isset($transaction, 'billing') && bxc_settings_get('invoice-active') ? bxc_transactions_invoice($transaction) : false;
    if ($cryptocurrency_code === 'btc_ln') {
        $response = bxc_transactions_complete($transaction, $transaction['amount'], '');
        return ['confirmed' => true, 'invoice' => $invoice, 'redirect' => bxc_isset($response, 'redirect'), 'source' => bxc_isset($response, 'source'), 'license_key' => bxc_isset($response, 'license_key'), 'downloads_url' => bxc_isset($response, 'downloads_url')];
    } else if ($is_crypto) {
        $minimum_confirmations = bxc_settings_get_confirmations($cryptocurrency_code, $transaction['amount']);
        $transaction_blockchain = bxc_blockchain($cryptocurrency_code, 'transaction', $transaction['hash'], $transaction['to']);
        if (!$transaction_blockchain) {
            return 'transaction-not-found';
        }
        if (is_string($transaction_blockchain)) {
            return bxc_error($transaction_blockchain, 'bxc_transactions_check_single', true);
        }
        $confirmations = bxc_isset($transaction_blockchain, 'confirmations');
        if (!$confirmations && $transaction_blockchain['block_height']) {
            $confirmations = max(bxc_blockchain($cryptocurrency_code, 'blocks_count') - $transaction_blockchain['block_height'] + 1, 0);
        }
        $confirmed = $confirmations >= $minimum_confirmations;
        $response = $confirmed ? bxc_transactions_complete($transaction, $transaction_blockchain['value'], $transaction_blockchain['address'], $invoice) : [];
        return ['confirmed' => $confirmed, 'confirmations' => $confirmations ? $confirmations : 0, 'minimum_confirmations' => $minimum_confirmations, 'hash' => $transaction['hash'], 'invoice' => $invoice, 'underpayment' => bxc_isset($response, 'underpayment') ? $transaction_blockchain['value'] : false, 'redirect' => bxc_isset($response, 'redirect'), 'source' => bxc_isset($response, 'source'), 'license_key' => bxc_isset($response, 'license_key'), 'downloads_url' => bxc_isset($response, 'downloads_url')];
    } else {
        $confirmed = $transaction['status'] === 'C';
        return array_merge(['confirmed' => $confirmed, 'invoice' => $invoice], defined('BXC_SHOP') && $confirmed ? bxc_shop_transaction_complete_get_details($transaction['checkout_id'], $transaction['id'], $transaction['customer_id']) : []);
    }
}

function bxc_transactions_check_pending() {
    $transactions = bxc_db_get('SELECT * FROM bxc_transactions WHERE status = "P" AND creation_time > "' . gmdate('Y-m-d H:i:s', time() - 172800) . '"', false);
    $transactions_blockchains = [];
    for ($i = 0; $i < count($transactions); $i++) {
        $transaction = $transactions[$i];
        $to = $transaction['to'];
        $cryptocurrency_code = strtolower($transaction['cryptocurrency']);
        if (bxc_crypto_whitelist_invalid($to, true, $cryptocurrency_code) || !bxc_crypto_is($cryptocurrency_code)) {
            continue;
        }
        if (!isset($transactions_blockchains[$to])) {
            $transactions_blockchains[$to] = bxc_blockchain($cryptocurrency_code, 'transactions', ['limit' => 99], $to);
        }
        $transactions_blockchain = $transactions_blockchains[$to];
        $address_generation = $to != bxc_settings_get_address($cryptocurrency_code);
        if (is_array($transactions_blockchain)) {
            for ($y = 0; $y < count($transactions_blockchain); $y++) {
                $transaction_blockchain = $transactions_blockchain[$y];
                if ((empty($transaction_blockchain['time']) || $transaction_blockchain['time'] > strtotime($transaction['creation_time'] . ' UTC')) && ($address_generation || $transaction['amount'] == $transaction_blockchain['value'] || strpos($transaction_blockchain['value'], $transaction['amount']) === 0)) {
                    $transaction['hash'] = $transaction_blockchain['hash'];
                    $response = bxc_transactions_check_single($transaction);
                    if ($response && !empty($response['confirmed'])) {
                        bxc_transactions_update($transaction['id'], ['status' => 'C']);
                    }
                }
            }
        }
    }
}

function bxc_transactions_complete($transaction, $amount_blockchain, $address_from, $invoice = false) {
    $redirect = false;
    $source = false;
    $license_key = false;
    $amount = bxc_isset($transaction, 'amount', $transaction['amount_fiat']);
    $cryptocurrency_code = $transaction['cryptocurrency'];
    $external_reference = $transaction['external_reference'];
    $underpayment = empty($amount) || floatval($amount_blockchain) < floatval($amount);
    $node_transfer = false;
    $checkout_id = bxc_isset($transaction, 'checkout_id');
    $checkout_title = '';
    $query = [];
    $return = [];
    if ($checkout_id) {
        $checkout = bxc_checkout_get($checkout_id);
        if ($checkout) {
            if (!empty($transaction['discount_code']) && defined('BXC_SHOP')) {
                $checkout['price'] = bxc_shop_discounts_apply($transaction['discount_code'], $checkout_id, $checkout['price']);
            }
            if ($checkout['price'] > bxc_isset($transaction, 'amount_fiat', 0) || strtolower($checkout['currency']) != strtolower($transaction['currency'])) {
                return bxc_error('Checkout amount/currency does not matach transaction amount/currency.', 'bxc_transactions_complete');
            }
            if (empty($amount) && empty($transaction['amount_fiat'])) {
                $underpayment = false;
                $query['vat'] = '0';
            }
            $checkout_title = $checkout['title'];
        }
    }
    if ($underpayment) {
        if (empty($amount_blockchain)) {
            $amount_blockchain = 0;
        }
        $note = $amount_blockchain . '/' . $amount . ' ' . strtoupper(bxc_crypto_is_fiat($cryptocurrency_code) ? $transaction['currency'] : $cryptocurrency_code) . ' ' . bxc_('received') . '. ' . bxc_decimal_number(floatval($amount) - floatval($amount_blockchain)) . ' ' . strtoupper(bxc_crypto_is_fiat($cryptocurrency_code) ? $transaction['currency'] : $cryptocurrency_code) . ' ' . bxc_('are missing.');
        $description = bxc_transactions_get_description($transaction['id']);
        if (!in_array($note, $description)) {
            array_push($description, $note);
        }
        if (bxc_settings_get('accept-underpayments') && ((abs($amount - $amount_blockchain) / (($amount + $amount_blockchain) / 2)) * 100) < 5) {
            $underpayment = false;
            $amount = $amount_blockchain;
            $query['amount'] = $amount_blockchain;
            $query['amount_fiat'] = bxc_crypto_get_fiat_value($amount_blockchain, $cryptocurrency_code, $transaction['currency']);
        } else {
            $query['description'] = $description;
        }
    }
    $query['from'] = $address_from;
    $query['hash'] = $transaction['hash'];
    $query['status'] = $underpayment ? 'X' : 'C';
    $response = bxc_transactions_update($transaction['id'], $query, false);
    if ($response === true) {
        $transaction = array_merge($transaction, $query);
    }
    if ($invoice === false) {
        $invoice = bxc_isset($transaction, 'billing') && bxc_settings_get('invoice-active') ? bxc_transactions_invoice($transaction) : false;
    }
    if (bxc_transactions_webhook_authorized($transaction) && (($cryptocurrency_code === 'btc' && bxc_settings_get('btc-node-address-generation') && bxc_settings_get('btc-node-url')) || bxc_is_eth_address_generation($cryptocurrency_code))) {
        $ethereum = $cryptocurrency_code !== 'btc';
        $prefix = $ethereum ? 'eth' : 'btc';
        $addresses = json_decode(bxc_encryption(bxc_settings_db($prefix . '-addresses'), false), true);
        for ($i = 0; $i < count($addresses); $i++) {
            $private_key = bxc_isset($addresses[$i][0], 'private_key');
            if ($private_key && $addresses[$i][0]['address'] == $transaction['to']) {
                require_once(__DIR__ . ($ethereum ? '/web3.php' : '/bitcoin.php'));
                $node_transfer = bxc_settings_get($prefix . '-node-transfer');
                $to = $node_transfer ? bxc_settings_get($prefix . '-node-transfer-address') : bxc_settings_get($prefix . '-address');
                if ($ethereum) {
                    bxc_eth_transfer($amount, $cryptocurrency_code, $to, $transaction['to'], $private_key);
                } else {
                    bxc_btc_transfer($amount, $to, $transaction['to'], $private_key);
                }
                break;
            }
        }
    }
    bxc_crypto_convert($transaction['id'], $cryptocurrency_code, $amount_blockchain);
    if (!$node_transfer) {
        bxc_crypto_transfer($transaction['id'], $cryptocurrency_code, $amount_blockchain);
    }
    if (bxc_settings_get('notifications-sale')) {
        $language = bxc_settings_get('language-admin');
        bxc_email_notification(($underpayment ? '[' . bxc_m('Underpayment', $language) . '] ' : '') . bxc_m('New payment of', $language) . ' ' . $transaction['amount_fiat'] . ' ' . strtoupper($transaction['currency']) . ($transaction['title'] ? ' | ' . $transaction['title'] : ''), str_replace('{T}', $transaction['amount_fiat'] . ' ' . strtoupper($transaction['currency']) . (bxc_crypto_is_fiat($cryptocurrency_code) ? '' : ' (' . $amount_blockchain . ' ' . strtoupper($cryptocurrency_code) . ')') . ($underpayment ? ' (<b>' . bxc_m('Underpayment', $language) . '</b>)' : ''), bxc_m('A new payment of {T} has been sent to your', $language)) . ' ' . (bxc_crypto_is_fiat($cryptocurrency_code) ? ucfirst($cryptocurrency_code) . ' ' . bxc_m('account', $language) . '. ' : ucfirst(bxc_crypto_name($transaction['cryptocurrency'])) . ' ' . bxc_m('address', $language) . ' <b>' . $transaction['to'] . '</b>. ') . bxc_m('Transaction ID:', $language) . ' ' . $transaction['id'] . '.' . ($transaction['title'] ? ' ' . bxc_m('Checkout:', $language) . ' ' . $transaction['title'] . '.' : ''));
    }
    if (strpos($external_reference, 'shopify_') === 0) {
        bxc_curl(bxc_settings_get('shopify-url') . '/admin/api/2023-01/orders/' . str_replace('shopify_', '', $external_reference) . '/transactions.json', json_encode(['transaction' => ['currency' => $transaction['currency'], 'amount' => $transaction['amount_fiat'], 'kind' => 'capture']]), ['X-Shopify-Access-Token: ' . trim(bxc_settings_get('shopify-token'))], 'POST');
    }
    if (!$underpayment) {
        $external_reference = explode('|', bxc_encryption($external_reference, false));
        $source = in_array('woo', $external_reference) ? 'woo' : (in_array('edd', $external_reference) ? 'edd' : false);
        $redirect = $source == 'woo' ? $external_reference[1] : ($source ? bxc_settings_db('wp_edd_url') : false);
        bxc_transactions_webhook($transaction, $source ? bxc_settings_db('wp_api_url') : false);
    }
    if (defined('BXC_EXCHANGE') && bxc_isset($transaction, 'type') == 3) {
        $response = bxc_exchange_finalize($transaction['external_reference'], empty($transaction['customer_id']) || bxc_verify_admin() ? false : bxc_customers_get($transaction['customer_id']));
        if (!$response[0]) {
            return bxc_error($response[1], 'bxc_transactions_complete');
        }
    } else if (defined('BXC_SHOP')) {
        $return = array_merge($return, bxc_shop_transaction_complete($transaction['customer_id'], $transaction['id'], $checkout_title));
    }
    if (BXC_CLOUD) {
        bxc_cloud_spend_credit($transaction['amount_fiat'], $transaction['currency']);
    }
    return array_merge($return, ['underpayment' => $underpayment, 'redirect' => $redirect, 'source' => $source, 'invoice' => $invoice]);
}

function bxc_transactions_update($transaction_id, $values, $complete_action = true) {
    $query = 'UPDATE bxc_transactions SET ';
    $transaction = false;

    if (is_string($values)) {
        $values = json_decode($values, true);
    }
    foreach ($values as $key => $value) {
        $query .= '`' . bxc_db_escape($key) . '` = "' . (is_string($value) || is_numeric($value) ? bxc_db_escape($value) : bxc_db_json_escape($value)) . '",';
    }
    $transaction_status = bxc_isset($values, 'status');
    if ($complete_action && $transaction_status == 'C') {
        $transaction = bxc_transactions_get($transaction_id);
        $response = bxc_transactions_complete($transaction, $transaction['amount'], $transaction['from']);
        if (bxc_isset($response, 'error') || is_string($response)) {
            return $response;
        }
    }
    if (defined('BXC_SHOP') && $transaction_status == 'R') {
        bxc_db_query('DELETE FROM bxc_license_keys WHERE transaction_id = ' . $transaction_id);
    }
    $response = bxc_db_query(substr($query, 0, -1) . ' WHERE id = ' . bxc_db_escape($transaction_id, true));
    $name = 'BXC_TRANSACTION_' . $transaction_id;
    if (isset($GLOBALS[$name])) {
        unset($GLOBALS[$name]);
        $GLOBALS[$name] = $transaction ? array_merge($transaction, $values) : bxc_transactions_get($transaction_id);
    }
    return $response;
}

function bxc_transactions_webhook($transaction, $webhook_url = false) {
    if (!$webhook_url) {
        $webhook_url = bxc_settings_get('webhook-url');
    }
    if (!$webhook_url) {
        return false;
    }
    if (is_string($transaction)) {
        $transaction = ['id' => bxc_transactions_decrypt($transaction)['id']];
    }
    $webhook_secret_key = bxc_settings_get('webhook-secret');
    $transaction = bxc_transactions_get($transaction['id']);
    if ($transaction['status'] != 'C' || !bxc_transactions_webhook_authorized($transaction)) {
        return false;
    }
    $body = json_encode(['key' => $webhook_secret_key ? $webhook_secret_key : BXC_PASSWORD, 'transaction' => $transaction]);
    bxc_transactions_update($transaction['id'], ['webhook' => 1]);
    return bxc_curl($webhook_url, $body, ['Content-Type: application/json', 'Content-Length: ' . strlen($body)], 'POST');
}

function bxc_transactions_webhook_authorized($transaction) {
    if ($transaction['webhook']) {
        $url = bxc_is_demo(true);
        if (!$url || bxc_isset($url, 'webhook_key') != bxc_settings_get('webhook-secret')) {
            return false;
        }
    }
    return true;
}

function bxc_transactions_download($search = false, $status = false, $cryptocurrency = false, $date_range = false, $checkout_id = false) {
    $header = ['ID', 'Title', 'Description', 'From', 'To', 'Hash', 'Amount', 'Amount FIAT', 'Cryptocurrency', 'Currency', 'External Reference', 'Creation Time', 'Status', 'Webhook', 'Billing', 'VAT', 'VAT details'];
    $transactions = bxc_transactions_get_all(-1, $search, $status, $cryptocurrency, $date_range, $checkout_id);
    if (defined('BXC_SHOP')) {
        $response = bxc_shop_transactions_download($transactions, $header);
        $transactions = $response[0];
        $header = $response[1];
    }
    return bxc_csv($transactions, $header, 'transactions');
}

function bxc_transactions_invoice($transaction_or_id) {
    $transaction_id = is_numeric($transaction_or_id) ? $transaction_or_id : bxc_isset($transaction_or_id, 'id');
    if (!$transaction_id) {
        return false;
    }
    $prefix = bxc_settings_get('invoice-number-prefix', 'inv-');
    $file_name = strtolower($prefix) . $transaction_id . '.pdf';
    $path_part = 'uploads' . bxc_cloud_path_part();
    if (!file_exists($path_part)) {
        mkdir($path_part, 0755, true);
    }
    $path_part .= '/' . $file_name;
    $invoice_url = BXC_URL . $path_part;
    $transaction = is_numeric($transaction_or_id) ? bxc_transactions_get($transaction_id) : $transaction_or_id;
    if (!$transaction || $transaction['status'] == 'P' || empty($transaction['billing'])) {
        return false;
    }
    require_once __DIR__ . '/vendor/fpdf/fpdf.php';
    require_once __DIR__ . '/vendor/fpdf/autoload.php';
    require_once __DIR__ . '/vendor/fpdf/Fpdi.php';
    $billing = json_decode($transaction['billing'], true);
    $billing_text = $billing ? bxc_isset($billing, 'name', '') . PHP_EOL . bxc_isset($billing, 'address', '') . PHP_EOL . bxc_isset($billing, 'city', '') . ', ' . (empty($billing['state']) ? '' : $billing['state'] . ', ') . bxc_isset($billing, 'zip', '') . PHP_EOL . bxc_isset($billing, 'country', '') . PHP_EOL . PHP_EOL . bxc_isset($billing, 'vat', '') : '';

    $pdf = new \setasign\Fpdi\Fpdi();
    $pdf->AddPage();
    $pdf->setSourceFile(__DIR__ . '/resources/invoice.pdf');
    $tpl = $pdf->importPage(1);
    $pdf->useTemplate($tpl, 0, 0, null, null);
    $pdf->SetTextColor(90, 90, 90);

    $pdf->SetXY(20, 29);
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->Cell(1000, 1, bxc_('Tax Invoice'));

    $pdf->SetXY(130, 27);
    $pdf->SetFont('Arial', '', 13);
    $pdf->Multicell(500, 7, bxc_('Invoice date: ') . date('d-m-Y') . PHP_EOL . bxc_('Purchase date: ') . date('d-m-Y', strtotime($transaction['creation_time'])) . PHP_EOL . bxc_('Invoice number: ') . strtoupper($prefix) . $transaction['id']);

    $pdf->SetXY(20, 60);
    $pdf->SetFont('Arial', 'B', 13);
    $pdf->Cell(50, 1, bxc_('To'));
    $pdf->SetFont('Arial', '', 13);
    $pdf->SetXY(20, 70);
    $pdf->Multicell(168, 7, strip_tags(trim(iconv('UTF-8', 'windows-1252', $billing_text))));

    $pdf->SetXY(130, 60);
    $pdf->SetFont('Arial', 'B', 13);
    $pdf->Cell(168, 1, bxc_('Supplier'));
    $pdf->SetFont('Arial', '', 13);
    $pdf->SetXY(130, 70);
    $pdf->Multicell(168, 7, strip_tags(trim(iconv('UTF-8', 'windows-1252', bxc_settings_get('invoice-details')))));

    $pdf->SetXY(20, 150);
    $pdf->SetFont('Arial', 'B', 13);
    $pdf->Cell(168, 1, bxc_('Purchase details'));
    $pdf->SetFont('Arial', '', 13);
    $pdf->SetXY(20, 160);
    $pdf->Cell(168, 1, $transaction['title']);

    $pdf->SetXY(20, 180);
    $pdf->SetFont('Arial', 'B', 13);
    $pdf->Cell(168, 1, bxc_('Transaction amount'));
    $pdf->SetFont('Arial', '', 13);
    $pdf->SetXY(20, 190);
    $pdf->Cell(168, 1, strtoupper($transaction['currency']) . ' ' . bxc_isset($transaction, 'amount_fiat', '0') . (empty($transaction['amount']) || !bxc_crypto_is($transaction['cryptocurrency']) ? '' : ' (' . strtoupper(bxc_crypto_get_base_code($transaction['cryptocurrency'])) . ' ' . $transaction['amount'] . ')'));
    if ($transaction['vat'] && !empty($transaction['amount_fiat'])) {
        $pdf->SetXY(20, 200);
        $pdf->Cell(100, 1, 'VAT ' . strtoupper($transaction['currency']) . ' ' . $transaction['vat']);
    }
    $pdf->Output(__DIR__ . '/' . $path_part, 'F');
    return $invoice_url;
}

function bxc_transactions_invoice_user($encrypted_transaction_id, $billing_details = false) {
    $transaction_id = bxc_encryption($encrypted_transaction_id, false);
    if ($transaction_id) {
        if ($billing_details) {
            bxc_transactions_update($transaction_id, ['billing' => is_string($billing_details) ? $billing_details : json_encode($billing_details, JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE)]);
            $path = __DIR__ . '/uploads' . bxc_cloud_path_part() . '/inv-' . $transaction_id . '.pdf';
            if (file_exists($path)) {
                unlink($path);
            }
        }
        return bxc_transactions_invoice($transaction_id);
    }
    return false;
}

function bxc_transactions_invoice_form($visible = false) {
    $code = '<div class="bxc-billing-cnt">' . ($visible ? '' : '<div id="bxc-btn-invoice" class="bxc-link bxc-underline">' . bxc_('Generate invoice?') . '</div>') . '<div id="bxc-billing" class="bxc-billing' . ($visible ? '' : ' bxc-hidden') . '">' . ($visible ? '' : '<i id="bxc-btn-invoice-close" class="bxc-icon-close bxc-btn-red bxc-btn-icon"></i>') . '<div class="bxc-title bxc-title-1">' . bxc_('Billing information') . '</div>';
    $fields = [['Full name', 'name'], ['Address', 'address'], ['City', 'city'], ['State', 'state'], ['ZIP code', 'zip'], ['Country', 'country'], ['VAT', 'vat']];
    for ($i = 0; $i < count($fields); $i++) {
        if ($fields[$i][1] == 'country') {
            $code .= '<div class="bxc-input"><span>' . bxc_('Country') . '</span><select name="country"><option></option>' . bxc_select_countries() . '</select></div>';
        } else {
            $code .= '<div class="bxc-input"><span>' . bxc_($fields[$i][0]) . '</span><input name="' . $fields[$i][1] . '" type="text" /></div>';
        }
    }
    return $code . ($visible ? '' : '<div class="bxc-title bxc-title-2">' . bxc_('Payment') . '</div>') . '</div></div>';
}

function bxc_select_countries() {
    $countries = json_decode(file_get_contents(__DIR__ . '/resources/countries.json'), true);
    $code = '';
    foreach ($countries as $key => $country_code) {
        $code .= '<option value="' . $key . '" data-country-code="' . $country_code . '">' . bxc_($key) . '</option>';
    }
    return $code;
}

function bxc_transactions_decrypt($transaction) {
    if (is_string($transaction)) {
        return json_decode(bxc_encryption($transaction, false), true);
    }
    if (!bxc_verify_admin()) {
        bxc_error('security-error', 'bxc_transactions_decrypt');
        return 'security-error';
    }
    return $transaction;
}

function bxc_payment_link($transaction_id) {
    bxc_transactions_update($transaction_id, ['creation_time' => gmdate('Y-m-d H:i:s')]);
    $url = bxc_get_url('id') . bxc_encryption($transaction_id);
    return $url . bxc_cloud_url_part(!strpos($url, '?'));
}

function bxc_transactions_get_description($transaction_id) {
    $description = json_decode(bxc_isset(bxc_db_get('SELECT description FROM bxc_transactions WHERE id = ' . bxc_db_escape($transaction_id, true)), 'description', '[]'), true);
    return $description ? $description : [];
}

function bxc_transactions_cancel($transaction) {
    $transaction = bxc_transactions_decrypt($transaction);
    $transaction_id = bxc_db_escape(bxc_isset($transaction, 'id'), true);
    if ($transaction_id) {
        bxc_db_query('DELETE FROM bxc_transactions WHERE id = ' . $transaction_id);
        $cryptocurrency_code = $transaction['cryptocurrency'];
        $slug = $cryptocurrency_code . '-manual-addresses';
        $addresses = json_decode(bxc_settings_db($slug, false, '{}'), true);
        $to = bxc_isset($transaction, 'to');
        if ($to) {
            unset($addresses[$to]);
            bxc_settings_db($slug, $addresses);
        }
        if ($cryptocurrency_code == 'btc' || bxc_crypto_get_network($cryptocurrency_code) == 'eth') {
            $slug = bxc_crypto_get_network($cryptocurrency_code) . '-addresses' . ($cryptocurrency_code == 'btc' && bxc_settings_get('btc-node-xpub') ? '-xpub' : '');
            $addresses = json_decode(bxc_encryption(bxc_settings_db($slug), false), true);
            if ($addresses) {
                for ($i = 0; $i < count($addresses); $i++) {
                    if ($addresses[$i][0]['address'] == $to) {
                        $addresses[$i][0]['address'] = '';
                        bxc_settings_db($slug, json_encode(bxc_encryption($addresses)));
                        break;
                    }
                }
            }
        }
        if (defined('BXC_SHOP')) {
            bxc_db_query('DELETE FROM bxc_license_keys WHERE transaction_id = ' . $transaction_id);
        }
        return true;
    }
    return false;
}

function bxc_transactions_refund($transaction_id) {
    $transaction = bxc_transactions_get($transaction_id);
    $status = ['transaction-not-found', 'Transaction not found.'];
    if ($transaction) {
        if (in_array($transaction['status'], ['C', 'X'])) {
            if ($transaction['hash']) {
                $cryptocurrency_code = $transaction['cryptocurrency'];
                $transaction_blockchain = bxc_blockchain($cryptocurrency_code, 'transaction', $transaction['hash'], $transaction['to']);
                $address = bxc_isset($transaction_blockchain, 'address');
                if ($address) {
                    if ($transaction_blockchain['value'] === $transaction['amount'] && $address === $transaction['from']) {
                        $status = ['refunds-not-enabled', 'Refunds not enabled.'];
                        if (bxc_settings_get('btc-node-refunds') && $cryptocurrency_code == 'btc') {
                            require_once(__DIR__ . '/bitcoin.php');
                            $response = bxc_btc_transfer($transaction_blockchain['value'], $address);
                            if (is_string($response)) {
                                $status = [true, str_replace('{R}', '<a href="#" data-hash="' . $response . '" target="_blank">' . bxc_('here') . '</a>', bxc_('Refund sent. Transaction details {R}.'))];
                            } else if ($response['error']) {
                                $status = ['bitcoin-error', bxc_isset($response['error'], 'message', $response['error'])];
                            }
                        } else if (bxc_settings_get('eth-node-refunds') && in_array($cryptocurrency_code, bxc_get_cryptocurrency_codes('eth'))) {
                            require_once(__DIR__ . '/web3.php');
                            $response = bxc_eth_transfer($transaction_blockchain['value'], $cryptocurrency_code, $address);
                            if (is_string($response)) {
                                $status = [true, str_replace('{R}', '<a href="#" data-hash="' . $response . '" target="_blank">' . bxc_('here') . '</a>', bxc_('Refund sent. Transaction details {R}.'))];
                            } else if ($response['error']) {
                                $status = ['ethereum-error', bxc_isset($response['error'], 'message', $response['error'])];
                            }
                        } else if (bxc_settings_get('coinbase-refunds')) {
                            $account = bxc_coinbase_get_accounts($cryptocurrency_code);
                            if ($account) {
                                $response = bxc_coinbase_curl('/v2/accounts/' . $account['id'] . '/transactions', ['to' => $address, 'amount' => $transaction_blockchain['value'], 'currency' => $cryptocurrency_code, 'type' => 'send']);
                                if (bxc_isset(bxc_isset($response, 'data', []), 'status') == 'pending') {
                                    $status = [true, str_replace('{R}', '<a href="https://www.coinbase.com' . str_replace('/v2', '', $response['data']['resource_path']) . '" target="_blank">' . bxc_('here') . '</a>', bxc_('Refund sent. Transaction details {R}.'))];
                                } else {
                                    $status = ['coinbase-error', isset($response['errors']) ? $response['errors'][0]['message'] : json_encode($response)];
                                }
                            } else {
                                $status = ['unsupported-cryptocurrency', 'Cryptocurrency not supported.'];
                            }
                        }
                    } else {
                        $status = ['invalid-amount', 'Invalid amount or address.'];
                    }
                } else {
                    $status = ['sender-address-not-found', 'Sender address not found.'];
                }
            } else {
                $status = ['hash-not-found', 'Transaction hash not found.'];
            }
        } else {
            $status = ['wrong-transaction-status', 'Incorrect transaction status. Only transactions marked as completed or underpaid can be refunded.'];
        }
    }
    if ($status[0] === true) {
        $description = bxc_transactions_get_description($transaction_id);
        array_push($description, str_replace('{R}', date('Y-m-d H:i:s'), bxc_('Refund sent on {R}. Transaction hash: ')) . $response);
        bxc_transactions_update($transaction_id, ['status' => 'R', 'description' => $description]);
    }
    return ['status' => $status[0], 'message' => bxc_($status[1])];
}

function bxc_transactions_delete($transaction_id) {
    return bxc_transactions_cancel(bxc_encryption(json_encode(bxc_transactions_get($transaction_id))));
}

/*
 * -----------------------------------------------------------
 * CHECKOUT
 * -----------------------------------------------------------
 *
 * 1. Return all checkouts or the specified one
 * 2. Save a checkout
 * 3. Delete a checkout
 * 4. Direct payment checkout
 *
 */

function bxc_checkout_get($checkout_id = false) {
    global $BXC_CHECKOUTS;
    if ($BXC_CHECKOUTS) {
        return $BXC_CHECKOUTS;
    }
    $response = bxc_db_get('SELECT * FROM bxc_checkouts' . ($checkout_id ? ' WHERE ' . (is_numeric($checkout_id) ? 'id' : 'slug') . ' = "' . bxc_db_escape($checkout_id) . '"' : ''), $checkout_id);
    $BXC_CHECKOUTS = defined('BXC_SHOP') && count($response) ? bxc_shop_checkout_get_part($response) : $response;
    return $BXC_CHECKOUTS;
}

function bxc_checkout_save($checkout) {
    $checkout = json_decode($checkout, true);
    $is_new = empty($checkout['id']);
    if (empty($checkout['currency'])) {
        $checkout['currency'] = bxc_settings_get('currency', 'USD');
    }
    $shop = defined('BXC_SHOP') ? bxc_shop_checkout_save_part($checkout, $is_new) : ($is_new ? ['', ''] : '');
    if ($is_new) {
        return bxc_db_query('INSERT INTO bxc_checkouts(title, description, price, currency, type, redirect, hide_title, external_reference, creation_time, slug' . $shop[0] . ') VALUES ("' . bxc_db_escape($checkout['title']) . '", "' . bxc_db_escape(bxc_isset($checkout, 'description', '')) . '", "' . bxc_db_escape($checkout['price'], true) . '", "' . bxc_db_escape(bxc_isset($checkout, 'currency', '')) . '", "' . bxc_db_escape($checkout['type']) . '", "' . bxc_db_escape(bxc_isset($checkout, 'redirect', '')) . '", ' . (empty($checkout['hide_title']) ? 0 : 1) . ', "' . bxc_db_escape(bxc_isset($checkout, 'external_reference', '')) . '", "' . gmdate('Y-m-d H:i:s') . '", "' . bxc_db_escape(bxc_isset($checkout, 'slug')) . '"' . $shop[1] . ')', true);
    } else {
        return bxc_db_query('UPDATE bxc_checkouts SET title = "' . bxc_db_escape($checkout['title']) . '", description = "' . bxc_db_escape(bxc_isset($checkout, 'description', '')) . '", price = "' . bxc_db_escape($checkout['price'], true) . '", currency = "' . bxc_db_escape(bxc_isset($checkout, 'currency', '')) . '", type = "' . bxc_db_escape($checkout['type']) . '", redirect = "' . bxc_db_escape(bxc_isset($checkout, 'redirect', '')) . '", hide_title = ' . (empty($checkout['hide_title']) ? 0 : 1) . ', external_reference = "' . bxc_db_escape(bxc_isset($checkout, 'external_reference', '')) . '", slug = "' . bxc_db_escape(bxc_isset($checkout, 'slug', '')) . '"' . $shop . ' WHERE id = "' . bxc_db_escape($checkout['id'], true) . '"');
    }
}

function bxc_checkout_delete($checkout_id) {
    $checkout = bxc_checkout_get($checkout_id);
    $response = bxc_db_query('DELETE FROM bxc_checkouts WHERE id = "' . bxc_db_escape($checkout_id) . '"');
    if ($response === true) {
        $downloads = bxc_isset($checkout, 'downloads', []);
        for ($i = 0; $i < count($downloads); $i++) {
            bxc_file_delete($downloads[$i], 'checkout/');
        }
        if (!empty($checkout['image'])) {
            bxc_file_delete($checkout['image']);
        }
    }
    return $response;
}

function bxc_checkout_direct() {
    if (isset($_GET['checkout_id'])) {
        echo '<div data-bxc="' . bxc_sanatize_string($_GET['checkout_id']) . '" data-price="' . bxc_sanatize_string(bxc_isset($_GET, 'price')) . '" data-external-reference="' . bxc_sanatize_string(bxc_isset($_GET, 'external_reference', bxc_isset($_GET, 'external-reference', ''))) . '" data-redirect="' . bxc_sanatize_string(bxc_isset($_GET, 'redirect', '')) . '" data-currency="' . bxc_sanatize_string(bxc_isset($_GET, 'currency', '')) . '"' . (isset($_GET['title']) ? ' data-title="' . bxc_sanatize_string($_GET['title']) . '"' : '') . (isset($_GET['description']) ? ' data-description="' . bxc_sanatize_string($_GET['description']) . '"' : '') . (isset($_GET['note']) ? ' data-note="' . bxc_sanatize_string($_GET['note']) . '"' : '') . '>';
        require_once(__DIR__ . '/init.php');
        echo '</div>';
    }
}

function bxc_checkout_url($amount, $cryptocurrency_code, $currency_code, $checkout_url, $transaction_id, $title = '', $description = '') {
    return $cryptocurrency_code == 'verifone' ? bxc_verifone_create_checkout($amount, $checkout_url, $transaction_id, $title, $currency_code) : ($cryptocurrency_code == 'stripe' ? bxc_stripe_payment(floatval($amount) * 100, $checkout_url, $transaction_id, $currency_code, $description) : bxc_paypal_get_checkout_url($transaction_id, $checkout_url, $amount, $currency_code, $title));
}

/*
 * -----------------------------------------------------------
 * CUSTOMERS
 * -----------------------------------------------------------
 *
 * 1. Add a new customer
 * 2. Return a customer
 * 3. Return the username of a customer
 *
 */

function bxc_customers_add($first_name = false, $last_name = false, $email = false, $phone = false, $country = false, $country_code = false, $extra = false) {
    return bxc_db_query('INSERT INTO bxc_customers(first_name, last_name, email, phone, country, country_code, creation_time, extra) VALUES ("' . ($first_name ? bxc_db_escape($first_name) : '') . '", "' . ($last_name ? bxc_db_escape($last_name) : '') . '", ' . ($email ? '"' . bxc_db_escape($email) . '"' : 'NULL') . ', "' . ($phone ? bxc_db_escape($phone) : '') . '", "' . ($country ? bxc_db_escape($country) : '') . '", "' . ($country_code ? bxc_db_escape($country_code) : '') . '", "' . gmdate('Y-m-d H:i:s') . '", "' . ($extra ? bxc_db_escape($extra) : '') . '")', true);
}

function bxc_customers_get($customer_id_or_email = false) {
    $is_id = is_numeric($customer_id_or_email);
    return bxc_db_get('SELECT * FROM bxc_customers' . ($customer_id_or_email ? ' WHERE ' . ($is_id ? 'id' : 'email') . ' = "' . bxc_db_escape($customer_id_or_email, $is_id) . '"' : ''), $customer_id_or_email);
}

function bxc_customers_username($user) {
    return trim(bxc_isset($user, 'first_name', '') . ' ' . bxc_isset($user, 'last_name', ''));
}

/*
 * -----------------------------------------------------------
 * CRYPTO
 * -----------------------------------------------------------
 *
 * 1. Get balances
 * 2. Get the API key
 * 3. Get the fiat value of a cryptocurrency value
 * 4. Get the cryptocurrency value of a fiat value
 * 5.
 * 6. Get cryptocurrency name
 * 7. Get the crypto payment address
 * 8. Get USD exchange rate
 * 9. Get exchange rate
 * 10. Convert to FIAT
 * 11. Transfer cryptocurrencies
 * 12. Get crypto network
 * 13. Return the base cryptocurrency code of a token
 * 14. Verify an address
 * 15. Get all custom tokens
 * 16. Get cryptocurrency codes by blockchain
 * 17. Get decimals of a cryptocurrency
 * 18. Get the amount in the correct decimal length
 * 19. Check if a currency is a cryptocurrency
 * 20. Check if the string is from a FIAT provider
 * 21. Check if a cryptocurrency code is a custom token
 * 22. Get the cryptocurrency logo of the specified cryptocurrency code
 * 23. Validate an address
 * 24. Return the external explorer link of a transaction
 * 25. Return the blockchain fee
 *
 */

function bxc_crypto_balances($cryptocurrency_code = false) {
    $cryptocurrencies = $cryptocurrency_code ? [$cryptocurrency_code] : ['btc', 'eth', 'usdt', 'usdt_tron', 'usdt_bsc', 'usdc', 'ltc', 'sol', 'xrp', 'doge', 'busd', 'bnb', 'shib', 'link', 'bat', 'algo', 'bch', 'xmr'];
    $currency = bxc_settings_get('currency', 'USD');
    $response = ['balances' => []];
    $total = 0;
    $custom_token_images = [];
    if (!$cryptocurrency_code) {
        $custom_tokens = bxc_get_custom_tokens();
        foreach ($custom_tokens as $key => $value) {
            array_push($cryptocurrencies, $value['code']);
            $custom_token_images[$key] = $value['img'];
        }
    }
    for ($i = 0; $i < count($cryptocurrencies); $i++) {
        $cryptocurrency_code = $cryptocurrencies[$i];
        $addresses = bxc_settings_get_address($cryptocurrency_code, false);
        if (($cryptocurrency_code === 'btc' && bxc_settings_get('btc-node-address-generation')) || bxc_is_eth_address_generation($cryptocurrency_code)) {
            $ethereum = $cryptocurrency_code != 'btc';
            $slug = ($ethereum ? 'eth' : 'btc') . '-addresses' . (!$ethereum && bxc_settings_get('btc-node-xpub') ? '-xpub' : '');
            $addresses_db = bxc_settings_db($slug);
            if ($addresses_db) {
                $addresses = array_merge(json_decode(bxc_encryption($addresses_db, false), true), $addresses);
            }
        }
        if ($addresses) {
            $balance = 0;
            $fiat = 0;
            for ($j = 0; $j < count($addresses); $j++) {
                $balance_blockchain = bxc_blockchain($cryptocurrency_code, 'balance', false, $addresses[$j]);
                if ($balance_blockchain && is_numeric($balance_blockchain)) {
                    $fiat_blockchain = bxc_crypto_get_fiat_value($balance_blockchain, bxc_crypto_get_base_code($cryptocurrency_code), $currency);
                    $total += $fiat_blockchain;
                    $balance += $balance_blockchain;
                    $fiat += $fiat_blockchain;
                }
            }
            $response['balances'][$cryptocurrency_code] = ['amount' => $balance, 'fiat' => round($fiat, 2), 'name' => bxc_crypto_name($cryptocurrency_code, true)];
        }
    }
    $response['total'] = round($total, 2);
    $response['currency'] = strtoupper($currency);
    $response['token_images'] = $custom_token_images;
    return $response;
}

function bxc_crypto_api_key($service, $url = false) {
    $key = false;
    $key_parameter = false;
    switch ($service) {
        case 'etherscan':
            $keys = ['TBGQBHIXM113HT94ZWYY8MXGWFP9257541', 'GHAQC5VG536H7MSZR5PZF27GZJUSGH94TK', 'F1HZ35IJCR8DQC4SGVJBYMYB928UFV58MP', 'ADR46A53KIXDJ6BMJYK5EEGKQJDDQH6H1K', 'AIJ9S76757JZ7B9KQMJTAN3SRNKF5F5P4M'];
            $key_parameter = 'apikey';
            break;
        case 'ethplorer':
            $keys = ['EK-feNiM-th8gYm7-qECAq', 'EK-qCQHY-co6TwoA-ASWUm', 'EK-51EKh-8cvKWm5-qhjuU', 'EK-wmJ14-faiQNhf-C5Gsj', 'EK-i6f3K-1BtBfUf-Ud7Lo'];
            $key_parameter = 'apiKey';
            break;
        case 'bscscan':
            $keys = ['2Z5V3AZV5P4K95M9UXPABQ19CAVWR7RM78', '6JG8B7F5CC5APF2Q1C3BXRMZSS92F1RGKX', '2BAPYF16Z6BR8TY2SZGN74231JNZ8TFQKU', '1DNAQ7C2UAYPS5WW7HQXPCF8WFYG8CP3XQ', 'MP3XAXN1D7XVYZQVNCMGII5JZTBRASG996'];
            $key_parameter = 'apiKey';
            break;
        case 'blockdaemon':
            $keys = ['5inALCDK3NzmSoA-EC4ribZEDAvj0zy95tPaorxMZYzTRR0u', 'i1-LMC4x9ZgSlZ-kSrCf3pEeckZadAsKCJxuvXRq9pusgK2T', 'ktbzuPccKUwnnMI73YLEK7h29dEOQfFBOCNAXJ0SnHw8rn69', 'FI2b6Cfpf8lee2xaTs98IprkPb1OuxjW11M2Sq-vlIrqzKsR', '1nvtfBzPsjByQPYBr0xoxc1jv9KrntMnOhkjKTkTt3ejxUXk'];
            $key_parameter = '-';
            break;
        case 'tatum':
            $keys = ['90a07172-cd5e-452e-9b81-56f37c9693bb', '573c3fea-8325-4088-a35e-e97fdf2bc365', '330c5774-0de7-4963-895f-2b0c784011d2', '2f9a0a5f-f587-4545-8c38-f72007461e7a', '076a59f5-7cb5-4169-a038-3decda950b41'];
            $key_parameter = '-';
            break;
    }
    if ($key_parameter) {
        $key = bxc_settings_get($service . '-key');
        if (!$key)
            $key = $keys[rand(0, 4)];
    }
    return $key ? ($url ? ($url . (strpos($url, '?') ? '&' : '?') . $key_parameter . '=' . $key) : $key) : ($url ? $url : false);
}

function bxc_crypto_get_fiat_value($amount, $cryptocurrency_code, $currency_code) {
    if (!is_numeric($amount)) {
        return bxc_error('Invalid amount (' . $amount . ')', 'bxc_crypto_get_fiat_value');
    }
    $cryptocurrency_code = strtoupper(bxc_crypto_get_base_code($cryptocurrency_code));
    $unsupported = ['BNB', 'BUSD'];
    if (in_array($cryptocurrency_code, $unsupported)) {
        $usd_rates = $currency_code == 'USD' ? 1 : bxc_usd_rates($currency_code);
        $crypto_rate_usd = json_decode(bxc_curl('https://www.binance.com/api/v3/ticker/price?symbol=' . $cryptocurrency_code . 'USDT'), true)['price'];
        $rate = 1 / (floatval($crypto_rate_usd) * $usd_rates);
    } else {
        $rate = bxc_exchange_rates($currency_code, $cryptocurrency_code);
    }
    return round((1 / $rate) * floatval($amount), 2);
}

function bxc_crypto_get_cryptocurrency_value($amount, $cryptocurrency_code, $currency_code) {
    $unsupported = ['BNB', 'BUSD'];
    $cryptocurrency_code = strtoupper(bxc_crypto_get_base_code($cryptocurrency_code));
    $rate = false;
    $is_crypto = bxc_crypto_is($currency_code);
    if (!$is_crypto && in_array($cryptocurrency_code, $unsupported)) {
        $usd_rates = $currency_code == 'USD' ? 1 : bxc_usd_rates($currency_code);
        $crypto_rate_usd = json_decode(bxc_curl('https://www.binance.com/api/v3/ticker/price?symbol=' . $cryptocurrency_code . 'USDT'), true)['price'];
        $rate = 1 / (floatval($crypto_rate_usd) * $usd_rates);
    } else if ($is_crypto) {
        $rate = bxc_exchange_rates('usd', $cryptocurrency_code) / bxc_exchange_rates('usd', $currency_code);
    } else {
        $rate = bxc_exchange_rates($currency_code, $cryptocurrency_code);
    }
    $response = bxc_crypto_get_value_with_decimals(bxc_decimal_number($rate * floatval($amount)), $cryptocurrency_code);
    return $response ? $response : 0;
}

function bxc_crypto_name($cryptocurrency_code = false, $uppercase = false) {
    $names = ['btc' => ['bitcoin', 'Bitcoin'], 'btc_ln' => ['bitcoinlightningnetwork', 'Bitcoin Lightning Network'], 'eth' => ['ethereum', 'Ethereum'], 'xrp' => ['xrp', 'XRP'], 'doge' => ['dogecoin', 'Dogecoin'], 'algo' => ['algorand', 'Algorand'], 'usdt' => ['tether', 'Tether'], 'usdt_tron' => ['tether', 'Tether'], 'usdt_bsc' => ['tether', 'Tether'], 'usdc' => ['usdcoin', 'USD Coin'], 'link' => ['chainlink', 'Chainlink'], 'shib' => ['shibainu', 'Shiba Inu'], 'bat' => ['basicattentiontoken', 'Basic Attention Token'], 'busd' => ['binanceusd', 'Binance USD'], 'bnb' => ['bnb', 'BNB'], 'ltc' => ['litecoin', 'Litecoin'], 'bch' => ['bitcoincash', 'Bitcoin Cash'], 'trx' => ['tron', 'Tron'], 'bsc' => ['binancechain', 'Binance Chain'], 'sol' => ['solana', 'Solana'], 'xmr' => ['monero', 'Monero']];
    $custom_tokens = bxc_get_custom_tokens();
    foreach ($custom_tokens as $key => $value) {
        $names[strtolower($key)] = [strtolower($value['name']), $value['name']];
    }
    return $cryptocurrency_code ? $names[strtolower($cryptocurrency_code)][$uppercase] : $names;
}

function bxc_crypto_get_address($cryptocurrency_code) {
    $address = false;
    $cryptocurrency_name = bxc_crypto_name($cryptocurrency_code);
    $cryptocurrency_code = strtolower($cryptocurrency_code);
    $stop_reusing_addresses = bxc_settings_get('stop-reusing-addresses');
    $addresses_count_xpub = false;
    if (($cryptocurrency_code === 'btc' && bxc_settings_get('btc-node-address-generation')) || bxc_is_eth_address_generation($cryptocurrency_code)) {
        $ethereum = $cryptocurrency_code != 'btc';
        $slug = ($ethereum ? 'eth' : 'btc') . '-addresses' . (!$ethereum && bxc_settings_get('btc-node-xpub') ? '-xpub' : '');
        $addresses = bxc_settings_db($slug);
        $addresses_count = 0;
        if ($addresses) {
            $addresses = json_decode(bxc_encryption($addresses, false), true);
            $addresses_count = count($addresses);
            $now_less_24h = time() - 86000;
            for ($i = 0; $i < $addresses_count; $i++) {
                $address_temp = bxc_isset($addresses[$i][0], 'address');
                if ($address_temp) {
                    if (!$stop_reusing_addresses && $addresses[$i][1] < $now_less_24h) {
                        $address = $address_temp;
                        $addresses[$i][1] = time();
                        break;
                    }
                } else {
                    $addresses_count_xpub = $i;
                }
            }
        } else {
            $addresses = [];
        }
        if (!$address) {
            require_once(__DIR__ . ($ethereum ? '/web3.php' : '/bitcoin.php'));
            if ($ethereum) {
                if (bxc_settings_get('eth-node-url')) {
                    $address = bxc_eth_generate_address();
                }
            } else if (bxc_settings_get('btc-node-xpub') && !bxc_settings_get('btc-node-transfer')) {
                $address = bxc_settings_get('btc-node-address-generation-method') === 'node' && bxc_settings_get('btc-node-url') ? bxc_btc_generate_address_xpub_node(false, [$addresses_count, $addresses_count]) : bxc_btc_generate_address_xpub(false, [$addresses_count, $addresses_count]);
            } else if (bxc_settings_get('btc-node-url')) {
                $address = bxc_btc_generate_address();
            }
            if ($address && isset($address['address'])) {
                if ($addresses_count_xpub) {
                    $addresses[$addresses_count_xpub] = [$address, time()];
                } else {
                    array_push($addresses, [$address, time()]);
                }
                $address = $address['address'];
            } else {
                bxc_error(is_array($address) ? json_encode($address) : $address, 'bxc_crypto_get_address');
            }
        }
        if ($address) {
            bxc_settings_db($slug, json_encode(bxc_encryption($addresses)));
        }
    }
    if (!$address && bxc_settings_get('custom-explorer-active')) {
        $data = bxc_curl(str_replace(['{N}', '{N2}'], [$cryptocurrency_code, $cryptocurrency_name], bxc_settings_get('custom-explorer-address')));
        $data = bxc_get_array_value_by_path(bxc_settings_get('custom-explorer-address-path'), json_decode($data, true));
        if ($data) {
            $address = $data;
        }
    }
    if (!$address) {
        $addresses = bxc_settings_get_address($cryptocurrency_code, false);
        $addresses_count = count($addresses);
        $addresses_db = json_decode(bxc_settings_db($cryptocurrency_code . '-manual-addresses', false, '{}'), true);
        if ($addresses_count > 2) {
            $now_less_24h = time() - 86000;
            for ($i = 1; $i < $addresses_count; $i++) {
                if (bxc_isset($addresses_db, $addresses[$i]) < $now_less_24h && (!$stop_reusing_addresses || !isset($addresses_db[$addresses[$i]]))) {
                    if (bxc_crypto_whitelist_invalid($addresses[$i], false, $cryptocurrency_code)) {
                        return bxc_error('whitelist-invalid', 'bxc_crypto_get_address', true);
                    }
                    $address = $addresses[$i];
                    $addresses_db[$address] = time();
                    break;
                }
            }
            if ($address) {
                bxc_settings_db($cryptocurrency_code . '-manual-addresses', $addresses_db);
            }
        }
    }
    if (!$address && bxc_settings_get('gemini-address-generation')) {
        $data = bxc_gemini_curl('deposit/' . $cryptocurrency_name . '/newAddress');
        $address = bxc_isset($data, 'address');
        if (bxc_isset($data, 'result') === 'error') {
            bxc_error($data['message'], 'bxc_crypto_get_address');
        }
    }
    if (!$address && bxc_settings_get('coinbase-address-generation')) {
        $account = bxc_coinbase_get_accounts($cryptocurrency_code);
        if ($account) {
            $data = bxc_coinbase_curl('/v2/accounts/' . $account['uuid'] . '/addresses');
            $address = bxc_isset(bxc_isset($data, 'data'), 'address');
            if (isset($data['error'])) {
                bxc_error($data['errors'][0]['message'], 'bxc_crypto_get_address');
            }
        }
    }
    if ($address) {
        $pos = strpos($address, ':');
        return $pos ? substr($address, $pos + 1) : $address;
    }
    return bxc_settings_get_address($cryptocurrency_code);
}

function bxc_usd_rates($currency_code = false) {
    $fiat_rates = json_decode(bxc_settings_db('fiat_rates'), true);
    if (!$fiat_rates || $fiat_rates[0] < (time() - 3600)) {
        $app_id = BXC_CLOUD ? OPEN_EXCHANGE_RATE_APP_ID : bxc_settings_get('openexchangerates-app-id');
        $error = '';
        if (!$app_id) {
            $app_ids = ['ce46867e51c9432ea6d36fae9537c3da', '99a38f6aeef64c23a9f7bc4395ed3951', '96558a544c9d48d0a79c84deeac3db3c', 'ccc588ffb72646fe943c30f8d1541774', 'dfd024988aff4eb9b1c07755848811b3'];
            $app_id = $app_ids[rand(0, 4)];
            $error = 'Missing Open Exchange Rates App ID. Set it in the Boxcoin settings area. ';
        }
        $json = bxc_curl('https://openexchangerates.org/api/latest.json?app_id=' . $app_id);
        $fiat_rates = bxc_isset(json_decode($json, true), 'rates');
        if ($fiat_rates) {
            bxc_settings_db('fiat_rates', [time(), json_encode($fiat_rates)]);
        } else {
            return bxc_error($error . 'Error: ' . $json, 'bxc_usd_rates', true);
        }
    } else {
        $fiat_rates = json_decode($fiat_rates[1], true);
    }
    return $currency_code ? $fiat_rates[strtoupper($currency_code)] : $fiat_rates;
}

function bxc_exchange_rates($currency_code, $cryptocurrency_code) {
    global $BXC_EXCHANGE_RATE;
    $custom_tokens = bxc_get_custom_tokens();
    $currency_code = strtoupper(bxc_crypto_is($currency_code) ? bxc_crypto_get_base_code($currency_code) : $currency_code);
    if ($custom_tokens && isset($custom_tokens[strtolower($cryptocurrency_code)])) {
        $rate = $custom_tokens[strtolower($cryptocurrency_code)]['rate'];
        if ($rate) {
            return $rate;
        }
    }
    $cryptocurrency_code = strtoupper(bxc_crypto_get_base_code($cryptocurrency_code));
    if (empty($BXC_EXCHANGE_RATE)) {
        $BXC_EXCHANGE_RATE = [];
    }
    for ($i = 0; $i < 4; $i++) {
        if (empty($BXC_EXCHANGE_RATE) || empty($BXC_EXCHANGE_RATE[$cryptocurrency_code]) || $BXC_EXCHANGE_RATE['currency_code'] != $currency_code) {
            switch ($i) {
                case 0:
                    $response = json_decode(bxc_curl('https://api.coinbase.com/v2/exchange-rates?currency=' . $currency_code), true);
                    if ($response) {
                        if (isset($response['errors'])) {
                            bxc_error($response['errors'][0]['message'], 'bxc_exchange_rates', true);
                        } else if (isset($response['data'])) {
                            $BXC_EXCHANGE_RATE = $response['data']['rates'];
                        }
                    }
                    break;
                case 1:
                    $response = json_decode(bxc_curl('https://www.binance.com/api/v3/ticker/price?symbol=' . $cryptocurrency_code . ($currency_code == 'USD' ? 'USDT' : $currency_code)), true);
                    $response = bxc_isset($response, 'price');
                    if ($response) {
                        $BXC_EXCHANGE_RATE[$cryptocurrency_code] = 1 / $response;
                    }
                    break;
                case 2:
                    $cryptocurrency_code_2 = strtolower(str_replace(' ', '-', bxc_crypto_name($cryptocurrency_code, true)));
                    $response = json_decode(bxc_curl('https://api.coingecko.com/api/v3/simple/price?ids=' . $cryptocurrency_code_2 . '&vs_currencies=' . $currency_code), true);
                    $BXC_EXCHANGE_RATE[$cryptocurrency_code] = bxc_isset(bxc_isset($response, $cryptocurrency_code_2), strtolower($currency_code));
                    break;
                case 3:
                    $response = json_decode(bxc_curl('https://www.bitstamp.net/api/v2/ticker/' . strtolower($cryptocurrency_code . $currency_code)), true);
                    $BXC_EXCHANGE_RATE[$cryptocurrency_code] = bxc_isset($response, 'last');
                    break;
            }
            $BXC_EXCHANGE_RATE['currency_code'] = $currency_code;
        } else {
            break;
        }
    }
    return floatval($BXC_EXCHANGE_RATE[$cryptocurrency_code]);
}

function bxc_crypto_convert($transaction_id, $cryptocurrency_code, $amount) {
    $response = false;
    $success = false;
    $ethereum = in_array($cryptocurrency_code, bxc_get_cryptocurrency_codes('eth')) && bxc_settings_get('eth-node-conversion');
    $cryptocurrency_code_uppercase = strtoupper($cryptocurrency_code);
    if ($ethereum) {
        require_once(__DIR__ . '/web3.php');
        $history = json_decode(bxc_settings_db('fiat_conversion', false, '[]'), true);
        if (!in_array($transaction_id, $history)) {
            $response = bxc_eth_swap($amount, $cryptocurrency_code);
            $success = substr($response, 0, 2) == '0x';
        }
    }
    if (!$success) {
        $gemini = bxc_settings_get('gemini-conversion');
        $coinbase = bxc_settings_get('coinbase-conversion');
        if ($gemini || $coinbase) {
            $history = json_decode(bxc_settings_db('fiat_conversion', false, '[]'), true);
            if (!in_array($transaction_id, $history)) {
                array_push($history, $transaction_id);
                if ($gemini) {
                    $response = bxc_gemini_convert_to_fiat($cryptocurrency_code, $amount);
                }
                if ($response == false && $coinbase) {
                    $currency_code = bxc_coinbase_get_account_fiat_currency_code();
                    $response = bxc_coinbase_curl('/api/v3/brokerage/orders', ['side' => 'SELL', 'client_order_id' => bxc_random_string(), 'product_id' => $cryptocurrency_code_uppercase . '-' . $currency_code, 'order_configuration' => ['limit_limit_gtc' => ['base_size' => $amount, 'limit_price' => (string) round(bxc_exchange_rates($cryptocurrency_code, $currency_code) * 0.9, 2)]]]);
                }
                $success = ($coinbase && bxc_isset(bxc_isset($response, 'data', []), 'status') == 'created') || ($gemini && bxc_isset($response, 'order_id'));
            }
        }
    }
    if ($success) {
        array_push($history, $transaction_id);
    }
    if ($ethereum || $gemini || $coinbase) {
        bxc_settings_db('fiat_conversion', $history);
        if (bxc_settings_get('notifications-conversion')) {
            $language = bxc_settings_get('language-admin');
            $value = $amount . ' ' . $cryptocurrency_code_uppercase;
            $subject = $value . ' ' . bxc_m('converted', $language);
            $name = $ethereum ? 'Ethereum' : ($gemini ? 'Gemini' : 'Coinbase');
            $message = str_replace(['{T}', '{T2}'], [$value, $name], bxc_m('{T} were converted to ' . strtoupper($ethereum ? bxc_settings_get('eth-node-conversion-currency') : ($gemini ? bxc_settings_get('gemini-conversion-currency') : 'FIAT')) . ' through {T2}.', $language));
            if (!$success) {
                $subject = 'The conversion of ' . $value . ' failed';
                $message = 'The conversion of ' . $value . ' through ' . $name . ' failed. Response details: <br><br>' . json_encode($response);
            }
            bxc_email_notification($subject, $message);
        }
    }
    return $response;
}

function bxc_crypto_transfer($transaction_id, $cryptocurrency_code, $amount) {
    $response = false;
    $success = false;
    $cryptocurrency_code = strtolower($cryptocurrency_code);
    $ethereum = in_array($cryptocurrency_code, bxc_get_cryptocurrency_codes('eth')) && bxc_settings_get('eth-node-transfer') && !bxc_settings_get('eth-node-conversion');
    $bitcoin = $cryptocurrency_code == 'btc' && bxc_settings_get('btc-node-transfer');
    if ($bitcoin || $ethereum) {
        require_once(__DIR__ . ($bitcoin ? '/bitcoin.php' : '/web3.php'));
        $history = json_decode(bxc_settings_db('crypto_transfers', false, '[]'), true);
        if (!in_array($transaction_id, $history)) {
            if ($bitcoin) {
                $transaction = bxc_transactions_get($transaction_id);
                $response = bxc_btc_transfer($amount, false, $transaction['to']);
                $success = $response && is_string($response);
            } else {
                $response = bxc_eth_transfer($amount, $cryptocurrency_code);
                $success = substr($response, 0, 2) == '0x';
            }
        }
    }
    if (!$success) {
        $gemini = bxc_settings_get('gemini-transfer') && !bxc_settings_get('gemini-conversion') && bxc_settings_get('gemini-address-generation');
        $coinbase = bxc_settings_get('coinbase-transfer') && !bxc_settings_get('coinbase-conversion') && bxc_settings_get('coinbase-address-generation');
        if ($gemini || $coinbase) {
            $history = json_decode(bxc_settings_db('crypto_transfers', false, '[]'), true);
            if (!in_array($transaction_id, $history)) {
                $address = bxc_settings_get_address($cryptocurrency_code);
                if ($address && !bxc_crypto_whitelist_invalid($address, false)) {
                    if ($gemini) {
                        $response = bxc_gemini_curl('withdraw/' . $cryptocurrency_code, ['address' => $address, 'amount' => $amount]);
                    } else if ($coinbase) {
                        $account = bxc_coinbase_get_accounts($cryptocurrency_code);
                        if ($account) {
                            $response = bxc_coinbase_curl('/v2/accounts/' . $account['id'] . '/transactions', ['to' => $address, 'amount' => $amount, 'currency' => $cryptocurrency_code, 'type' => 'send']);
                        }
                    }
                    $success = ($coinbase && bxc_isset(bxc_isset($response, 'data', []), 'status') == 'pending') || ($gemini && bxc_isset($response, 'address'));
                }
            }
        }
    }
    if ($success) {
        array_push($history, $transaction_id);
    }
    if ($bitcoin || $ethereum || $gemini || $coinbase) {
        bxc_settings_db('crypto_transfers', $history);
        if (bxc_settings_get('notifications-transfer')) {
            $language = bxc_settings_get('language-admin');
            $value = $amount . ' ' . strtoupper($cryptocurrency_code);
            $subject = $value . ' ' . bxc_m('sent to', $language) . ' ' . $address;
            $name = $ethereum ? 'Ethereum' : ($bitcoin ? 'Bitcoin' : ($gemini ? 'Gemini' : 'Coinbase'));
            $message = str_replace(['{T}', '{T2}', '{T3}'], [$value, '<b>' . $address . '</b>', $name], bxc_m('{T} sent to {T2} through {T3}.', $language));
            if (!$success) {
                $subject = 'The transfer of ' . $value . ' failed';
                $message = 'The transfer of ' . $value . ' to <b>' . $address . '</b> through ' . $name . ' failed. Response details: <br><br>' . json_encode($response);
            }
            bxc_email_notification($subject, $message);
        }
    }
    return $response;
}

function bxc_crypto_get_base_code($cryptocurrency_code) {
    $cryptocurrency_code = strtolower($cryptocurrency_code);
    return bxc_isset(['usdt_tron' => 'usdt', 'usdt_bsc' => 'usdt', 'btc_ln' => 'btc'], $cryptocurrency_code, $cryptocurrency_code);
}

function bxc_crypto_get_network($cryptocurrency_code, $label = false, $exclude_optional_networks = false) {
    $networks = bxc_get_cryptocurrency_codes();
    $cryptocurrency_code = strtolower($cryptocurrency_code);
    $full_name = $label === 'full_name';
    foreach ($networks as $key => $value) {
        if ((!$exclude_optional_networks || bxc_crypto_get_base_code($cryptocurrency_code) != $cryptocurrency_code || $cryptocurrency_code != $value[0]) && in_array($cryptocurrency_code, $networks[$key])) {
            $text = $key . ' <div>' . bxc_('network') . '</div>';
            return $label === true ? '<span class="bxc-label">' . $text . '</span>' : ($full_name ? bxc_crypto_name($key) : strtolower($key));
        }
    }
    return '';
}

function bxc_crypto_whitelist_invalid($address, $check_address_generation = true, $cryptocurrency_code = false) {
    if ($check_address_generation && bxc_is_address_generation($cryptocurrency_code)) {
        return false;
    }
    if (!defined('BXC_WHITELIST') || in_array($address, BXC_WHITELIST)) {
        return false;
    }
    bxc_error('The address ' . $address . ' is not on the whitelist. Edit the config.php file and add it to the constant BXC_WHITELIST.', 'bxc_crypto_address_verification', true);
    return true;
}

function bxc_get_custom_tokens() {
    global $BXC_CUSTOM_TOKENS;
    if (isset($BXC_CUSTOM_TOKENS)) {
        return $BXC_CUSTOM_TOKENS;
    }
    $BXC_CUSTOM_TOKENS = [];
    $items = bxc_settings_get_repeater(['custom-token-code', 'custom-token-type', 'custom-token-name', 'custom-token-address', 'custom-token-contract-address', 'custom-token-img', 'custom-token-decimals', 'custom-token-rate', 'custom-token-rate-url']);
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        if ($item[1]) {
            $token = strtolower($item[0]);
            $BXC_CUSTOM_TOKENS[$token] = ['type' => $item[1], 'code' => $token, 'name' => $item[2], 'address' => $item[3], 'contract_address' => $item[4], 'img' => $item[5], 'decimals' => $item[6], 'rate' => $item[7], 'rate_url' => $item[8]];
        }
    }
    return $BXC_CUSTOM_TOKENS;
}

function bxc_get_cryptocurrency_codes($blockchain = false) {
    $custom_tokens = bxc_get_custom_tokens();
    $custom_tokens_network = ['erc-20' => 'ETH', 'bep-20' => 'BSC'];
    $cryptocurrencies = ['BTC' => ['btc'], 'ETH' => ['eth', 'usdt', 'usdc', 'link', 'shib', 'bat'], 'SOL' => ['sol'], 'XMR' => ['xmr'], 'TRX' => ['usdt_tron'], 'XRP' => ['xrp'], 'LTC' => ['ltc'], 'DOGE' => ['doge'], 'BSC' => ['bnb', 'busd', 'usdt_bsc'], 'BCH' => ['bch'], 'ALGO' => ['algo']];
    foreach ($custom_tokens as $key => $value) {
        array_push($cryptocurrencies[$custom_tokens_network[$value['type']]], $key);
    }
    return $blockchain ? $cryptocurrencies[strtoupper($blockchain)] : $cryptocurrencies;
}

function bxc_crypto_get_decimals($cryptocurrency_code = false) {
    $custom_tokens = bxc_get_custom_tokens();
    $cryptocurrency_code = strtolower($cryptocurrency_code);
    $decimals = ['btc' => 8, 'btc_ln' => 8, 'eth' => 8, 'xrp' => 6, 'usdt' => 6, 'usdt_tron' => 6, 'usdt_bsc' => 6, 'usdc' => 6, 'link' => 5, 'doge' => 8, 'algo' => 6, 'shib' => 1, 'bat' => 3, 'bnb' => 7, 'busd' => 18, 'ltc' => 8, 'bch' => 8, 'sol' => 8, 'xmr' => 12];
    foreach ($custom_tokens as $key => $value) {
        $decimals[$key] = $value['decimals'];
    }
    return $cryptocurrency_code ? bxc_isset($decimals, $cryptocurrency_code) : $decimals;
}

function bxc_crypto_get_value_with_decimals($amount, $cryptocurrency_code_or_digits) {
    $decimals = is_int($cryptocurrency_code_or_digits) ? $cryptocurrency_code_or_digits : bxc_crypto_get_decimals($cryptocurrency_code_or_digits);
    $amount_array = explode('.', $amount);
    if (!isset($amount_array[1])) {
        array_push($amount_array, '');
    }
    if (strlen($amount_array[1]) > $decimals) {
        $amount_array[1] = substr($amount_array[1], 0, $decimals);
    }
    if (in_array($cryptocurrency_code_or_digits, ['usdt', 'usdc', 'busd']) && strlen($amount_array[1]) > 2) {
        $amount_array[1] = substr($amount_array[1], 0, 2);
    }
    return $amount_array[0] . rtrim($amount_array[1] && $amount_array[1] != '0' && $amount_array[1] != '00' ? '.' . $amount_array[1] : '', '0');
}

function bxc_crypto_is($currency_code) {
    return isset(bxc_crypto_name()[strtolower($currency_code)]);
}

function bxc_crypto_is_fiat($value) {
    return in_array($value, ['stripe', 'verifone', 'paypal']) || (strlen($value) == 3 && !bxc_crypto_is($value));
}

function bxc_crypto_is_custom_token($cryptocurrency_code) {
    return isset(bxc_get_custom_tokens()[strtolower($cryptocurrency_code)]);
}

function bxc_crypto_get_image($cryptocurrency_code) {
    return bxc_crypto_is_custom_token($cryptocurrency_code) ? bxc_get_custom_tokens()[strtolower($cryptocurrency_code)]['img'] : BXC_URL . 'media/icon-' . $cryptocurrency_code . '.svg';
}

function bxc_crypto_validate_address($address, $cryptocurrency_code) {
    $cryptocurrency_code = strtolower($cryptocurrency_code);
    if ($cryptocurrency_code == 'btc') {
        require_once(__DIR__ . '/bitcoin.php');
        return bxc_btc_validate_address($address);
    }
    if (bxc_crypto_get_network($cryptocurrency_code) == 'eth') {
        require_once(__DIR__ . '/web3.php');
        return bxc_eth_validate_address($address);
    }
    return false;
}

function bxc_crypto_get_explorer_link($hash, $cryptocurrency_code) {
    $response = bxc_blockchain($cryptocurrency_code, 'transaction-explorer', $hash);
    $hash = bxc_isset($response, 'hash');
    $network = bxc_crypto_get_network($cryptocurrency_code);
    $explorers = [
        'btc' => ['mempool' => 'https://mempool.space/tx/{R}', 'blockstream' => 'https://blockstream.info/tx/{R}', 'blockchain' => 'https://www.blockchain.com/explorer/transactions/{R2}/{R}'],
        'eth' => ['etherscan' => 'https://etherscan.io/tx/{R}', 'ethplorer' => 'https://ethplorer.io/tx/{R}', 'blockscout' => 'https://blockscout.com/eth/mainnet/tx/{R}'],
        'xrp' => ['ripple' => 'https://livenet.xrpl.org/accounts/{R}'],
        'doge' => ['blockcypher' => 'https://live.blockcypher.com/doge/tx/{R}'],
        'algo' => ['algoexplorerapi' => 'https://algoexplorer.io/tx/{R}'],
        'bnb' => ['bscscan' => 'https://bscscan.com/tx/{R}'],
        'ltc' => ['blockcypher' => 'https://live.blockcypher.com/ltc/tx/{R}'],
        'bch' => ['biggestfan' => 'https://blockchair.com/bitcoin-cash/transaction/{R}'],
        'trx' => ['tronscan' => 'https://tronscan.org/#/transaction/{R}'],
        'sol' => ['solana' => 'https://explorer.solana.com/tx/{R}'],
        'xmr' => ['monero' => 'https://blockchair.com/monero/transaction/{R}']
    ];
    $explorers_testnet = [
        'btc' => ['mempool' => 'https://mempool.space/testnet/tx/{R}'],
        'eth' => ['etherscan' => 'https://sepolia.etherscan.io/tx/{R}'],
    ];
    return $hash ? str_replace('{R}', $hash, bxc_settings_get('testnet-' . $network) ? $explorers_testnet[$network][$response['explorer']] : $explorers[$network][$response['explorer']]) : false;
}

function bxc_crypto_get_network_fee($cryptocurrency_code, $returned_currency_code = false) {
    $network = bxc_crypto_get_network($cryptocurrency_code);
    switch ($network) {
        case 'btc':
            require_once(__DIR__ . '/bitcoin.php');
            $fee = bxc_isset(bxc_btc_curl('estimatesmartfee', [5]), 'feerate', 0.00015) / 5;
            break;
        case 'eth':
            require_once(__DIR__ . '/web3.php');
            $fee = hexdec(bxc_eth_curl('eth_gasPrice')) * ($cryptocurrency_code === 'eth' ? 21000 : 100000) / 1000000000000000000;
            break;
    }
    if (!$returned_currency_code) {
        $returned_currency_code = $cryptocurrency_code;
    }
    return bxc_crypto_is($returned_currency_code) ? bxc_crypto_get_cryptocurrency_value($fee, $returned_currency_code, $network) : bxc_crypto_get_fiat_value($fee, $network, $returned_currency_code);
}

/*
 * -----------------------------------------------------------
 * # ACCOUNT
 * -----------------------------------------------------------
 *
 * 1. Admin login
 * 2. Verify the admin login
 * 3. Get the active account
 * 4. Get wallet key
 * 5. Check if the user is logged in as an agent
 *
 */

function bxc_login($username, $password, $code = false) {
    $ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) && substr_count($_SERVER['HTTP_CF_CONNECTING_IP'], '.') == 3 ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
    $ips = json_decode(bxc_settings_db('ip-ban', false, '{}'), true);
    $username = strtolower($username);
    if (isset($ips[$ip]) && $ips[$ip][0] > 10) {
        if ($ips[$ip][1] > time() - 3600) {
            return 'ip-ban';
        }
        unset($ips[$ip]);
        bxc_settings_db('ip-ban', $ips);
    }
    $is_success_login = $username == strtolower(BXC_USER) && password_verify($password, BXC_PASSWORD);

    if ($is_success_login) {
        if (bxc_settings_get('two-fa-active') && bxc_settings_db('2fa') && (!$code || bxc_2fa($code) !== true)) {
            return '2fa';
        }
    } else if (defined('BXC_AGENTS')) {
        for ($i = 0; $i < count(BXC_AGENTS); $i++) {
            if ($username == strtolower(BXC_AGENTS[$i][0]) && password_verify($password, BXC_AGENTS[$i][1])) {
                $is_success_login = 'agent';
                break;
            }
        }
    }
    if ($is_success_login) {
        $data = [BXC_USER, $is_success_login === 'agent' ? 'agent' : 'admin'];
        $GLOBALS['BXC_LOGIN'] = $data;
        if (bxc_settings_get('notifications-login')) {
            $language = bxc_settings_get('language-admin');
            bxc_email_notification(bxc_m('New login', $language), str_replace(['{T}', '{T2}'], [BXC_URL . 'admin.php', date('Y-m-d H:i:s')], bxc_m('New Boxcoin login at the URL {T}. Date and time of access: {T2}.', $language)));
        }
        return [bxc_encryption(json_encode($data, JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE))];
    }
    $ips[$ip] = empty($ips[$ip]) ? [1, time()] : [$ips[$ip][0] + 1, $ips[$ip][1]];
    bxc_settings_db('ip-ban', $ips);
    return false;
}

function bxc_verify_admin($allow_agent = true) {
    $account = bxc_account();
    return $account && $account[0] === BXC_USER && ($allow_agent || $account[1] != 'agent') ? $account[1] : false;
}

function bxc_account() {
    global $BXC_LOGIN;
    if (!defined('BXC_USER')) {
        return false;
    }
    if ($BXC_LOGIN) {
        return $BXC_LOGIN;
    }
    if (isset($_COOKIE['BXC_LOGIN'])) {
        $data = json_decode(bxc_encryption($_COOKIE['BXC_LOGIN'], false), true);
        if ($data) {
            $GLOBALS['BXC_LOGIN'] = $data;
            return $data;
        }
    }
    return false;
}

function bxc_get_wallet_key($cryptocurrency_code) {
    $key = bxc_settings_get(strtolower($cryptocurrency_code) . '-wallet-key');
    return $key ? bxc_encryption($key, false) : false;
}

function bxc_is_agent() {
    return bxc_verify_admin() === 'agent';
}

/*
 * -----------------------------------------------------------
 * SETTINGS
 * -----------------------------------------------------------
 *
 * 1. Populate the admin area with the settings of the file /resources/settings.json
 * 2. Return the HTML code of a setting element
 * 3. Save all settings
 * 4. Return a single setting
 * 5. Return all settings
 * 6. Return a repeater setting
 * 7. Return JS settings for admin side
 * 8. Return or save a database setting
 * 9. Get a saved address
 * 10. Get confirmations number
 *
 */

function bxc_settings_populate() {
    global $BXC_APPS;
    $settings = json_decode(file_get_contents(__DIR__ . '/resources/settings.json'), true);
    $code = '';
    $language = bxc_language(true);
    $translations = [];
    if ($language) {
        $path = __DIR__ . '/resources/languages/settings/' . $language . '.json';
        if (file_exists($path)) {
            $translations = json_decode(file_get_contents($path), true);
        }
    }
    if (BXC_CLOUD) {
        array_push($settings, ['id' => 'custom-domain', 'type' => 'text', 'title' => 'Custom domain', 'content' => 'Use a custom domain like example.com instead of cloud.boxcoin.dev.', 'help' => '#custom-domain']);
    }
    for ($i = 0; $i < count($settings); $i++) {
        $code .= bxc_settings_get_code($settings[$i], $translations);
    }
    for ($i = 0; $i < count($BXC_APPS); $i++) {
        if (BXC_CLOUD && $BXC_APPS[$i] == 'exchange') {
            continue;
        }
        $path = __DIR__ . '/apps/' . $BXC_APPS[$i] . '/settings.json';
        if (file_exists($path)) {
            $settings = json_decode(file_get_contents($path), true);
            $title = 'Settings related to the {R} addon.';
            $code .= '<div class="bxc-settings-title bxc-input"><div><span>' . ucfirst($BXC_APPS[$i]) . '</span><p>' . str_replace('{R}', $BXC_APPS[$i], bxc_isset($translations, $title, $title)) . '</p></div></div>';
            for ($y = 0; $y < count($settings); $y++) {
                $code .= bxc_settings_get_code($settings[$y], $translations);
            }
        }
    }
    echo $code;
}

function bxc_settings_get_code($setting, &$translations = []) {
    if (isset($setting)) {
        $id = $setting['id'];
        $type = $setting['type'];
        $title = $setting['title'];
        $content = $setting['content'];
        $code = '<div id="' . $id . '" data-type="' . $type . '" class="bxc-input"><div class="bxc-setting-content"><span>' . bxc_isset($translations, $title, $title) . '</span><p>' . bxc_isset($translations, $content, $content) . (isset($setting['help']) ? '<a href="' . (BXC_CLOUD ? CLOUD_DOCS : 'https://boxcoin.dev/docs') . '/' . $setting['help'] . '" target="_blank" class="bxc-icon-help"></a>' : '') . '</p></div><div class="bxc-setting-input">';
        switch ($type) {
            case 'color':
            case 'text':
                $code .= '<input type="text">';
                break;
            case 'password':
                $code .= '<input type="password">';
                break;
            case 'textarea':
                $code .= '<textarea></textarea>';
                break;
            case 'select':
                $values = $setting['value'];
                $code .= '<select>';
                for ($i = 0; $i < count($values); $i++) {
                    $code .= '<option value="' . $values[$i][0] . '">' . bxc_isset($translations, $values[$i][1], $values[$i][1]) . '</option>';
                }
                $code .= '</select>';
                break;
            case 'checkbox':
                $code .= '<input type="checkbox">';
                break;
            case 'number':
                $code .= '<input type="number">';
                break;
            case 'multi-input':
                $values = $setting['value'];
                for ($i = 0; $i < count($values); $i++) {
                    $sub_type = $values[$i]['type'];
                    $sub_title = $values[$i]['title'];
                    $code .= '<div id="' . $values[$i]['id'] . '" data-type="' . $sub_type . '"><span>' . bxc_isset($translations, $sub_title, $sub_title) . (isset($values[$i]['label']) ? '<span class="bxc-label">' . $values[$i]['label'] . '</span>' : '') . '</span>';
                    switch ($sub_type) {
                        case 'color':
                        case 'text':
                            $code .= '<input type="text">';
                            break;
                        case 'password':
                            $code .= '<input type="password">';
                            break;
                        case 'number':
                            $code .= '<input type="number">';
                            break;
                        case 'textarea':
                            $code .= '<textarea></textarea>';
                            break;
                        case 'checkbox':
                            $code .= '<input type="checkbox">';
                            break;
                        case 'select':
                            $code .= '<select>';
                            $items = $values[$i]['value'];
                            for ($j = 0; $j < count($items); $j++) {
                                $code .= '<option value="' . $items[$j][0] . '">' . bxc_isset($translations, $items[$j][1], $items[$j][1]) . '</option>';
                            }
                            $code .= '</select>';
                            break;
                        case 'button':
                            $code .= '<a class="bxc-btn" href="' . $values[$i]['button-url'] . '">' . bxc_isset($translations, $values[$i]['button-text'], $values[$i]['button-text']) . '</a>';
                            break;
                    }
                    $code .= '</div>';
                }
                if (isset($setting['repeater'])) {
                    $code .= '<div class="bxc-btn bxc-btn-repater" data-index="2">' . bxc_isset($translations, $setting['repeater_button'], $setting['repeater_button']) . '</div>';
                }
                break;
        }
        return $code . '</div></div>';
    }
    return '';
}

function bxc_settings_save($settings) {
    $settings = json_decode($settings, true);
    $settings_old = bxc_settings_get_all();
    if (!$settings) {
        return false;
    }
    $encryption = ['btc-wallet-key', 'eth-wallet-key', 'ln-macaroon'];
    for ($i = 0; $i < count($encryption); $i++) {
        $key = $encryption[$i];
        if (!empty($settings[$key])) {
            $settings[$key] = $settings[$key] == '********' ? $settings_old[$key] : bxc_encryption($settings[$encryption[$i]]);
        }
    }
    if (bxc_isset($settings_old, 'btc-node-xpub') != bxc_isset($settings, 'btc-node-xpub')) {
        bxc_settings_db('btc-addresses-xpub', false);
    }
    if (BXC_CLOUD && defined('APPROXIMATED_KEY')) {
        require_once(__DIR__ . '/cloud/functions.php');
        bxc_cloud_custom_domain(bxc_isset($settings, 'custom-domain'));
    }
    $settings = str_replace(['"false"', '"true"'], ['false', 'true'], json_encode($settings, JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE));
    if (json_last_error() != JSON_ERROR_NONE || !$settings) {
        return json_last_error();
    }
    $settings = bxc_encryption($settings);
    return bxc_db_query('INSERT INTO bxc_settings (name, value) VALUES (\'settings\', \'' . $settings . '\') ON DUPLICATE KEY UPDATE value = \'' . $settings . '\'');
}

function bxc_settings_get($id, $default = false) {
    global $BXC_SETTINGS;
    if (!$BXC_SETTINGS) {
        $BXC_SETTINGS = bxc_settings_get_all();
    }
    return bxc_isset($BXC_SETTINGS, $id, $default);
}

function bxc_settings_get_all() {
    global $BXC_SETTINGS;
    if (!$BXC_SETTINGS) {
        $BXC_SETTINGS = bxc_settings_db('settings');
        if ($BXC_SETTINGS) {
            if (substr($BXC_SETTINGS, 0, 1) !== '{') {
                $BXC_SETTINGS = bxc_encryption($BXC_SETTINGS, false); // temp, the if check will be removed soon
            }
            $BXC_SETTINGS = json_decode($BXC_SETTINGS, true);
        } else {
            $BXC_SETTINGS = [];
        }
    }
    return $BXC_SETTINGS;
}

function bxc_settings_get_repeater($ids) {
    $index = 1;
    $repeater_items = [];
    $count = count($ids);
    while ($index) {
        $suffix = $index > 1 ? '-' . $index : '';
        $repeater_item = [bxc_settings_get($ids[0] . $suffix)];
        if ($repeater_item[0]) {
            for ($i = 1; $i < $count; $i++) {
                array_push($repeater_item, bxc_settings_get($ids[$i] . $suffix));
            }
            array_push($repeater_items, $repeater_item);
            $index++;
        } else {
            $index = false;
        }
    }
    return $repeater_items;
}

function bxc_settings_js_admin() {
    global $BXC_APPS;
    $language = bxc_language(true);
    $addresses = [];
    $address_keys = ['btc', 'eth', 'doge', 'algo', 'link', 'usdt', 'usdt_tron', 'usdt_bsc', 'usdc', 'bat', 'shib', 'bnb', 'busd', 'ltc', 'bch', 'xrp', 'sol', 'xmr'];
    for ($i = 0; $i < count($address_keys); $i++) {
        $addresses[$address_keys[$i]] = bxc_settings_get_address($address_keys[$i], false);
    }
    $code = 'var BXC_LANG = "' . $language . '"; var BXC_AJAX_URL = "' . BXC_URL . 'ajax.php' . '"; var BXC_TRANSLATIONS = ' . ($language ? file_get_contents(__DIR__ . '/resources/languages/admin/' . $language . '.json') : '{}') . '; var BXC_CURRENCY = "' . bxc_settings_get('currency', 'USD') . '"; var BXC_URL = "' . BXC_URL . '"; var BXC_ADMIN = true; var BXC_ADDRESS = ' . json_encode($addresses) . ';';
    $refunds = [];
    $settings = [
        'apps' => [],
        'cryptocurrencies' => bxc_get_cryptocurrency_codes(),
        'invoice' => bxc_settings_get('invoice-active'),
        'max_file_size' => bxc_server_max_file_size(),
        'url_rewrite_checkout' => BXC_CLOUD ? 'checkout/' : bxc_settings_get('url-rewrite-checkout'),
        'url_rewrite_invoice' => BXC_CLOUD ? 'invoice/' : bxc_settings_get('url-rewrite-invoice'),
        'testnet_btc' => bxc_settings_get('testnet-btc'),
        'testnet_eth' => bxc_settings_get('testnet-eth'),
    ];
    for ($i = 0; $i < count($BXC_APPS); $i++) {
        if (defined('BXC_' . strtoupper($BXC_APPS[$i]))) {
            array_push($settings['apps'], $BXC_APPS[$i]);
        }
    }
    if (bxc_settings_get('stripe-active') && strpos(bxc_settings_get('stripe-key'), '_test_')) {
        $settings['stripe_test_mode'] = true;
    }
    if (bxc_settings_get('coinbase-refunds')) {
        array_push($refunds, 'coinbase');
    }
    if (bxc_settings_get('btc-node-refunds')) {
        array_push($refunds, 'btc');
    }
    if (bxc_settings_get('eth-node-refunds')) {
        array_push($refunds, 'eth');
    }
    if (!BXC_CLOUD) {
        $code .= 'var BXC_CLOUD = false;';
    }
    $code .= 'var BXC_REFUNDS = ' . json_encode($refunds) . ';var BXC_ADMIN_SETTINGS = ' . json_encode($settings) . ';';
    return $code;
}

function bxc_settings_db($name, $value = false, $default = false) {
    if ($value === false) {
        return bxc_isset(bxc_db_get('SELECT value FROM bxc_settings WHERE name = "' . bxc_db_escape($name) . '"'), 'value', $default);
    }
    if (is_string($value) || is_numeric($value)) {
        $value = bxc_db_escape($value);
    } else {
        $value = bxc_db_json_escape($value);
        if (json_last_error() != JSON_ERROR_NONE || !$value) {
            return json_last_error();
        }
    }
    return bxc_db_query('INSERT INTO bxc_settings (name, value) VALUES (\'' . bxc_db_escape($name) . '\', \'' . $value . '\') ON DUPLICATE KEY UPDATE value = \'' . $value . '\'');
}

function bxc_settings_get_address($cryptocurrency_code, $single = true) {
    $custom_tokens = bxc_get_custom_tokens();
    $address = $custom_tokens && isset($custom_tokens[$cryptocurrency_code]) ? $custom_tokens[$cryptocurrency_code]['address'] : bxc_settings_get('address-' . $cryptocurrency_code);
    $addresses = explode(',', str_replace(' ', '', preg_replace('/\s+/', '', $address)));
    return $addresses ? ($single ? $addresses[0] : $addresses) : false;
}

function bxc_settings_get_confirmations($cryptocurrency_code, $transaction_value = false) {
    $confirmations = bxc_settings_get('confirmations-' . $cryptocurrency_code);
    $confirmations = $confirmations ? $confirmations : bxc_settings_get('confirmations', 3);
    $threshold = bxc_settings_get('confirmations-increase-threshold');
    if ($transaction_value && $threshold && $transaction_value >= $threshold) {
        $confirmations = intval(intval($confirmations) * bxc_settings_get('confirmations-increase-percentage'));
    }
    return $confirmations;
}

/*
 * -----------------------------------------------------------
 * # LANGUAGE
 * -----------------------------------------------------------
 *
 * 1. Initialize the translations
 * 2. Get the active language
 * 3. Return the translation of a string
 * 4. Echo the translation of a string
 *
 */

function bxc_init_translations() {
    global $BXC_TRANSLATIONS;
    global $BXC_LANGUAGE;
    if (!empty($BXC_LANGUAGE) && $BXC_LANGUAGE[0] != 'en') {
        $path = __DIR__ . '/resources/languages/' . $BXC_LANGUAGE[1] . '/' . $BXC_LANGUAGE[0] . '.json';
        if (file_exists($path)) {
            $BXC_TRANSLATIONS = json_decode(file_get_contents($path), true);
        } else {
            $BXC_TRANSLATIONS = false;
        }
    } else if (!isset($BXC_LANGUAGE)) {
        $BXC_LANGUAGE = false;
        $BXC_TRANSLATIONS = false;
        $admin = bxc_verify_admin();
        $language = bxc_language($admin);
        $area = $admin ? 'admin' : 'client';
        if ($language) {
            $path = __DIR__ . '/resources/languages/' . $area . '/' . $language . '.json';
            if (file_exists($path)) {
                $BXC_TRANSLATIONS = json_decode(file_get_contents($path), true);
                $BXC_LANGUAGE = [$language, $area];
            } else {
                $BXC_TRANSLATIONS = false;
            }
        }
    }
    if ($BXC_LANGUAGE && $BXC_TRANSLATIONS && file_exists(__DIR__ . '/translations.json')) {
        $custom_translations = json_decode(file_get_contents(__DIR__ . '/translations.json'), true);
        if ($custom_translations && isset($custom_translations[$BXC_LANGUAGE[0]])) {
            $BXC_TRANSLATIONS = array_merge($BXC_TRANSLATIONS, $custom_translations[$BXC_LANGUAGE[0]]);
        }
    }
}

function bxc_language($admin = false) {
    $language = bxc_settings_get($admin ? 'language-admin' : 'language');
    if ($language == 'auto')
        $language = strtolower(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : false);
    if (!$language)
        $language = bxc_isset($_POST, 'language');
    return $language == 'en' ? false : $language;
}

function bxc_($string) {
    global $BXC_TRANSLATIONS;
    if (!isset($BXC_TRANSLATIONS)) {
        bxc_init_translations();
    }
    return empty($BXC_TRANSLATIONS[$string]) ? $string : $BXC_TRANSLATIONS[$string];
}

function bxc_e($string) {
    echo bxc_($string);
}

function bxc_m($string, $language_code) {
    global $BXC_TRANSLATIONS_2;
    if (!$language_code) {
        return $string;
    }
    if (!isset($BXC_TRANSLATIONS_2)) {
        $path = __DIR__ . '/resources/languages/client/' . $language_code . '.json';
        if (file_exists($path)) {
            $BXC_TRANSLATIONS_2 = json_decode(file_get_contents($path), true);
        }
    }
    return empty($BXC_TRANSLATIONS_2[$string]) ? $string : $BXC_TRANSLATIONS_2[$string];
}

/*
 * -----------------------------------------------------------
 * DATABASE
 * -----------------------------------------------------------
 *
 * 1. Connection to the database
 * 2. Get database values
 * 3. Insert or update database values
 * 4. Escape and sanatize values prior to databse insertion
 * 5. Escape a JSON string prior to databse insertion
 * 6. Set default database environment settings
 *
 */

function bxc_db_connect() {
    global $BXC_CONNECTION;
    if (!defined('BXC_DB_NAME') || !BXC_DB_NAME)
        return false;
    if ($BXC_CONNECTION) {
        bxc_db_init_settings();
        return true;
    }
    $BXC_CONNECTION = new mysqli(BXC_DB_HOST, BXC_DB_USER, BXC_DB_PASSWORD, BXC_DB_NAME, defined('BXC_DB_PORT') && BXC_DB_PORT ? intval(BXC_DB_PORT) : ini_get('mysqli.default_port'));
    if ($BXC_CONNECTION->connect_error) {
        echo 'Connection error. Visit the admin area for more details or open the config.php file and check the database information. Message: ' . $BXC_CONNECTION->connect_error . '.';
        return false;
    }
    bxc_db_init_settings();
    return true;
}

function bxc_db_get($query, $single = true) {
    global $BXC_CONNECTION;
    $status = bxc_db_connect();
    $value = ($single ? '' : []);
    if ($status) {
        $result = $BXC_CONNECTION->query($query);
        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if ($single) {
                        $value = $row;
                    } else {
                        array_push($value, $row);
                    }
                }
            }
        } else {
            return $BXC_CONNECTION->error;
        }
    } else {
        return $status;
    }
    return $value;
}

function bxc_db_query($query, $return = false) {
    global $BXC_CONNECTION;
    $status = bxc_db_connect();
    if ($status) {
        $result = $BXC_CONNECTION->query($query);
        if ($result) {
            if ($return) {
                if (isset($BXC_CONNECTION->insert_id) && $BXC_CONNECTION->insert_id > 0) {
                    return $BXC_CONNECTION->insert_id;
                } else {
                    return $BXC_CONNECTION->error;
                }
            } else {
                return true;
            }
        } else {
            return $BXC_CONNECTION->error;
        }
    } else {
        return $status;
    }
}

function bxc_db_escape($value, $numeric = -1) {
    if (is_numeric($value)) {
        return $value;
    } else if ($numeric === true) {
        return false;
    }
    if ($value === false) {
        return false;
    }
    global $BXC_CONNECTION;
    bxc_db_connect();
    if ($BXC_CONNECTION) {
        $value = $BXC_CONNECTION->real_escape_string($value);
    }
    $value = bxc_sanatize_string($value);
    $value = str_replace('&amp;', '&', $value);
    return $value;
}

function bxc_db_json_escape($array) {
    global $BXC_CONNECTION;
    bxc_db_connect();
    $value = str_replace(['"false"', '"true"'], ['false', 'true'], json_encode($array, JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE));
    $value = bxc_sanatize_string($value, false);
    return $BXC_CONNECTION ? $BXC_CONNECTION->real_escape_string($value) : $value;
}

function bxc_db_check_connection($name = false, $user = false, $password = false, $host = false, $port = false) {
    global $BXC_CONNECTION;
    $response = true;
    if ($name === false && defined('BXC_DB_NAME')) {
        $name = BXC_DB_NAME;
        $user = BXC_DB_USER;
        $password = BXC_DB_PASSWORD;
        $host = BXC_DB_HOST;
        $port = defined('BXC_DB_PORT') && BXC_DB_PORT ? intval(BXC_DB_PORT) : false;
    }
    try {
        set_error_handler(function () { }, E_ALL);
        $BXC_CONNECTION = new mysqli($host, $user, $password, $name, $port === false ? ini_get('mysqli.default_port') : intval($port));
    } catch (Exception $e) {
        $response = $e->getMessage();
    }
    if ($BXC_CONNECTION->connect_error) {
        $response = $BXC_CONNECTION->connect_error;
    }
    restore_error_handler();
    return $response;
}

function bxc_db_init_settings() {
    global $BXC_CONNECTION;
    $BXC_CONNECTION->set_charset('utf8mb4');
    $BXC_CONNECTION->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
}

/*
 * -----------------------------------------------------------
 * MISCELLANEOUS
 * -----------------------------------------------------------
 *
 * 1. Encryption
 * 2. Check if a key is set and return it
 * 3. Update or create config file
 * 4. Installation
 * 5. Check if database connection is working
 * 6. Curl
 * 7. Cron jobs
 * 8. Scientific number to decimal number
 * 9. Get array value by path
 * 10. Updates
 * 11. Check if demo URL
 * 12. Check if RTL
 * 13. Debug
 * 14. CSV
 * 15. Apply admin colors
 * 16. Admin JS
 * 18. Load the custom .js and .css files
 * 19. Generate the payment redirect URL
 * 19. Apply version updates
 * 20. Error reporting
 * 21. Env check
 * 22. Check if address generation or not
 * 23. Vbox
 * 24. Email
 * 25. Email notifications
 * 26. Check if ETH address generation
 * 27. Get Tron contract addresses
 * 28. Get Binance contract addresses
 * 29. Get the user IP
 * 30. Slug string
 * 31. Returns the max file size for uploading
 * 32. Delete a file
 * 33. Generate a random string
 * 34. Convert text syntax to HTML tags
 * 35. Return the rewritten URL
 * 36. Sanatize a string
 * 37. Sanatize a file name
 * 38. 2FA
 *
 */

function bxc_encryption($string, $encrypt = true) {
    $output = false;
    $encrypt_method = 'AES-256-CBC';
    $secret_key = BXC_PASSWORD . BXC_USER;
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', BXC_PASSWORD), 0, 16);
    if ($encrypt) {
        $output = openssl_encrypt(is_string($string) ? $string : json_encode($string, JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE), $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
        if (substr($output, -1) == '=')
            $output = substr($output, 0, -1);
    } else {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

function bxc_isset($array, $key, $default = false) {
    return !empty($array) && isset($array[$key]) && $array[$key] !== '' ? $array[$key] : $default;
}

function bxc_config($content) {
    $file = fopen(__DIR__ . '/config.php', 'w');
    fwrite($file, $content);
    fclose($file);
    return true;
}

function bxc_installation($data) {
    if (!defined('BXC_USER') || !defined('BXC_DB_HOST') || BXC_CLOUD) {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }
        $connection_check = bxc_db_check_connection($data['db-name'], $data['db-user'], $data['db-password'], $data['db-host'], $data['db-port']);
        if ($connection_check === true) {

            // Create the config.php file
            $code = '<?php' . PHP_EOL;
            if (empty($data['db-host'])) {
                $data['db-host'] = 'localhost';
            }
            if (empty($data['db-port'])) {
                $data['db-port'] = ini_get('mysqli.default_port');
            }
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password-check']);
            foreach ($data as $key => $value) {
                if (!$value && $key != 'db-password') {
                    return 'Empty ' . $key;
                }
                $code .= 'define(\'BXC_' . str_replace('-', '_', strtoupper($key)) . '\', \'' . str_replace('\'', '\\\'', $value) . '\');' . PHP_EOL;
            }
            $file = fopen(__DIR__ . (!empty($data['token']) ? '/cloud/config/' . $data['token'] . '.php' : '/config.php'), 'w');
            fwrite($file, $code . '?>');
            fclose($file);

            // Create the database tables
            $connection = new mysqli($data['db-host'], $data['db-user'], $data['db-password'], $data['db-name'], $data['db-port']);
            $connection->set_charset('utf8mb4');
            $connection->query('CREATE TABLE IF NOT EXISTS bxc_checkouts (id INT NOT NULL AUTO_INCREMENT, title VARCHAR(191), description TEXT, price VARCHAR(100) NOT NULL, currency VARCHAR(10) NOT NULL, type VARCHAR(1), redirect VARCHAR(191), hide_title TINYINT, external_reference VARCHAR(1000) NOT NULL DEFAULT "", creation_time DATETIME NOT NULL, slug VARCHAR(191), PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
            $connection->query('CREATE TABLE IF NOT EXISTS bxc_transactions (id INT NOT NULL AUTO_INCREMENT, `from` VARCHAR(191) NOT NULL DEFAULT "", `to` VARCHAR(191), hash VARCHAR(191) NOT NULL DEFAULT "", `title` VARCHAR(500) NOT NULL DEFAULT "", description VARCHAR(1000) NOT NULL DEFAULT "", amount VARCHAR(100) NOT NULL, amount_fiat VARCHAR(100) NOT NULL, cryptocurrency VARCHAR(10) NOT NULL, currency VARCHAR(10) NOT NULL, external_reference VARCHAR(1000) NOT NULL DEFAULT "", creation_time DATETIME NOT NULL, status VARCHAR(1) NOT NULL, webhook TINYINT NOT NULL, billing TINYTEXT, vat FLOAT, vat_details TINYTEXT, checkout_id INT, type TINYINT, PRIMARY KEY (id), FOREIGN KEY (checkout_id) REFERENCES bxc_checkouts(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
            $connection->query('CREATE TABLE IF NOT EXISTS bxc_settings (name VARCHAR(191) NOT NULL, value LONGTEXT, PRIMARY KEY (name)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
            $connection->query('CREATE TABLE IF NOT EXISTS bxc_customers (id INT NOT NULL AUTO_INCREMENT, first_name VARCHAR(191) NOT NULL DEFAULT "", last_name VARCHAR(191) NOT NULL DEFAULT "", email VARCHAR(191), phone VARCHAR(191) NOT NULL DEFAULT "", country VARCHAR(191) NOT NULL DEFAULT "", country_code VARCHAR(3) NOT NULL DEFAULT "", creation_time DATETIME NOT NULL, extra TINYTEXT, PRIMARY KEY (id), UNIQUE (email)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
            return true;
        }
        return $connection_check;
    }
    return false;
}

function bxc_curl($url, $post_fields = '', $header = [], $type = 'GET', $timeout = false) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'BOXCOIN');
    switch ($type) {
        case 'DELETE':
        case 'PUT':
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($post_fields) ? $post_fields : http_build_query($post_fields));
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout ? $timeout : 7);
            if ($type != 'POST') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
            }
            break;
        case 'GET':
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout ? $timeout : 7);
            curl_setopt($ch, CURLOPT_HEADER, false);
            break;
        case 'DOWNLOAD':
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout ? $timeout : 70);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            break;
        case 'FILE':
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout ? $timeout : 400);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            if (strpos($url, '?')) {
                $url = substr($url, 0, strpos($url, '?'));
            }
            $path_part = 'uploads' . bxc_cloud_path_part() . '/' . basename($url);
            $file = fopen(__DIR__ . '/' . $path_part, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $file);
            break;
    }
    if (!empty($header)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if (bxc_isset($header, 'CURLOPT_USERPWD')) {
            curl_setopt($ch, CURLOPT_USERPWD, $header['CURLOPT_USERPWD']);
        }
    }
    $response = curl_exec($ch);
    if (curl_errno($ch) > 0) {
        $error = curl_error($ch);
        curl_close($ch);
        return $error;
    }
    curl_close($ch);
    if ($type == 'FILE' && $response === true) {
        return [__DIR__ . '/' . $path_part, BXC_URL . $path_part];
    }
    return $response;
}

function bxc_download($url) {
    return bxc_curl($url, '', '', 'DOWNLOAD');
}

function bxc_cron() {

    // Updates
    if (!BXC_CLOUD && bxc_settings_get('update-auto')) {
        bxc_update($_POST['domain']);
        bxc_version_updates();
    }

    // Invoice deletion
    if (!BXC_CLOUD && bxc_settings_get('invoice-active')) {
        $path = __DIR__ . '/uploads/';
        $files = scandir($path);
        if ($files) {
            $expiration = strtotime('-1 days');
            for ($i = 0; $i < count($files); $i++) {
                $file = $files[$i];
                if (strpos($file, 'inv-') === 0 && (filemtime($path . $file) < $expiration)) {
                    unlink($path . '/' . $file);
                }
            }
        }
    }

    // Shop
    if (defined('BXC_SHOP')) {
        bxc_shop_downloads_delete_old_files();
    }

    // Delete pending transactions
    bxc_transactions_delete_pending();

    // Check payment for pending transactions
    bxc_transactions_check_pending();
}

function bxc_decimal_number($number) {
    $number = rtrim(number_format($number, 10, '.', ''), '0');
    return substr($number, -1) == '.' ? substr($number, 0, -1) : $number;
}

function bxc_get_array_value_by_path($path, $array) {
    $path = str_replace(' ', '', $path);
    if (strpos($path, ',')) {
        $response = [];
        $paths = explode(',', $path);
        for ($i = 0; $i < count($paths); $i++) {
            array_push($response, bxc_get_array_value_by_path($paths[$i], $array));
        }
        return $response;
    }
    $path = explode('>', $path);
    for ($i = 0; $i < count($path); $i++) {
        $array = $array ? bxc_isset($array, $path[$i]) : false;
    }
    return $array;
}

function bxc_update($domain) {
    if (!class_exists('ZipArchive')) {
        return 'no-zip-archive';
    }
    $latest_versions = bxc_versions();
    $response_update = [];
    foreach ($latest_versions as $name => $version) {
        $addon = $name != 'boxcoin';
        if ($addon && !defined('BXC_' . strtoupper($name))) {
            continue;
        }
        $envato_purchase_code = bxc_settings_get(($addon ? $name . '-' : '') . 'envato-purchase-code');
        if (!$envato_purchase_code) {
            return ($addon ? $name . '-' : '') . 'envato-purchase-code-not-found';
        }
        if ((!$addon && $version == BXC_VERSION) || ($name == 'exchange' && $version == BXC_EXCHANGE)) {
            $response_update[$name] = 'latest-version-installed';
            continue;
        }
        $response_json = bxc_download('https://boxcoin.dev/sync/updates.php?key=' . trim($envato_purchase_code) . '&domain=' . $domain . '&app=' . $name);
        $response = json_decode($response_json, true);
        if (empty($response[$name])) {
            return $response_json;
        }
        $zip = bxc_download('https://boxcoin.dev/sync/temp/' . $response[$name]);
        if ($zip) {
            $file_path = __DIR__ . '/boxcoin.zip';
            file_put_contents($file_path, $zip);
            if (file_exists($file_path)) {
                $zip = new ZipArchive;
                if ($zip->open($file_path) === true) {
                    $zip->extractTo(__DIR__ . ($addon ? '/apps/' : ''));
                    $zip->close();
                    unlink($file_path);
                    $response_update[$name] = true;
                } else
                    return 'zip-error';
            } else
                return 'file-not-found';
        } else
            return 'download-error';
    }
    return $response_update;
}

function bxc_versions() {
    return json_decode(bxc_download('https://boxcoin.dev/sync/versions.json'), true);
}

function bxc_is_demo($attributes = false) {
    $url = bxc_isset($_SERVER, 'HTTP_REFERER');
    if (strpos($url, 'demo=true')) {
        if ($attributes) {
            parse_str($url, $url);
            return $url;
        }
        return true;
    }
    return false;
}

function bxc_is_rtl($language) {
    return in_array($language, ['ar', 'he', 'ku', 'fa', 'ur']);
}

function bxc_debug($value) {
    $value = is_string($value) ? $value : json_encode($value, JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE);
    $path = __DIR__ . '/debug.txt';
    if (file_exists($path)) {
        $value = file_get_contents($path) . PHP_EOL . $value;
    }
    bxc_file($path, $value);
}

function bxc_file($path, $content) {
    try {
        $file = fopen($path, 'w');
        fwrite($file, $content);
        fclose($file);
        return true;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

function bxc_csv($rows, $header, $filename) {
    $filename .= '-' . bxc_random_string() . '.csv';
    $path_part = 'uploads' . bxc_cloud_path_part() . '/' . $filename;
    $file = fopen(__DIR__ . '/' . $path_part, 'w');
    if ($header) {
        fputcsv($file, $header);
    }
    for ($i = 0; $i < count($rows); $i++) {
        fputcsv($file, $rows[$i]);
    }
    fclose($file);
    return BXC_URL . $path_part;
}

function bxc_colors_admin() {
    $color_1 = bxc_settings_get('color-admin-1');
    $color_2 = bxc_settings_get('color-admin-2');
    $code = '';
    if ($color_1) {
        $code = '.bxc-btn,.datepicker-cell.range-end:not(.selected), .datepicker-cell.range-start:not(.selected), .datepicker-cell.selected, .datepicker-cell.selected:hover,.bxc-select ul li:hover,.bxc-underline:hover:after { background-color: ' . $color_1 . '; }';
        $code .= '.bxc-nav>div:hover, .bxc-nav>div.bxc-active,.bxc-btn-icon:hover,.bxc-btn.bxc-btn-border:hover, .bxc-btn.bxc-btn-border:active,[data-type="multi-input"] .bxc-btn:hover { border-color: ' . $color_1 . ' !important; color: ' . $color_1 . '; }';
        $code .= '.bxc-link:hover, .bxc-link:active,.bxc-input input[type="checkbox"]:checked:before,.bxc-loading:before, [data-bxc]:empty:before,.bxc-search input:focus+input+i,.bxc-select p:hover { color: ' . $color_1 . '; }';
        $code .= '.bxc-input input:focus, .bxc-input input.bxc-focus, .bxc-input select:focus, .bxc-input select.bxc-focus, .bxc-input textarea:focus, .bxc-input textarea.bxc-focus { border-color: ' . $color_1 . '; }';
        $code .= '.datepicker-cell.range,.bxc-btn-icon:hover,.bxc-input input:focus, .bxc-input input.bxc-focus, .bxc-input select:focus, .bxc-input select.bxc-focus, .bxc-input textarea:focus, .bxc-input textarea.bxc-focus,.bxc-table tr:hover td { background-color: rgb(105 105 105 / 5%); }';
        $code .= '.bxc-input input, .bxc-input select, .bxc-input textarea, .bxc-input input[type="checkbox"] { background-color: #fafafa; }';
    }
    if ($color_2) {
        $code .= '.bxc-btn:hover, .bxc-btn:active { background-color: ' . $color_2 . '; }';
    }
    if ($code) {
        echo '<style>' . $code . '</style>';
    }
}

function bxc_load_custom_js_css() {
    $js = BXC_CLOUD ? false : bxc_settings_get('js-admin');
    $css = bxc_settings_get('css-admin');
    if ($js)
        echo '<script src="' . $js . '?v=' . BXC_VERSION . '"></script>';
    if ($css)
        echo '<link rel="stylesheet" href="' . $css . '?v=' . BXC_VERSION . '" media="all" />';
}

function bxc_payment_redirect_url($url, $client_reference_id, $encode = true) {
    parse_str(bxc_isset(parse_url($url), 'query'), $attributes);
    $url = str_replace(['payment_status=cancelled', isset($attributes['cc']) ? 'cc=' . $attributes['cc'] : '', isset($attributes['pay']) ? 'pay=' . $attributes['pay'] : ''], '', $url);
    $url = $url . (strpos($url, '?') ? '&' : '?') . 'cc=' . bxc_encryption(json_encode(['id' => $client_reference_id]));
    return $encode ? urlencode($url) : $url;
}

function bxc_version_updates() {
    if (bxc_settings_db('version') != BXC_VERSION) {
        try {
            // 05-24
            bxc_db_query('DELETE FROM bxc_settings WHERE name = "coinbase_accounts"');

            // 11-23
            bxc_db_query('ALTER TABLE bxc_checkouts ADD COLUMN slug VARCHAR(191)');

            // 09-23
            bxc_db_query('CREATE TABLE IF NOT EXISTS bxc_customers (id INT NOT NULL AUTO_INCREMENT, first_name VARCHAR(191) NOT NULL DEFAULT "", last_name VARCHAR(191) NOT NULL DEFAULT "", email VARCHAR(191), phone VARCHAR(191) NOT NULL DEFAULT "", country VARCHAR(191) NOT NULL DEFAULT "", country_code VARCHAR(3) NOT NULL DEFAULT "", creation_time DATETIME NOT NULL, extra TINYTEXT, PRIMARY KEY (id), UNIQUE (email)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
            bxc_db_query('ALTER TABLE bxc_transactions ADD COLUMN type TINYINT');

            // 08-23
            bxc_db_query('ALTER TABLE bxc_transactions ADD COLUMN checkout_id INT, ADD CONSTRAINT FOREIGN KEY (checkout_id) REFERENCES bxc_checkouts(id) ON DELETE CASCADE');

        } catch (Exception $e) {
        }
        bxc_settings_db('version', BXC_VERSION);
    }
}

function bxc_error($message, $function_name, $force = false) {
    $message = 'Boxcoin error [' . $function_name . ']: ' . (is_string($message) ? $message : json_encode($message));
    if ($force || bxc_isset($_GET, 'debug') || strpos(bxc_isset($_SERVER, 'HTTP_REFERER'), 'debug')) {
        if (bxc_verify_admin()) {
            bxc_debug($message);
        }
        trigger_error($message);
    }
    return $message;
}

function bxc_is_address_generation($cryptocurrency_code = false) {
    return bxc_settings_get('gemini-address-generation') || bxc_settings_get('coinbase-address-generation') || (strtolower($cryptocurrency_code) === 'btc' && bxc_settings_get('btc-node-address-generation')) || bxc_is_eth_address_generation($cryptocurrency_code) || (bxc_settings_get('custom-explorer-active') && bxc_settings_get('custom-explorer-address')) || ($cryptocurrency_code && count(bxc_settings_get_address($cryptocurrency_code, false)) > 2);
}

function bxc_ve_box() {
    $main = !isset($_COOKIE['TR_' . 'VUU' . 'KMILO']) || !password_verify('YTYFUJG', $_COOKIE['TR_' . 'VUU' . 'K' . 'MILO']);
    $exchange = defined('BXC_EXCH' . 'ANGE') && (!isset($_COOKIE['EX_' . 'HH' . 'U' . 'V' . 'AR']) || !password_verify('EXC' . 'JKYU', $_COOKIE['EX_' . 'H' . 'H' . 'U' . 'V' . 'AR']));
    $shop = defined('BXC_S' . 'HOP') && (!isset($_COOKIE['SH_' . 'UI' . 'X' . 'U' . 'JJ']) || !password_verify('SHP' . 'UIOO', $_COOKIE['SH_' . 'U' . 'I' . 'X' . 'U' . 'JJ']));
    if ($main || $exchange || $shop) {
        echo '<' . 'scr' . 'ipt' . '>' . 'va' . 'r BXC' . 'EV = ' . ($main ? 'false' : ($exchange ? '"Exc' . 'hange"' : ($shop ? '"S' . 'hop"' : 'false'))) . ';<' . '/scri' . 'pt' . '>';
        echo file_get_contents(__DIR__ . '/resou' . 'r' . 'ces/e' . 'pc.ht' . 'ml');
        return false;
    }
    return true;
}

function bxc_ve($code, $domain, $app = false) {
    $app = strtolower($app);
    if ($code == 'auto') {
        $code = bxc_settings_get(($app ? strtolower($app) . '-' : '') . 'en' . 'vato-purc' . 'hase-code');
    }
    if (empty($code)) {
        return [false, ''];
    }
    $response = bxc_curl('htt' . 'ps://boxcoin' . '.dev/sync/ve' . 'r' . 'ification.p' . 'hp?ve' . 'rifi' . 'cation&code=' . $code . '&domain=' . $domain . '&app=' . $app);
    if ($response == 've' . 'rific' . 'ation-success') {
        return [true, password_hash($app ? ($app == 'exch' . 'ange' ? 'EXC' . 'JKYU' : 'SHP' . 'UIOO') : 'YTY' . 'FUJG', PASSWORD_DEFAULT)];
    }
    return [false, $response];
}

function bxc_email_send($to, $subject, $body, $is_user = false) {
    $settings = BXC_CLOUD ? ['smtp-host' => CLOUD_SMTP_HOST, 'smtp-user' => CLOUD_SMTP_USERNAME, 'smtp-password' => CLOUD_SMTP_PASSWORD, 'smtp-from' => CLOUD_SMTP_SENDER, 'email-sender-name' => CLOUD_SMTP_SENDER_NAME, 'smtp-port' => CLOUD_SMTP_PORT] : ['smtp-host' => bxc_settings_get('smtp-host'), 'smtp-user' => bxc_settings_get('smtp-user'), 'smtp-password' => bxc_settings_get('smtp-password'), 'smtp-from' => bxc_settings_get('smtp-from'), 'email-sender-name' => bxc_settings_get('smtp-name'), 'smtp-port' => bxc_settings_get('smtp-port')];
    if (empty($to)) {
        return false;
    }
    if (!is_string($body)) {
        $code = file_get_contents(__DIR__ . '/resources/email.html');
        $code = str_replace('{message}', $body['message'], $code);
        $code = str_replace('{title}', isset($body['title']) ? '<h1 style="text-align:left;font-size: 25px;line-height: 40px;font-weight: 500;color: #283c49;text-decoration: none;">' . $body['title'] . '</h1>' : '', $code);
        $code = str_replace('{tagline}', isset($body['title']) ? $body['title'] : substr($body['message'], 0, 100), $code);
        $code = str_replace('{image}', BXC_CLOUD ? CLOUD_LOGO_PNG : (bxc_settings_get('logo-admin') ? bxc_settings_get('logo-url-png', bxc_settings_get('logo-url', BXC_URL . 'media/logo.png')) : BXC_URL . 'media/logo.png'), $code);
        $code = str_replace('{link}', BXC_CLOUD ? CLOUD_URL : ($is_user ? '#' : BXC_URL . 'admin.php'), $code);
        $code = str_replace('{footer}', BXC_CLOUD ? CLOUD_EMAIL : bxc_settings_get('notifications-footer', ''), $code);
        $body = $code;
    } else {
        $body = nl2br(trim($body));
    }
    if ($settings['smtp-host']) {
        require_once __DIR__ . '/vendor/phpmailer/PHPMailerAutoload.php';
        $port = $settings['smtp-port'];
        $mail = new PHPMailer;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isSMTP();
        $mail->Host = $settings['smtp-host'];
        $mail->SMTPAuth = true;
        $mail->Username = $settings['smtp-user'];
        $mail->Password = $settings['smtp-password'];
        $mail->SMTPSecure = $port == 25 ? '' : ($port == 465 ? 'ssl' : 'tls');
        $mail->Port = $port;
        $mail->setFrom($settings['smtp-from'], bxc_isset($settings, 'email-sender-name', ''));
        $mail->isHTML(true);
        $mail->Subject = trim($subject);
        $mail->Body = $body;
        $mail->AltBody = $body;
        $mail->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]];
        if (strpos($to, ',') > 0) {
            $emails = explode(',', $to);
            for ($i = 0; $i < count($emails); $i++) {
                $mail->addAddress($emails[$i]);
            }
        } else {
            $mail->addAddress($to);
        }
        return $mail->send() ? true : bxc_error($mail->ErrorInfo, 'bxc_email_send');
    } else {
        return mail($to, $subject, $body);
    }
}

function bxc_email_notification($subject, $message, $to = false) {
    if (!$to) {
        $to = bxc_settings_get('notifications-email');
        if (!$to) {
            return bxc_error('Missing recipient email.', 'bxc_email_notification');
        }
    }
    return bxc_email_send($to, $subject, ['message' => $message]);
}

function bxc_is_eth_address_generation($cryptocurrency_code) {
    $cryptocurrency_code = strtolower($cryptocurrency_code);
    return in_array($cryptocurrency_code, bxc_get_cryptocurrency_codes('eth')) && bxc_settings_get('eth-node-address-generation');
}

function bxc_tron_get_contract_address($cryptocurrency_code) {
    return bxc_isset(['usdt' => 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t'], strtolower($cryptocurrency_code));
}

function bxc_binance_get_contract_address($cryptocurrency_code) {
    return bxc_isset(['busd' => '0xe9e7CEA3DedcA5984780Bafc599bD69ADd087D56', 'usdt' => '0x55d398326f99059ff775485246999027b3197955'], strtolower($cryptocurrency_code));
}

function bxc_ip_info($fields, $ip = false) {
    $ip = $ip ? $ip : (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && substr_count($_SERVER['HTTP_CF_CONNECTING_IP'], '.') == 3 ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR']);
    return strlen($ip) > 6 ? json_decode(bxc_download('http://ip-api.com/json/' . $ip . '?fields=' . $fields), true) : false;
}

function bxc_string_slug($string, $action = 'slug') {
    $string = trim($string);
    if ($action == 'slug') {
        return strtolower(str_replace([' ', '\'', '"'], ['-', '', ''], $string));
    } else if ($action == 'string') {
        return ucfirst(strtolower(str_replace(['-', '_'], ' ', $string)));
    }
    return $string;
}

function bxc_server_max_file_size() {
    $size = ini_get('post_max_size');
    if (empty($size)) {
        return 9999;
    }
    $suffix = strtoupper(substr($size, -1));
    $size = substr($size, 0, -1);
    if ($size === 0) {
        return 9999;
    }
    switch ($suffix) {
        case 'P':
            $size /= 1024;
        case 'T':
            $size /= 1024;
        case 'G':
            $size /= 1024;
        case 'K':
            $iValue *= 1024;
            break;
    }
    return $size;
}

function bxc_file_delete($file_name, $folder = '') {
    $path = __DIR__ . '/uploads' . bxc_cloud_path_part() . '/' . $folder . $file_name;
    if (file_exists($path)) {
        return unlink($path);
    } else if (BXC_CLOUD && defined('CLOUD_AWS_S3')) {
        return bxc_cloud_aws_s3(bxc_cloud_aws_s3_url($file_name), 'DELETE');
    }
    return false;
}

function bxc_random_string() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $random_string = '';
    for ($i = 0; $i < 15; $i++) {
        $random_string .= $characters[rand(0, $characters_length - 1)];
    }
    return $random_string;
}

function bxc_render_text($text) {
    $text = trim(preg_replace('@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@', '<a href="$0" target="_blank" class="bxc-link">$0</a>', str_replace('&amp;', '&', bxc_($text))));
    $regex = [['/\*(.*?)\*/', '<b>', '</b>'], ['/__(.*?)__/', '<em>', '</em>'], ['/~(.*?)~/', '<del>', '</del>'], ['/`(.*?)`/', '<code>', '</code>']];
    for ($i = 0; $i < count($regex); $i++) {
        $values = [];
        if (preg_match_all($regex[$i][0], $text, $values, PREG_SET_ORDER)) {
            for ($j = 0; $j < count($values); $j++) {
                $text = str_replace($values[$j][0], $regex[$i][1] . $values[$j][1] . $regex[$i][2], $text);
            }
        }
    }
    return $text;
}

function bxc_get_url($type) {
    $custom_domain = BXC_CLOUD ? bxc_settings_get('custom-domain') : false;
    $url = $custom_domain ? str_replace('//' . $type, '/' . $type, $custom_domain . '/' . $type . '/') : BXC_URL . (BXC_CLOUD ? $type . '/' : (bxc_settings_get('url-rewrite-' . $type) ? bxc_settings_get('url-rewrite-' . $type) : 'pay.php?' . ($type == 'checkout' ? 'checkout_id' : $type) . '='));
    return $url;
}

function bxc_sanatize_string($value, $is_htmlspecialchars = true) {
    $value = str_ireplace(['<script', '</script'], ['&lt;script', '&lt;/script'], $value);
    if ($is_htmlspecialchars) {
        $value = htmlspecialchars($value, ENT_NOQUOTES | ENT_SUBSTITUTE, 'utf-8');
    }
    return str_ireplace(['onload', 'javascript:', 'onclick', 'onerror', 'onmouseover', 'oncontextmenu', 'ondblclick', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseup', 'ontoggle'], '', $value);
}

function bxc_sanatize_file_name($value) {
    return htmlspecialchars(str_ireplace(['\\', '/', ':', '?', '"', '*', '<', '>', '|'], '', bxc_sanatize_string($value)), ENT_NOQUOTES | ENT_SUBSTITUTE, 'utf-8');
}

function bxc_2fa($pin = false) {
    $secret_code = bxc_settings_db('2fa');
    if ($pin) {
        if (!$secret_code) {
            return bxc_error('2FA secret code not found.', 'bxc_2fa');
        }
        $response = bxc_curl('https://www.authenticatorApi.com/Validate.aspx?Pin=' . $pin . '&SecretCode=' . $secret_code);
        return $response == 'True' ? true : $response;
    } else {
        if (!$secret_code) {
            $secret_code = bxc_random_string();
            bxc_settings_db('2fa', $secret_code);
        }
        return 'https://www.authenticatorApi.com/pair.aspx?AppName=' . (BXC_CLOUD ? CLOUD_NAME : 'Boxcoin') . '&AppInfo=' . BXC_USER . '&SecretCode=' . $secret_code;
    }
}

/*
 * -----------------------------------------------------------
 * BLOCKCHAIN
 * -----------------------------------------------------------
 *
 */

function bxc_blockchain($cryptocurrency_code, $action, $extra = false, $address = false) {
    $services = [
        'btc' => [['https://mempool.space/api/', 'address/{R}', 'address/{R}/txs', 'tx/{R}', 'blocks/tip/height', 'mempool'], ['https://blockstream.info/api/', 'address/{R}', 'address/{R}/txs', 'tx/{R}', 'blocks/tip/height', 'blockstream'], ['https://blockchain.info/', 'q/addressbalance/{R}', 'rawaddr/{R}?limit=30', 'rawtx/{R}', 'q/getblockcount', 'blockchain'], 'blockdaemon'],
        'eth' => [['https://api.etherscan.io/api?', 'module=account&action=balance&address={R}', 'module=account&action=txlist&address={R}&startblock=0&endblock=99999999&offset=99&sort=asc', 'module=account&action=txlist&address={R}&startblock=0&endblock=99999999&offset=99&sort=asc', false, 'etherscan'], ['https://api.ethplorer.io/', 'getAddressInfo/{R}', 'getAddressTransactions/{R}?limit=99&showZeroValues=false', 'getTxInfo/{R}', 'getLastBlock', 'ethplorer'], ['https://blockscout.com/eth/mainnet/api?', 'module=account&action=balance&address={R}', 'module=account&action=txlist&address={R}', 'module=transaction&action=gettxinfo&txhash={R}', false, 'blockscout'], 'blockdaemon'],
        'xrp' => ['blockdaemon'],
        'doge' => ['blockcypher', 'blockdaemon'],
        'algo' => [['https://algoindexer.algoexplorerapi.io/v2/', 'accounts/{R}', 'accounts/{R}/transactions?limit=99', 'transactions/{R}', 'accounts/{R}', 'algoexplorerapi'], 'blockdaemon'],
        'bnb' => [['https://api.bscscan.com/api?', 'module=account&action=balance&address={R}', 'module=account&action=txlist&address={R}&startblock=0&endblock=99999999&offset=99&sort=asc', 'module=account&action=txlist&address={R}&startblock=0&endblock=99999999&offset=99&sort=asc', false, 'bscscan']],
        'ltc' => ['tatum', 'blockdaemon', 'blockcypher'],
        'bch' => [['https://rest1.biggestfan.net/v2/address/', 'details/{R}', 'transactions/{R}', 'transactions/{R}', false, 'biggestfan'], 'blockdaemon'],
        'trx' => [['https://apilist.tronscan.org/api/', 'account?address={R}', 'transaction?sort=-timestamp&count=true&limit=99&start=0&address={R}', 'transaction-info?hash={R}', false, 'tronscan'], 'tatum'],
        'sol' => ['blockdaemon'],
        'xmr' => [['https://xmrchain.net/api/', false, 'outputsblocks?address={R}&viewkey={R2}&limit=5&mempool=1', 'outputs?txhash={R}&viewkey={R2}&address={R3}&txprove=0', false, 'xmrchain']]
    ];
    $services_testnet = [
        'btc' => [['https://mempool.space/testnet/api/', 'address/{R}', 'address/{R}/txs', 'tx/{R}', 'blocks/tip/height', 'mempool']],
        'eth' => [['https://api-sepolia.etherscan.io/api?', 'module=account&action=balance&address={R}', 'module=account&action=txlist&address={R}&startblock=0&endblock=99999999&offset=99&sort=asc', 'module=account&action=txlist&address={R}&startblock=0&endblock=99999999&offset=99&sort=asc', false, 'etherscan']],
    ];
    if (bxc_settings_get('testnet-' . bxc_crypto_get_network($cryptocurrency_code)) && isset($services_testnet[$cryptocurrency_code])) {
        $services = $services_testnet;
    }
    $address = $address ? $address : bxc_settings_get_address($cryptocurrency_code);
    $address_lowercase = strtolower($address);
    $cryptocurrency_code_base = bxc_crypto_get_base_code($cryptocurrency_code);
    $return_explorer = $action == 'transaction-explorer';
    if ($return_explorer) {
        $action = 'transaction';
    }

    // Tokens
    $custom_token_code = ['eth' => false, 'trx' => false, 'bsc' => false];
    $custom_token = bxc_isset(bxc_get_custom_tokens(), $cryptocurrency_code);
    $is_token = (in_array($cryptocurrency_code, bxc_get_cryptocurrency_codes('eth')) && $cryptocurrency_code != 'eth') || ($custom_token && $custom_token['type'] == 'erc-20') ? 'eth' : (in_array($cryptocurrency_code, ['usdt_tron']) ? 'trx' : (in_array($cryptocurrency_code, ['busd', 'usdt_bsc']) || $custom_token && $custom_token['type'] == 'bep-20' ? 'bsc' : false));
    if ($is_token) {
        switch ($is_token) {
            case 'eth':
                require_once(__DIR__ . '/web3.php');
                $services = [['https://api.etherscan.io/api?', 'module=account&action=tokenbalance&contractaddress={A}&address={R}&tag=latest', 'module=account&action=tokentx&address={R}&startblock=0&endblock=99999999&offset=99&sort=asc', 'module=account&action=tokentx&address={R}&startblock=0&endblock=99999999&offset=99&sort=asc', false, 'etherscan', 'module=account&action=tokentx&address={R}&startblock=0&endblock=99999999&offset=99&sort=asc'], ['https://api.ethplorer.io/', 'getAddressInfo/{R}', 'getAddressHistory/{R}?limit=99&showZeroValues=false', 'getTxInfo/{R}', false, 'ethplorer', 'getAddressHistory/{R}?limit=99&showZeroValues=false'], ['https://blockscout.com/eth/mainnet/api?', 'module=account&action=tokenbalance&contractaddress={A}&address={R}', 'module=account&action=tokentx&address={R}&offset=99', 'module=account&action=tokentx&address={R}&offset=99', false, 'blockscout', 'module=account&action=tokenlist&address={R}']];
                $contract_address = bxc_eth_get_contract($cryptocurrency_code_base);
                $contract_address = $contract_address ? $contract_address[0] : false;
                break;
            case 'trx':
                $services = $services['trx'];
                $services[0][2] = 'contract/events?address={R}&start=0&limit=30';
                $contract_address = bxc_tron_get_contract_address($cryptocurrency_code_base);
                break;
            case 'bsc':
                $services = [['https://api.bscscan.com/api?', 'module=account&action=tokenbalance&contractaddress={A}&address={R}&tag=latest', 'module=account&action=tokentx&contractaddress={A}&address={R}&startblock=0&endblock=99999999&offset=99&sort=asc', 'module=account&action=tokentx&contractaddress={A}&address={R}&startblock=0&endblock=99999999&offset=99&sort=asc', false, 'bscscan', 'module=account&action=tokentx&address={R}&startblock=0&endblock=99999999&offset=99&sort=asc']];
                $contract_address = bxc_binance_get_contract_address($cryptocurrency_code_base);
                break;
        }
        $custom_token_code[$is_token] = $cryptocurrency_code;
    } else {
        $services = bxc_isset($services, $cryptocurrency_code);
    }
    if ($custom_token) {
        $contract_address = bxc_isset($custom_token, 'contract_address', $contract_address);
    }

    $slugs = false;
    $transactions = [];
    $single_transaction = $action == 'transaction';
    $divider = 1;

    // Custom Blockchain explorer
    $custom_explorer = bxc_settings_get('custom-explorer-active') ? bxc_settings_get('custom-explorer-' . $action . '-url') : false;
    if ($custom_explorer) {
        $path = bxc_settings_get('custom-explorer-' . $action . '-path');
        $data = bxc_curl(str_replace(['{R}', '{N}', '{N2}'], [$single_transaction ? $extra : $address, $cryptocurrency_code, bxc_crypto_name($cryptocurrency_code)], $custom_explorer));
        $data = bxc_get_array_value_by_path($action == 'transactions' ? trim(explode(',', $path)[0]) : $path, json_decode($data, true));
        if ($data) {
            $custom_explorer_divider = 1;
            if (bxc_settings_get('custom-explorer-divider')) {
                $custom_explorer_divider = $cryptocurrency_code == 'eth' ? 1000000000000000000 : 100000000;
            }
            switch ($action) {
                case 'balance':
                    if (is_numeric($data)) {
                        return floatval($data) / $custom_explorer_divider;
                    }
                    break;
                case 'transaction':
                    if (is_array($data) && $data[0]) {
                        return ['time' => $data[0], 'address' => $data[1], 'value' => floatval($data[2]) / $custom_explorer_divider, 'confirmations' => $data[3], 'hash' => $data[4]];
                    }
                    break;
                case 'transactions':
                    if (is_array($data)) {
                        for ($i = 0; $i < count($data); $i++) {
                            $transaction = bxc_get_array_value_by_path($path, $data[$i]);
                            array_push($transactions, ['time' => $transaction[1], 'address' => $transaction[2], 'value' => floatval($transaction[3]) / $custom_explorer_divider, 'confirmations' => $transaction[4], 'hash' => $transaction[5]]);
                        }
                        return $transactions;
                    }
                    break;
            }
        }
    }

    // Multi Network Explorers
    $data_original = false;
    if (empty($services)) {
        return;
    }
    for ($i = 0; $i < count($services); $i++) {
        if (!$return_explorer) {
            if ($services[$i] === 'tatum') {
                $base_url = 'https://api.tatum.io/v3/' . bxc_crypto_get_network($cryptocurrency_code, 'full_name') . '/';
                $header = ['x-api-key: ' . (BXC_CLOUD ? TATUM_API_KEY : bxc_crypto_api_key('tatum'))];
                switch ($action) {
                    case 'balance':
                        if ($cryptocurrency_code == 'usdt_tron') {
                            $json = bxc_curl($base_url . 'account/' . $address);
                            $data = json_decode($json, true);
                            if ($is_token) {
                                $trc_20 = bxc_isset($data, 'trc20');
                                if ($trc_20 && count($trc_20) && isset($trc_20[0][$contract_address])) {
                                    return bxc_decimal_number($trc_20[0][$contract_address] / (10 ** bxc_crypto_get_decimals($cryptocurrency_code)));
                                }
                            } else if (isset($data['balance'])) {
                                return bxc_decimal_number($data['balance'] / (10 ** bxc_crypto_get_decimals($cryptocurrency_code)));
                            }
                        } else {
                            $json = bxc_curl($base_url . 'address/balance/' . $address);
                            $data = json_decode($json, true);
                            if (isset($data['incoming'])) {
                                return bxc_decimal_number($data['incoming'] - $data['outgoing']);
                            }
                        }
                        bxc_error($json, 'tatum');
                        continue 2;
                    case 'transactions':
                        if ($cryptocurrency_code == 'usdt_tron') {
                            $json = bxc_curl($base_url . 'transaction/account/' . $address . ($is_token ? '/trc20' : ''), '', $header);
                            $data = json_decode($json, true);
                            if (isset($data['transactions'])) {
                                $slugs = [false, 'from', 'value', false, 'txID', false];
                                $transactions = $data['transactions'];
                                $transactions_data = [];
                                for ($j = 0; $j < count($transactions); $j++) {
                                    $token_info = $transactions[$i]['tokenInfo'];
                                    if (strtolower($token_info['symbol']) == $cryptocurrency_code_base) {
                                        $divider = 10 ** $token_info['decimals'];
                                        array_push($transactions_data, $transactions[$j]);
                                    }
                                }
                                $data = $transactions_data;
                            } else {
                                bxc_error($json, 'tatum');
                                continue 2;
                            }
                        } else {
                            $json = bxc_curl($base_url . 'transaction/address/' . $address . '?pageSize=30', '', $header);
                            $data = json_decode($json, true);
                            if (is_array($data) && count($data) && isset($data[0]['inputs'])) {
                                $slugs = ['ts', 'from', 'value', false, 'hash', 'blockNumber'];
                                for ($j = 0; $j < count($data); $j++) {
                                    $data[$j]['address'] = $data[$j]['inputs'][0]['coin']['address'];
                                    $data[$j]['value'] = 0;
                                    $outputs = $data[$j]['outputs'];
                                    $total = 0;
                                    for ($y = 0; $y < count($outputs); $y++) {
                                        $value = $outputs[$y]['value'];
                                        $total += $value;
                                        if (strtolower($outputs[$y]['address']) == $address_lowercase) {
                                            $data[$j]['value'] = $value;
                                            break;
                                        }
                                    }
                                    if (!$data[$j]['value']) {
                                        $data[$j]['value'] = $total + $data[$j]['fee'];
                                    }
                                }
                            } else if (isset($data['errorCode'])) {
                                bxc_error($json, 'tatum');
                                continue 2;
                            }
                        }
                        break;
                    case 'transaction':
                        $json = bxc_curl($base_url . 'transaction/' . $extra, '', $header);
                        $data = json_decode($json, true);
                        if ($cryptocurrency_code == 'usdt_tron') {
                            if (isset($data['txID'])) {
                                $slugs = ['time', 'from', 'value', 'confirmations', 'txID', 'blockNumber'];
                                $raw = $data['rawData'];
                                $data['time'] = $raw['timestamp'];
                                $data['from'] = $raw['contract'][0]['parameter']['value']['ownerAddressBase58'];
                                $data['confirmations'] = bxc_isset(json_decode(bxc_curl($base_url . 'info', '', $header), true), 'blockNumber', $data['blockNumber']) - $data['blockNumber'];
                                $data['value'] = bxc_decimal_number(hexdec($data['log'][0]['data']) / (10 ** bxc_crypto_get_decimals($cryptocurrency_code)));
                                $data = [$data];
                            } else {
                                bxc_error($json, 'tatum');
                                continue 2;
                            }
                        } else {
                            if (isset($data['hash'])) {
                                $slugs = ['time', 'from', 'value', 'confirmations', 'hash', 'blockNumber'];
                                $inputs = bxc_isset($data, 'inputs', []);
                                $outputs = bxc_isset($data, 'outputs', []);
                                $data['address'] = count($inputs) ? $inputs[0]['coin']['address'] : '';
                                $data['value'] = 0;
                                $total = 0;
                                $data['confirmations'] = 0;
                                for ($y = 0; $y < count($outputs); $y++) {
                                    $value = $outputs[$y]['value'];
                                    $total += $value;
                                    if (strtolower($outputs[$y]['address']) == $address_lowercase) {
                                        $data['value'] = $value;
                                        break;
                                    }
                                }
                                if (!$data['value']) {
                                    $data['value'] = $total + $data['fee'];
                                }
                                if (!empty($data['blockNumber'])) {
                                    $blocks_count = json_decode(bxc_curl($base_url . 'info', '', $header), true);
                                    $data['confirmations'] = isset($blocks_count['blocks']) ? $blocks_count['blocks'] - $data['blockNumber'] + 1 : 0;
                                }
                                $data = [$data];
                            } else {
                                bxc_error($json, 'tatum');
                                continue 2;
                            }
                        }
                        break;
                }
            } else if ($services[$i] === 'blockdaemon') {
                $base_url = 'https://svc.blockdaemon.com/universal/v1/' . bxc_crypto_name($cryptocurrency_code) . '/mainnet/';
                $header = ['Content-Type: application/json', 'Authorization: Bearer ' . (BXC_CLOUD ? BLOCKDAEMON_API_KEY : bxc_crypto_api_key('blockdaemon'))];
                switch ($action) {
                    case 'balance':
                        $json = bxc_curl($base_url . 'account/' . $address, '', $header);
                        $data = json_decode($json, true);
                        if (is_array($data) && isset($data[0]['confirmed_balance'])) {
                            return bxc_decimal_number($data[0]['confirmed_balance'] / (10 ** $data[0]['currency']['decimals']));
                        }
                        bxc_error($json, 'blockdaemon');
                        continue 2;
                    case 'transactions':
                    case 'transaction':
                        $json = bxc_curl($base_url . ($single_transaction ? 'tx/' . $extra : 'account/' . $address . '/txs'), '', $header);
                        $data = json_decode($json, true);
                        $transaction_status = bxc_isset($data, 'status');
                        if ($data) {
                            if ($single_transaction) {
                                if (isset($data['events'])) {
                                    $data = [$data];
                                }
                            } else if (isset($data['data'])) {
                                $data = $data['data'];
                            }
                        }
                        if (is_array($data)) {
                            if (count($data) && isset($data[0]['events'])) {
                                $slugs = ['date', 'address', 'value', 'confirmations', 'id', 'block_number'];
                                for ($j = 0; $j < count($data); $j++) {
                                    $events = $data[$j]['events'];
                                    $transaction_value = 0;
                                    $sender_address = '';
                                    for ($y = 0; $y < count($events); $y++) {
                                        switch ($cryptocurrency_code) {
                                            case 'btc':
                                                if (!empty($events[$y]['meta']) && !empty($events[$y]['meta']['addresses'])) {
                                                    $event_address = $events[$y]['meta']['addresses'][0];
                                                    $amount = $events[$y]['amount'];
                                                    if ($events[$y]['type'] == 'utxo_output' && strtolower($event_address) == $address_lowercase) {
                                                        $transaction_value += $amount;
                                                        if (isset($events[$y]['decimals'])) {
                                                            $divider = 10 ** $events[$y]['decimals'];
                                                        }
                                                    } else if ($events[$y]['type'] == 'utxo_input') {
                                                        $sender_address = $event_address;
                                                    }
                                                }
                                                break;
                                            case 'sol':
                                            case 'xrp':
                                            case 'bch':
                                            case 'algo':
                                            case 'ltc':
                                            case 'doge':
                                            case 'eth':
                                                $get_address = false;
                                                if (strtolower(bxc_isset($events[$y], 'destination')) == $address_lowercase) {
                                                    $transaction_value += $events[$y]['amount'];
                                                    $get_address = true;
                                                    if (isset($events[$y]['decimals'])) {
                                                        $divider = 10 ** $events[$y]['decimals'];
                                                    }
                                                } else if (bxc_isset($events[$y], 'type') == 'utxo_input') {
                                                    $get_address = true;
                                                }
                                                if ($get_address && !empty($events[$y]['source'])) {
                                                    $sender_address = $events[$y]['source'];
                                                }
                                                break;
                                        }
                                    }
                                    if ($single_transaction && $cryptocurrency_code == 'sol' && $transaction_status == 'completed') {
                                        $data[$j]['confirmations'] = 9999;
                                    }
                                    $data[$j]['value'] = $transaction_value;
                                    $data[$j]['address'] = $sender_address;
                                }
                            }
                        } else {
                            bxc_error($json, 'blockdaemon');
                            continue 2;
                        }
                        break;
                }
            } else if ($services[$i] === 'blockcypher') {
                $base_url = 'https://api.blockcypher.com/v1/' . $cryptocurrency_code . '/main/';
                switch ($action) {
                    case 'balance':
                        $json = bxc_curl($base_url . 'addrs/' . $address);
                        $data = json_decode($json, true);
                        if ($data && isset($data['balance'])) {
                            return bxc_decimal_number($data['balance'] / (10 ** bxc_crypto_get_decimals($cryptocurrency_code)));
                        }
                        bxc_error($json, 'blockcypher');
                        continue 2;
                    case 'transactions':
                    case 'transaction':
                        $json = bxc_curl($base_url . ($single_transaction ? 'txs/' . $extra : 'addrs/' . $address . '/full'));
                        $data = json_decode($json, true);
                        if ($data) {
                            if ($single_transaction) {
                                if (isset($data['hash']))
                                    $data = [$data];
                            } else if (isset($data['txs']))
                                $data = $data['txs'];
                        }
                        if ($data && is_array($data)) {
                            if (count($data)) {
                                $slugs = ['time', 'address', 'value', 'confirmations', 'hash', 'block_height'];
                                $divider = 10 ** bxc_crypto_get_decimals($cryptocurrency_code_base);
                                for ($j = 0; $j < count($data); $j++) {
                                    $outputs = bxc_isset($data[$j], 'outputs', []);
                                    $data[$j]['time'] = strtotime($data[$j]['received']);
                                    $data[$j]['address'] = $data[$j]['inputs'][0]['addresses'][0];
                                    $data[$j]['value'] = 0;
                                    for ($y = 0; $y < count($outputs); $y++) {
                                        if (strtolower($outputs[$y]['addresses'][0]) == $address_lowercase) {
                                            $data[$j]['value'] = $outputs[$y]['value'];
                                            break;
                                        }
                                    }
                                }
                            }
                        } else {
                            bxc_error($json, 'blockcypher');
                            continue 2;
                        }
                        break;
                }
            }
        }

        // Other explorers
        if (!in_array($services[$i], ['tatum', 'blockdaemon', 'blockcypher'])) {
            $url_part = $services[$i][$action == 'balance' ? 1 : ($action == 'transactions' ? 2 : ($single_transaction ? 3 : 4))];
            if ($url_part === false) {
                continue;
            }
            $url = $services[$i][0] . str_replace('{R}', $single_transaction && !in_array($services[$i][5], ['etherscan', 'bscscan', 'biggestfan']) ? $extra : $address, $url_part);
            if ($cryptocurrency_code == 'xmr' && $services[$i][5] == 'xmrchain') {
                $url = str_replace('{R2}', bxc_settings_get('xmr-secret-view-key'), $url);
                $url = str_replace('{R3}', $address, $url);
            }
            if ($is_token) {
                $url = str_replace('{A}', $contract_address, $url);
            }
            $data = $data_original = bxc_curl(bxc_crypto_api_key($services[$i][5], $url));
            switch ($cryptocurrency_code) {
                case 'btc':
                    switch ($action) {
                        case 'balance':
                            $data = json_decode($data, true);
                            switch ($i) {
                                case 0:
                                case 1:
                                    if (isset($data['chain_stats'])) {
                                        return ($data['chain_stats']['funded_txo_sum'] - $data['chain_stats']['spent_txo_sum']) / 100000000;
                                    }
                                    break;
                                case 2:
                                    if (is_numeric($data)) {
                                        return intval($data) / 100000000;
                                    }
                                    break;
                            }
                            break;
                        case 'transaction':
                        case 'transactions':
                            $data = json_decode($data, true);
                            $input_slug = false;
                            $output_slug = false;
                            $confirmations = false;
                            $continue = false;

                            // Get transaction and verify the API is working
                            switch ($i) {
                                case 0:
                                case 1:
                                    if (is_array($data) && empty($data['error'])) {
                                        $output_slug = 'vout';
                                        $input_slug = 'vin';
                                        $continue = true;
                                    }
                                    break;
                                case 2:
                                    if (($single_transaction && isset($data['inputs'])) || isset($data['txs'])) {
                                        if (!$single_transaction)
                                            $data = $data['txs'];
                                        $input_slug = 'inputs';
                                        $output_slug = 'out';
                                        $continue = true;
                                    }
                                    break;
                            }
                            if ($continue) {
                                $slugs = ['time', 'address', 'value', 'confirmations', 'hash', 'block_height'];
                                $sender_address = '';
                                $time = 0;
                                $block_height = 0;
                                $hash = '';
                                $divider = $i === 1 ? 1 : 100000000;
                                if ($single_transaction)
                                    $data = [$data];

                                // Get transactions details
                                for ($j = 0; $j < count($data); $j++) {
                                    $transaction_value = 0;
                                    switch ($i) {
                                        case 0:
                                        case 1:
                                            if (bxc_isset($data[$j]['status'], 'confirmed')) {
                                                $time = $data[$j]['status']['block_time'];
                                                $block_height = $data[$j]['status']['block_height'];
                                            }
                                            $hash = $data[$j]['txid'];
                                            break;
                                        case 2:
                                            $time = $data[$j]['time'];
                                            $block_height = $data[$j]['block_height'];
                                            $hash = $data[$j]['hash'];
                                            break;
                                    }

                                    // Get transaction amount
                                    $outputs = $output_slug ? $data[$j][$output_slug] : [];
                                    for ($y = 0; $y < count($outputs); $y++) {
                                        switch ($i) {
                                            case 0:
                                            case 1:
                                                $value = $outputs[$y]['value'];
                                                $output_address = bxc_isset($outputs[$y], 'scriptpubkey_address');
                                                break;
                                            case 2:
                                                $value = $outputs[$y]['value'];
                                                $output_address = $outputs[$y]['addr'];
                                                break;
                                        }
                                        if (strtolower($output_address) == $address_lowercase) {
                                            $transaction_value += $value;
                                        }
                                        $outputs[$y] = ['value' => $value, 'address' => $output_address];
                                    }

                                    // Get sender address
                                    $input = bxc_isset($data[$j], $input_slug);
                                    if ($input && count($input)) {
                                        $input = $input[0];
                                        switch ($i) {
                                            case 0:
                                            case 1:
                                                $sender_address = $input['prevout']['scriptpubkey_address'];
                                                break;
                                            case 2:
                                                $sender_address = $input['prev_out']['addr'];
                                                break;
                                        }
                                    }

                                    // Assign transaction values
                                    $data[$j]['time'] = $time;
                                    $data[$j]['address'] = $sender_address;
                                    $data[$j]['confirmations'] = $confirmations;
                                    $data[$j]['value'] = $transaction_value;
                                    $data[$j]['hash'] = $hash;
                                    $data[$j]['block_height'] = $block_height;
                                }
                            }
                            break;
                        case 'blocks_count':
                            if (is_numeric($data)) {
                                return intval($data);
                            }
                    }
                    break;
                case $custom_token_code['eth']:
                case 'link':
                case 'shib':
                case 'bat':
                case 'usdt':
                case 'usdc':
                case 'eth':
                    $data = json_decode($data, true);
                    switch ($action) {
                        case 'balance':
                            switch ($i) {
                                case 2:
                                case 0:
                                    $data = bxc_isset($data, 'result');
                                    if (is_numeric($data)) {
                                        require_once(__DIR__ . '/web3.php');
                                        return floatval($data) / ($is_token ? 10 ** ($custom_token ? $custom_token['decimals'] : bxc_eth_get_contract($cryptocurrency_code)[1]) : 1000000000000000000);
                                    }
                                    break;
                                case 1:
                                    if ($is_token) {
                                        $data = bxc_isset($data, 'tokens', []);
                                        for ($j = 0; $j < count($data); $j++) {
                                            if (strtolower(bxc_isset(bxc_isset($data, 'tokenInfo'), 'symbol')) == $cryptocurrency_code) {
                                                return floatval($data['balance']) / (10 ** intval($data['tokenInfo']['decimals']));
                                            }
                                        }
                                    } else {
                                        $data = bxc_isset(bxc_isset($data, 'ETH'), 'balance');
                                        if (is_numeric($data)) {
                                            return floatval($data);
                                        }
                                    }
                                    break;
                            }
                            break;
                        case 'transaction':
                        case 'transactions':
                            switch ($i) {
                                case 2:
                                case 0:
                                    $data = bxc_isset($data, 'result');
                                    if (is_array($data)) {
                                        $count = count($data);
                                        $slugs = ['timeStamp', 'from', 'value', 'confirmations', 'hash', 'blockNumber'];
                                        $divider = $is_token ? 1000000 : 1000000000000000000;
                                        if ($single_transaction) {
                                            if ($i === 0) {
                                                $data_single = [];
                                                for ($j = 0; $j < $count; $j++) {
                                                    if ($data[$j]['hash'] == $extra) {
                                                        $data_single = [$data[$j]];
                                                        break;
                                                    }
                                                }
                                                $data = $data_single;
                                            } else {
                                                $data = [$data];
                                            }
                                        } else if ($is_token) {
                                            $data_temp = [];
                                            for ($j = 0; $j < $count; $j++) {
                                                if (strtolower($data[$j]['tokenSymbol']) == $cryptocurrency_code) {
                                                    array_push($data_temp, $data[$j]);
                                                }
                                            }
                                            $data = $data_temp;
                                        }
                                        if ($count && isset($data[0]['tokenDecimal'])) {
                                            $divider = 10 ** intval($data[0]['tokenDecimal']);
                                        }
                                    }
                                    break;
                                case 1:
                                    if ($single_transaction || is_array($data) || $is_token) {
                                        $slugs = ['timestamp', 'from', 'value', 'confirmations', 'hash', 'blockNumber'];
                                        if ($single_transaction) {
                                            $data = [$data];
                                        }
                                    }
                                    if ($is_token) {
                                        if ($single_transaction) {
                                            if (count($data)) {
                                                $transaction_value = 0;
                                                if (isset($data[0]['operations'])) {
                                                    $operations = $data[0]['operations'];
                                                    $address = strtolower($address);
                                                    for ($j = 0; $j < count($operations); $j++) {
                                                        if ($operations[$j]['type'] == 'transfer' && strtolower($operations[$j]['to']) == $address_lowercase) {
                                                            $transaction_value += $operations[$j]['value'];
                                                        }
                                                    }
                                                    $divider = 10 ** intval($operations[0]['tokenInfo']['decimals']);
                                                    $data[0]['value'] = $transaction_value;
                                                }
                                            }
                                        } else {
                                            $data = bxc_isset($data, 'operations', []);
                                            $data_temp = [];
                                            for ($j = 0; $j < count($data); $j++) {
                                                if (strtolower($data[$j]['tokenInfo']['symbol']) == $cryptocurrency_code) {
                                                    array_push($data_temp, $data[$j]);
                                                    $divider = 10 ** intval($data[$j]['tokenInfo']['decimals']);
                                                }
                                            }
                                            $slugs[4] = 'transactionHash';
                                            $data = $data_temp;
                                        }
                                    }
                                    break;
                            }
                            if ($slugs && ((!is_array($data) && !$data) || (count($data) && (!isset($data[0]) || !bxc_isset($data[0], $slugs[0]))))) {
                                $slugs = false;
                            }
                            break;
                        case 'blocks_count':
                            switch ($i) {
                                case 1:
                                    if (is_numeric($data['lastBlock'])) {
                                        return intval($data['lastBlock']);
                                    }
                                    break;
                            }
                    }
                    break;
                case 'doge':
                    $data = json_decode($data, true);
                    switch ($action) {
                        case 'balance':
                            switch ($i) {
                                case 0:
                                    $data = bxc_isset($data, 'data');
                                    if ($data && isset($data['confirmed_balance'])) {
                                        return $data['confirmed_balance'];
                                    }
                                    break;
                            }
                            break;
                        case 'transaction':
                        case 'transactions':
                            switch ($i) {
                                case 0:
                                    $data = bxc_isset($data, 'data');
                                    if ($data) {
                                        if (!$single_transaction) {
                                            $data = bxc_isset($data, 'txs');
                                        }
                                        $slugs = ['time', 'address', 'value', 'confirmations', 'txid', false];
                                    } else if (is_array($data)) {
                                        return [];
                                    }
                                    break;
                            }
                            if ($slugs) {
                                if (is_array($data)) {
                                    if ($single_transaction && ($i === 0 || $i === 1)) {
                                        $data['address'] = $data['inputs'][0]['address'];
                                        $outputs = $data['outputs'];
                                        for ($j = 0; $j < count($outputs); $j++) {
                                            if (strtolower($outputs[$j]['address']) == $address_lowercase) {
                                                $data['value'] = $outputs[$j]['value'];
                                                break;
                                            }
                                        }
                                        $data = [$data];
                                    }
                                }
                                if ((!is_array($data) && !$data) || (count($data) && (!isset($data[0]) || (!bxc_isset($data[0], $slugs[0]) && !bxc_isset($data[0], $slugs[1]))))) {
                                    $slugs = false;
                                }
                            }
                            break;
                        case 'blocks_count':
                            switch ($i) {
                                case 0:
                                    if (is_numeric($data['lastBlock'])) {
                                        return intval($data['lastBlock']);
                                    }
                                    break;
                            }
                    }
                    break;
                case 'algo':
                    $data = json_decode($data, true);
                    switch ($action) {
                        case 'balance':
                            switch ($i) {
                                case 0:
                                    $data = bxc_isset(bxc_isset($data, 'account'), 'amount');
                                    if (is_numeric($data)) {
                                        return floatval($data) / 1000000;
                                    }
                                    break;
                            }
                            break;
                        case 'transaction':
                        case 'transactions':
                            switch ($i) {
                                case 0:
                                    $current_round = bxc_isset($data, 'current-round');
                                    $data = bxc_isset($data, $single_transaction ? 'transaction' : 'transactions');
                                    if ($data) {
                                        $slugs = ['round-time', 'sender', 'amount', 'confirmations', 'id', 'confirmed-round'];
                                        $divider = 1000000;
                                        if ($single_transaction) {
                                            $data['amount'] = bxc_isset(bxc_isset($data, 'payment-transaction'), 'amount', -1);
                                            $data['confirmations'] = $current_round - bxc_isset($data, 'confirmed-round');
                                            $data = [$data];
                                        } else {
                                            for ($j = 0; $j < count($data); $j++) {
                                                $data[$j]['amount'] = bxc_isset(bxc_isset($data[$j], 'payment-transaction'), 'amount', -1);
                                                $data[$j]['confirmations'] = $current_round - bxc_isset($data[$j], 'confirmed-round');
                                            }
                                        }
                                    } else if (is_array($data)) {
                                        return [];
                                    }
                                    break;
                            }
                            break;
                        case 'blocks_count':
                            switch ($i) {
                                case 1:
                                    if (is_numeric($data['current-round'])) {
                                        return intval($data['current-round']);
                                    }
                                    break;
                            }
                    }
                    break;
                case $custom_token_code['bsc']:
                case 'usdt_bsc':
                case 'busd':
                case 'bnb':
                    $data = json_decode($data, true);
                    switch ($action) {
                        case 'balance':
                            switch ($i) {
                                case 0:
                                    $data = bxc_isset($data, 'result');
                                    if (is_numeric($data)) {
                                        return floatval($data) / 1000000000000000000;
                                    }
                                    break;
                            }
                            break;
                        case 'transaction':
                        case 'transactions':
                            switch ($i) {
                                case 0:
                                    $data = bxc_isset($data, 'result');
                                    if (is_array($data)) {
                                        $slugs = ['timeStamp', 'from', 'value', 'confirmations', 'hash', 'blockNumber'];
                                        $divider = 1000000000000000000;
                                        if ($single_transaction) {
                                            if ($i === 0) {
                                                $data_single = [];
                                                for ($j = 0; $j < count($data); $j++) {
                                                    if ($data[$j]['hash'] == $extra) {
                                                        $data_single = [$data[$j]];
                                                        break;
                                                    }
                                                }
                                                $data = $data_single;
                                            } else {
                                                $data = [$data];
                                            }
                                        }
                                    }
                                    break;
                            }
                            break;
                    }
                    break;
                case 'ltc':
                    $data = json_decode($data, true);
                    switch ($action) {
                        case 'balance':
                            switch ($i) {
                                case 0:
                                    $data = bxc_isset($data, 'data');
                                    if ($data && isset($data['confirmed_balance'])) {
                                        return $data['confirmed_balance'];
                                    }
                                    break;
                            }
                            break;
                        case 'transaction':
                        case 'transactions':
                            switch ($i) {
                                case 0:
                                    $data = bxc_isset($data, 'data');
                                    if ($data) {
                                        if (!$single_transaction) {
                                            $data = bxc_isset($data, 'txs');
                                        }
                                        $slugs = ['time', 'address', 'value', 'confirmations', 'txid', false];
                                    } else if (is_array($data))
                                        return [];
                                    break;
                            }
                            if ($slugs) {
                                if (is_array($data)) {
                                    if ($single_transaction && ($i === 0 || $i === 1)) {
                                        $data['address'] = $data['inputs'][0]['address'];
                                        $outputs = $data['outputs'];
                                        for ($j = 0; $j < count($outputs); $j++) {
                                            if (strtolower($outputs[$j]['address']) == $address_lowercase) {
                                                $data['value'] = $outputs[$j]['value'];
                                                break;
                                            }
                                        }
                                        $data = [$data];
                                    }
                                }
                                if ((!is_array($data) && !$data) || (count($data) && (!isset($data[0]) || (!bxc_isset($data[0], $slugs[0]) && !bxc_isset($data[0], $slugs[1]))))) {
                                    $slugs = false;
                                }
                            }
                            break;
                    }
                    break;
                case 'bch':
                    $data = json_decode($data, true);
                    switch ($action) {
                        case 'balance':
                            switch ($i) {
                                case 0:
                                    $data = bxc_isset($data, 'balance');
                                    if ($data) {
                                        return $data;
                                    }
                                    break;
                            }
                            break;
                        case 'transaction':
                        case 'transactions':
                            switch ($i) {
                                case 0:
                                    $data = bxc_isset($data, 'txs');
                                    if ($data) {
                                        $slugs = ['time', 'address', 'value', 'confirmations', 'txid', false];
                                    } else if (is_array($data)) {
                                        return [];
                                    }
                                    break;
                            }
                            if ($slugs) {
                                if (is_array($data)) {
                                    for ($j = 0; $j < count($data); $j++) {
                                        $data_transaction = $data[$j][0];
                                        $data_transaction['address'] = str_replace('bitcoincash:', '', $data_transaction['vin'][0]['cashAddress']);
                                        $outputs = $data_transaction['vout'];
                                        $address_prefix = 'bitcoincash:' . $address;
                                        for ($y = 0; $y < count($outputs); $y++) {
                                            if (strtolower($outputs[$y]['scriptPubKey']['addresses'][0]) == $address_prefix) {
                                                $data_transaction['value'] = $outputs[$y]['value'];
                                                break;
                                            }
                                        }
                                        $data[$j] = $data_transaction;
                                    }
                                    if ($single_transaction) {
                                        for ($j = 0; $j < count($data); $j++) {
                                            if ($data[$j]['txid'] == $extra) {
                                                $data = [$data[$j]];
                                                break;
                                            }
                                        }
                                    }
                                }
                                if ((!is_array($data) && !$data) || (count($data) && (!isset($data[0]) || (!bxc_isset($data[0], $slugs[0]) && !bxc_isset($data[0], $slugs[1]))))) {
                                    $slugs = false;
                                }
                            }
                            break;
                    }
                    break;
                case $custom_token_code['trx']:
                case 'trx':
                case 'usdt_tron':
                    $data = json_decode($data, true);
                    if (isset($data)) {
                        switch ($action) {
                            case 'balance':
                                switch ($i) {
                                    case 0:
                                        if ($is_token) {
                                            $data = bxc_isset($data, 'trc20token_balances');
                                            if (is_array($data)) {
                                                $cryptocurrency_code = bxc_crypto_get_base_code($cryptocurrency_code);
                                                for ($j = 0; $j < count($data); $j++) {
                                                    if (strtolower($data[$j]['tokenAbbr']) == $cryptocurrency_code) {
                                                        return floatval($data[$j]['balance']) / 1000000;
                                                    }
                                                }
                                            }
                                        }
                                        break;
                                }
                                break;
                            case 'transaction':
                                switch ($i) {
                                    case 0:
                                        if (isset($data['contractData']) && bxc_isset($data['contractData'], 'contract_address') == $contract_address && isset($data['trigger_info'])) {
                                            $data['value'] = $data['trigger_info']['parameter']['_value'];
                                            $divider = 10 ** $data['tokenTransferInfo']['decimals'];
                                            $data = [$data];
                                            $slugs = ['timestamp', 'ownerAddress', 'value', 'confirmations', 'hash', 'block'];
                                        }
                                        break;
                                }
                                break;
                            case 'transactions':
                                switch ($i) {
                                    case 0:
                                        if (isset($data['data'])) {
                                            $data = $data['data'];
                                            $transactions_data = [];
                                            if ($is_token) {
                                                for ($j = 0; $j < count($data); $j++) {
                                                    $data[$j]['amount'] = bxc_decimal_number($data[$j]['amount'] / (10 ** $data[$j]['decimals']));
                                                    $data[$j]['timestamp'] = $data[$j]['timestamp'] / 1000;
                                                }
                                            }
                                            $slugs = ['timestamp', 'transferFromAddress', 'amount', 'confirmed', 'transactionHash', 'block'];
                                        }
                                        break;
                                }
                                break;
                        }
                    }
                    break;
                case 'xmr':
                    $data = json_decode($data, true);
                    switch ($action) {
                        case 'balance':
                            return 0;
                        case 'transaction':
                        case 'transactions':
                            switch ($i) {
                                case 0:
                                    $data = bxc_isset($data, 'data', []);
                                    $divider = 1000000000000;
                                    if ($single_transaction) {
                                        $slugs = ['tx_timestamp', 'address', 'amount', 'tx_confirmations', 'tx_hash', false];
                                        $data['amount'] = bxc_isset($data, 'outputs')[0]['amount'];
                                        $data = [$data];
                                    } else {
                                        $slugs = [false, false, 'amount', false, 'tx_hash', false];
                                        $data = bxc_isset($data, 'outputs', []);
                                    }
                                    break;
                            }
                            break;
                    }
                    break;
            }
        }

        // Add the transactions
        if ($slugs) {
            $transactions = [];
            $count = count($data);
            if ($count) {
                for ($j = 0; $j < $count; $j++) {
                    $transaction = $data[$j];
                    array_push($transactions, ['time' => bxc_isset($transaction, $slugs[0]), 'address' => bxc_isset($transaction, $slugs[1], ''), 'value' => bxc_decimal_number($transaction[$slugs[2]] / $divider), 'confirmations' => bxc_isset($transaction, $slugs[3], 0), 'hash' => $transaction[$slugs[4]], 'block_height' => bxc_isset($transaction, $slugs[5], '')]);
                }
                if ($single_transaction) {
                    $transactions[0]['explorer'] = is_string($services[$i]) ? $services[$i] : $services[$i][5];
                }
                return $single_transaction ? $transactions[0] : $transactions;
            }
            return [];
        }
    }
    return $data_original;
}

function bxc_node_rpc($cryptocurrency_code, $method_name, $parameters) {
    $cryptocurrency_code = bxc_crypto_get_network($cryptocurrency_code);
    $node_url = bxc_settings_get($cryptocurrency_code . '-node-url');
    $node_headers = bxc_settings_get($cryptocurrency_code . '-node-headers', []);
    if (!$node_url) {
        bxc_error(bxc_crypto_name($cryptocurrency_code, true) . ' node not found', 'bxc_btc_curl', true);
    }
    if ($node_headers) {
        $node_headers = explode(',', $node_headers);
    }
    $post_fields = ['method' => $method_name, 'params' => $parameters, 'jsonrpc' => '2.0'];
    if ($cryptocurrency_code == 'eth') {
        $post_fields['id'] = 1;
    }
    $response_json = bxc_curl($node_url, json_encode($post_fields), array_merge(['accept: application/json', 'content-type: application/json'], $node_headers), 'POST');
    $response = json_decode($response_json, true);
    $result = bxc_isset($response, 'result');
    if (!$result) {
        bxc_error($response_json, 'bxc_node_rpc');
    }
    return $result ? $result : ($response ? $response : $response_json);
}

/*
 * -----------------------------------------------------------
 * FIAT
 * -----------------------------------------------------------
 *
 */

function bxc_stripe_payment($price_amount, $checkout_url, $client_reference_id, $currency_code = false, $description = false) {
    $response = bxc_stripe_create_session(bxc_stripe_get_price($price_amount, $currency_code)['id'], $checkout_url, $client_reference_id, $description);
    return isset($response['url']) ? $response['url'] : $response;
}

function bxc_stripe_get_price($price_amount, $currency_code = false) {
    $price_amount = intval($price_amount);
    $product_id = bxc_settings_get('stripe-product-id');
    $prices = bxc_stripe_curl('prices?product=' . $product_id . '&limit=100&type=one_time', 'GET');
    $currency_code = strtolower($currency_code);
    if (!isset($prices['data'])) {
        return $prices;
    }
    $prices = $prices['data'];
    for ($i = 0; $i < count($prices); $i++) {
        if ($price_amount == $prices[$i]['unit_amount'] && $prices[$i]['currency'] == $currency_code) {
            return $prices[$i];
        }
    }
    return bxc_stripe_curl('prices?unit_amount=' . $price_amount . '&currency=' . ($currency_code ? $currency_code : bxc_settings_get('currency')) . '&product=' . $product_id);
}

function bxc_stripe_create_session($price_id, $checkout_url, $client_reference_id = false, $description = false) {
    parse_str(bxc_isset(parse_url($checkout_url), 'query'), $attributes);
    $checkout_url = str_replace(['payment_status=cancelled', isset($attributes['cc']) ? 'cc=' . $attributes['cc'] : '', isset($attributes['pay']) ? 'pay=' . $attributes['pay'] : ''], '', $checkout_url);
    while (in_array(substr($checkout_url, -1), ['&', '?'])) {
        $checkout_url = substr($checkout_url, 0, -1);
    }
    $checkout_url = str_replace(['?&', '&&'], ['?', '&'], $checkout_url);
    return bxc_stripe_curl('checkout/sessions?' . (bxc_settings_get('stripe-tax') ? 'automatic_tax[enabled]=true&' : '') . 'metadata[source]=boxcoin' . (BXC_CLOUD ? '&metadata[cloud]=' . bxc_cloud_get_data() : '') . '&cancel_url=' . urlencode($checkout_url . (strpos($checkout_url, '?') ? '&' : '?') . 'payment_status=cancelled') . '&success_url=' . bxc_payment_redirect_url($checkout_url, $client_reference_id) . '&line_items[0][price]=' . $price_id . '&mode=payment&line_items[0][quantity]=1&client_reference_id=' . $client_reference_id . ($description ? '&payment_intent_data[description]=' . urlencode(str_replace(['&', '=', '?'], '', $description)) : ''));
}

function bxc_stripe_curl($url_part, $type = 'POST') {
    $response = bxc_curl('https://api.stripe.com/v1/' . $url_part, '', ['Authorization: Basic ' . base64_encode(bxc_settings_get('stripe-key'))], $type);
    $response = json_decode($response, true);
    if (isset($response['error'])) {
        bxc_error($response['error'], 'bxc_stripe_curl');
    }
    return $response;
}

function bxc_stripe_get_divider($currency_code) {
    return in_array(strtoupper($currency_code), ['BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF']) ? 1 : 100;
}

function bxc_verifone_create_checkout($price_amount, $checkout_url, $client_reference_id, $title, $currency_code = false) {
    $url = 'https://secure.2checkout.com/checkout/buy?currency=' . ($currency_code ? $currency_code : bxc_settings_get('currency')) . '&dynamic=1&merchant=' . bxc_settings_get('verifone-merchant-id') . '&order-ext-ref=' . bxc_encryption($client_reference_id . '|||' . bxc_settings_get('verifone-key')) . '&price=' . $price_amount . '&prod=' . $title . '&qty=1&return-type=redirect&return-url=' . bxc_payment_redirect_url($checkout_url, $client_reference_id) . '&type=digital';
    return $url . '&signature=' . bxc_verifone_get_signature($url);
}

function bxc_verifone_get_signature($url) {
    parse_str(substr($url, strpos($url, '?') + 1), $values);
    $serialized = '';
    foreach ($values as $key => $value) {
        if (!in_array($key, ['merchant', 'dynamic', 'email'])) {
            $serialized .= mb_strlen($value) . $value;
        }
    }
    return hash_hmac('sha256', $serialized, bxc_settings_get('verifone-word'));
}

function bxc_verifone_curl($url_part, $type = 'POST') {
    $merchant_id = bxc_settings_get('verifone-merchant-id');
    $date = gmdate('Y-m-d H:i:s');
    $string = strlen($merchant_id) . $merchant_id . strlen($date) . $date;
    $hash = hash_hmac('md5', $string, bxc_settings_get('verifone-key'));
    $response = bxc_curl('https://api.2checkout.com/rest/6.0/' . $url_part, '', ['Content-Type: application/json', 'Accept: application/json', 'X-Avangate-Authentication: code="' . $merchant_id . '" date="' . $date . '" hash="' . $hash . '"'], $type);
    return is_string($response) ? json_decode($response, true) : $response;
}

function bxc_vat($amount, $country_code = false, $currency_code = false, $vat_number = false) {
    $ip = $country_code ? ['countryCode' => $country_code] : bxc_ip_info('country,countryCode');
    if (isset($ip['countryCode']) && (!$vat_number || !bxc_settings_get('vat-validation') || !bxc_vat_validation($vat_number))) {
        $rates = json_decode(file_get_contents(__DIR__ . '/resources/vat.json'), true)['rates'];
        for ($i = 0; $i < count($rates); $i++) {
            if ($rates[$i]['country_code'] == $ip['countryCode']) {
                $amount = floatval($amount);
                $rate_percentage = $rates[$i]['standard']['rate'];
                $rate = $amount * ($rate_percentage / 100);
                return [round($amount + $rate, 2), round($rate, 2), $rates[$i]['country_code'], $rates[$i]['country_name'], str_replace(['{1}', '{2}'], [strtoupper($currency_code), round($rate, 2)], bxc_('Including {1} {2} for VAT in')) . ' ' . bxc_($rates[$i]['country_name']), $rate_percentage];
            }
        }
    }
    return [$amount ? $amount : 0, 0, '', bxc_isset($ip, 'country', ''), '', 0];
}

function bxc_vat_validation($vat_number) {
    $key = bxc_settings_get('vatsense-key');
    if (!$key) {
        return bxc_error('Missing Vatsense key. Set it in the Boxcoin settings area.', 'bxc_vat_validation', true);
    }
    $vat_numbers = json_decode(bxc_settings_db('vat-numbers', false, '[]'), true);
    if (isset($vat_numbers[$vat_number])) {
        return $vat_numbers[$vat_number];
    }
    $response = json_decode(bxc_curl('https://api.vatsense.com/1.0/validate?vat_number=' . $vat_number, '', ['CURLOPT_USERPWD' => 'user:' . $key]), true);
    if ($response && bxc_isset($response, 'success')) {
        $vat_numbers[$vat_number] = bxc_isset(bxc_isset($response, 'data', []), 'valid');
        bxc_settings_db('vat-numbers', $vat_numbers);
        return $vat_numbers[$vat_number];
    } else {
        bxc_error($response, 'bxc_vat_validation');
    }
    return false;
}

function bxc_paypal_get_checkout_url($transaction_id, $checkout_url, $amount, $currency_code, $title = '') {
    parse_str($checkout_url, $checkout_url_paramaters);
    $checkout_url = isset($checkout_url_paramaters['redirect']) && isset($checkout_url_paramaters['pay']) ? $checkout_url_paramaters['redirect'] : $checkout_url;
    $data = [
        'cmd' => '_xclick',
        'item_number' => $transaction_id,
        'business' => bxc_settings_get('paypal-email'),
        'return' => bxc_payment_redirect_url($checkout_url, $transaction_id, false),
        'cancel_return' => $checkout_url . (strpos($checkout_url, '?') ? '&' : '?') . 'payment_status=cancelled',
        'notify_url' => BXC_URL . 'paypal.php',
        'item_name' => empty($title) ? bxc_('Transaction') . ' ' . $transaction_id : $title,
        'amount' => $amount,
        'currency_code' => strtoupper($currency_code),
        'custom' => $transaction_id . (BXC_CLOUD ? '|' . bxc_cloud_get_data() : '')
    ];
    return 'https://www.' . (bxc_settings_get('paypal-sandbox') ? 'sandbox.' : '') . 'paypal.com/cgi-bin/webscr?' . http_build_query($data);
}

/*
 * -----------------------------------------------------------
 * EXCHANGES
 * -----------------------------------------------------------
 *
 */

function bxc_gemini_curl($url_part, $parameters = [], $type = 'POST') {
    $signature = base64_encode(utf8_encode(json_encode(array_merge(['request' => '/v1/' . $url_part, 'nonce' => time()], $parameters))));
    $header = [
        'Content-Type: text/plain',
        'Content-Length: 0',
        'X-GEMINI-APIKEY: ' . bxc_settings_get('gemini-key'),
        'X-GEMINI-PAYLOAD: ' . $signature,
        'X-GEMINI-SIGNATURE: ' . hash_hmac('sha384', $signature, utf8_encode(bxc_settings_get('gemini-key-secret'))),
        'Cache-Control: no-cache'
    ];
    return json_decode(bxc_curl('https://api' . (bxc_settings_get('gemini-sandbox') ? '.sandbox' : '') . '.gemini.com/v1/' . $url_part, '', $header, $type), true);
}

function bxc_gemini_convert_to_fiat($cryptocurrency_code, $amount) {
    $symbol = strtolower($cryptocurrency_code . bxc_settings_get('gemini-conversion-currency'));
    $symbol_uppercase = strtoupper($symbol);
    $price = json_decode(bxc_curl('https://api.gemini.com/v1/pricefeed'), true);
    if (!$price)
        return bxc_gemini_convert_to_fiat($cryptocurrency_code, $amount);
    for ($i = 0; $i < count($price); $i++) {
        if ($price[$i]['pair'] == $symbol_uppercase) {
            $response = ['remaining_amount' => 1];
            $continue = 5;
            while ($continue && bxc_isset($response, 'remaining_amount') != '0' && !bxc_isset($response, 'is_live')) {
                $response = bxc_gemini_curl('order/new', ['symbol' => $symbol, 'amount' => $amount, 'price' => round(floatval($price[$i]['price']) * 0.999, 2), 'side' => 'sell', 'type' => 'exchange limit']);
                $continue--;
            }
            return $response;
        }
    }
    return false;
}

function bxc_coinbase_curl($url_part, $parameters = [], $type = 'POST', $header = []) {
    $body = $parameters ? json_encode($parameters) : '';
    $response_json = bxc_curl('https://api.coinbase.com' . $url_part, $body, array_merge($header, ['Authorization: Bearer ' . bxc_coinbase_get_jwt($url_part, $type), 'Content-Type: application/json']), $type);
    $response = json_decode($response_json, true);
    return $response ? $response : bxc_error($response_json, 'bxc_coinbase_curl', true);
}

function bxc_coinbase_get_jwt($url_part, $type) {
    require_once(__DIR__ . '/vendor/jwt/JWTExceptionWithPayloadInterface.php');
    require_once(__DIR__ . '/vendor/jwt/BeforeValidException.php');
    require_once(__DIR__ . '/vendor/jwt/CachedKeySet.php');
    require_once(__DIR__ . '/vendor/jwt/ExpiredException.php');
    require_once(__DIR__ . '/vendor/jwt/JWK.php');
    require_once(__DIR__ . '/vendor/jwt/JWT.php');
    require_once(__DIR__ . '/vendor/jwt/Key.php');
    require_once(__DIR__ . '/vendor/jwt/SignatureInvalidException.php');
    $uri = $type . ' api.coinbase.com' . $url_part;
    $key_name = trim(bxc_settings_get('coinbase-key'));
    $private_key_resource = openssl_pkey_get_private(trim(str_replace('\n', PHP_EOL, bxc_settings_get('coinbase-key-secret'))));
    if (!$private_key_resource) {
        return bxc_error('Invalid private key', 'bxc_coinbase_curl', true);
    }
    $time = time();
    $nonce = bin2hex(random_bytes(16));
    $payload = [
        'aud' => ['retail_rest_api_proxy'],
        'sub' => $key_name,
        'iss' => 'coinbase-cloud',
        'nbf' => $time,
        'exp' => $time + 120,
        'uri' => $uri,
    ];
    $headers = [
        'typ' => 'JWT',
        'alg' => 'ES256',
        'kid' => $key_name,
        'nonce' => $nonce
    ];
    return JWT::encode($payload, $private_key_resource, 'ES256', $key_name, $headers);
}

function bxc_coinbase_get_accounts($currency_code = false) {
    $accounts = json_decode(bxc_settings_db('coinbase_accounts'), true);
    if (empty($accounts)) {
        $accounts = [];
        $url = '/api/v3/brokerage/accounts';
        while ($url) {
            $accounts_2 = bxc_coinbase_curl($url, [], 'GET');
            $accounts = array_merge($accounts, bxc_isset($accounts_2, 'accounts', []));
            $url = isset($accounts_2['pagination']) ? bxc_isset($accounts_2['pagination'], 'next_uri') : false;
        }
        bxc_settings_db('coinbase_accounts', $accounts);
    }
    if ($currency_code) {
        $currency_code = strtoupper($currency_code);
        for ($i = 0; $i < count($accounts); $i++) {
            if (bxc_isset($accounts[$i], 'currency') == $currency_code && $accounts[$i]['type'] != 'ACCOUNT_TYPE_VAULT') {
                return $accounts[$i];
            }
        }
        return false;
    }
    return $accounts;
}

function bxc_coinbase_get_account_fiat_currency_code() {
    $currency_code = bxc_settings_db('coinbase_fiat_currency_code');
    if ($currency_code) {
        return $currency_code;
    }
    $currencies = array_column(json_decode(file_get_contents(__DIR__ . '/resources/currencies.json'), true), 0);
    $accounts = bxc_coinbase_get_accounts();
    for ($i = 0; $i < count($accounts); $i++) {
        $currency_code = strtoupper($accounts[$i]['currency']['code']);
        if (in_array($currency_code, $currencies)) {
            bxc_settings_db('coinbase_fiat_currency_code', $currency_code);
            break;
        }
    }
    return $currency_code;
}

function bxc_coinbase_send_transaction($amount, $address, $cryptocurrency_code) {
    require_once(__DIR__ . '/vendor/otphp/lib/otphp.php');
    $coinbase_2fa = bxc_settings_get('coinbase-2fa');
    if (!$coinbase_2fa) {
        return bxc_error('Coinbase 2FA key not set.', 'bxc_coinbase_send_transaction', true);
    }
    $account = bxc_coinbase_get_accounts($cryptocurrency_code);
    if (!$account) {
        return bxc_error('Coinbase account not found:' . $cryptocurrency_code, 'bxc_coinbase_send_transaction', true);
    }
    $totp = new \OTPHP\TOTP($coinbase_2fa);
    $response = bxc_coinbase_curl('/v2/accounts/' . $account['uuid'] . '/transactions', ['to' => $address, 'amount' => $amount, 'currency' => $cryptocurrency_code, 'type' => 'send'], 'POST', ['CB-2FA-Token: ' . $totp->now()]);
    if (bxc_isset(bxc_isset($response, 'data', []), 'status') == 'pending') {
        true;
    }
    $error = isset($response['errors']) ? $response['errors'][0]['message'] : json_encode($response);
    bxc_error($error, 'bxc_coinbase_send_transaction');
    return $error;
}

/*
 * -----------------------------------------------------------
 * CLOUD
 * -----------------------------------------------------------
 *
 */

function bxc_cloud_load() {
    if (!defined('BXC_DB_NAME') && defined('CLOUD_URL')) {
        $data = bxc_cloud_get_data();
        if ($data) {
            require_once(__DIR__ . '/cloud/functions.php');
            $data = json_decode(bxc_cloud_encryption($data, false), true);
            $path = __DIR__ . '/cloud/config/' . $data['token'] . '.php';
            if (file_exists($path)) {
                require_once($path);
                return true;
            }
            return 'config-file-missing';
        } else
            return 'cloud-data-not-found';
    }
    return true;
}

function bxc_cloud_spend_credit($transaction_amount, $currency) {
    $fee_usd = floatval($transaction_amount) * (defined('CLOUD_FEE') ? CLOUD_FEE : 0.008);
    if (strtoupper($currency) != 'USD') {
        $rate = bxc_usd_rates($currency);
        $fee_usd = $fee_usd / $rate;
    }
    $credit_balance = floatval(bxc_settings_db('credit_balance')) - $fee_usd;
    bxc_cloud_credit_email($credit_balance);
    return bxc_db_query('UPDATE bxc_settings SET value = ' . bxc_db_escape($credit_balance, true) . ' WHERE name = "credit_balance"');
}

function bxc_cloud_credit_email($credit_balance) {
    if ($credit_balance < 5) {
        $account = bxc_account();
        if ($account) {
            if ($credit_balance > 0 && intval(bxc_settings_db('credit_balance_email')) > (time() - 604800)) {
                return;
            }
            $emails = $credit_balance < 0 ? ['Action required: your account has been suspended.', 'Your account has been suspended because your balance is negative. Your checkouts are blocked and your customers cannot make payments.'] : ['Your balance is low: Add credit to avoid service disruption', 'Your balance is less than USD 5. When the balance drops below zero, the checkouts will stop working and your customers will not be able to make payments.'];
            $response = bxc_email_send($account[0], $emails[0], ['title' => $emails[0], 'message' => $emails[1] . ' Click the button below to add credit to your account. <br /> <br /> <a href="' . BXC_URL . '#account" style="display:block;text-decoration:none;border:none;background:#2acad6;color:#fff;font-weight:500;margin:30px auto;max-width:200px;padding:15px 30px;border-radius:6px;font-size:17px;white-space:nowrap;text-align:center;cursor:pointer">Add credit to your account</a>']);
            if ($response === true && $credit_balance > 0) {
                bxc_settings_db('credit_balance_email', time());
            }
        }
    }
}

function bxc_cloud_get_data() {
    bxc_cloud_domain_rewrite_load(true);
    return isset($_COOKIE['BXC_CLOUD']) ? $_COOKIE['BXC_CLOUD'] : (isset($_GET['cloud']) ? bxc_sanatize_string($_GET['cloud']) : bxc_sanatize_string(bxc_isset($_POST, 'cloud')));
}

function bxc_cloud_url_part($question_mark = true) {
    return BXC_CLOUD && !bxc_settings_get('custom-domain') ? ($question_mark ? '?' : '&') . 'cloud=' . bxc_cloud_get_data() : '';
}

function bxc_cloud_path_part() {
    return defined('BXC_DB_NAME') ? (BXC_CLOUD ? '/' . substr(BXC_DB_NAME, 4) : '') : '';
}

function bxc_cloud_domain_rewrite_load($set_cookie = true) {
    if (isset($_COOKIE['BXC_CLOUD'])) {
        return $_COOKIE['BXC_CLOUD'];
    }
    $referral = bxc_isset($_SERVER, 'HTTP_APX_INCOMING_HOST');
    if ($referral) {
        require_once(__DIR__ . '/cloud/functions.php');
        $domains = json_decode(bxc_isset(db_get('SELECT value FROM settings WHERE name = "custom_domains"'), 'value', '{}'), true);
        $referral = bxc_isset($domains, $_SERVER['HTTP_APX_INCOMING_HOST']);
        if ($referral && $set_cookie) {
            $_COOKIE['BXC_CLOUD'] = $referral;
        }
        return $referral;
    }
}

?>