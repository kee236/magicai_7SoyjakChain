<?php

/*
 * ==========================================================
 * STRIPE.PHP
 * ==========================================================
 *
 * Process Stripe payments
 *
 */

header('Content-Type: application/json');
$raw = file_get_contents('php://input');
$response = json_decode($raw, true);

if ($response && empty($response['error']) && !empty($response['id'])) {
    require('functions.php');
    $metadata = bxc_isset($response['data']['object'], 'metadata');
    if ($metadata && isset($metadata['source']) && $metadata['source'] === 'boxcoin') {
        if (BXC_CLOUD) {
            if (isset($metadata['cloud'])) {
                $_POST['cloud'] = $metadata['cloud'];
                bxc_cloud_load();
            } else {
                die();
            }
        }
        $response = bxc_stripe_curl('events/' . $response['id'], 'GET');
        $data = $response['data']['object'];
        switch ($response['type']) {
            case 'checkout.session.completed':
                $transaction = bxc_transactions_get($data['client_reference_id']);
                if ($transaction) {
                    bxc_transactions_complete($transaction, $data['amount_total'] / bxc_stripe_get_divider($transaction['currency']), $data['customer']);
                    if (BXC_CLOUD) {
                        bxc_cloud_spend_credit($data['amount_total'] / bxc_stripe_get_divider($transaction['currency']), $transaction['currency']);
                    }
                }
                break;
        }
    }
}

?>