/*
 * ==========================================================
 * ADMINISTRATION SCRIPT
 * ==========================================================
 *
 * Main JavaScript admin file. © 2022-2024 boxcoin.dev. All rights reserved.
 *  
 */

'use strict';
(function () {

    var body;
    var main;
    var timeout;
    var areas;
    var active_area;
    var active_area_id;
    var transactions_table;
    var transactions_filters = [false, false, false, false, false];
    var transaction_statuses = { P: 'Pending', C: 'Completed', R: 'Refunded', X: 'Underpayment' };
    var pagination = 0;
    var pagination_count = true;
    var today = new Date();
    var BXC_CHECKOUTS = false;
    var responsive = window.innerWidth < 769;
    var datepicker = false;
    var datepicker_loaded = false;
    var date_picker_settings = {
        format: 'dd/mm/yyyy',
        maxDate: new Date(),
        dateDelimiter: ' - ',
        separator: ' - ',
        clearBtn: true,
    };
    var WP;
    var _ = window._query;
    var refund_timeout;
    var upload_input;
    var upload_target;
    var confirm_on_success;

    /*
    * ----------------------------------------------------------
    * BXCTransactions
    * ----------------------------------------------------------
    */

    var BXCTransactions = {

        get: function (onSuccess, search = false, status = false, cryptocurrency = false, date_range = false, checkout_id = false) {
            ajax('get-transactions', { pagination: pagination, search: search, status: status, cryptocurrency: cryptocurrency, date_range: date_range, checkout_id: checkout_id }, (response) => {
                pagination++;
                onSuccess(response);
            });
        },

        print: function (onSuccess, search = transactions_filters[0], status = transactions_filters[1], cryptocurrency = transactions_filters[2], date_range = transactions_filters[3], checkout_id = transactions_filters[4]) {
            this.get((response) => {
                let code = '';
                pagination_count = response.length;
                for (var i = 0; i < pagination_count; i++) {
                    let transaction = response[i];
                    let from = transaction.from ? `<a href="${BXCAdmin.explorer(transaction.cryptocurrency, transaction.from)}" target="_blank" class="bxc-link">${transaction.from}</a>` : '';
                    let amount = transaction.currency.toUpperCase() + ' ' + transaction.amount_fiat;
                    let vat = '';
                    let responsive_labels = ['', '', '', '', '', ''];
                    let code_shop = '';
                    let amounts = [transaction.amount ? transaction.amount + ' ' + BOXCoin.baseCode(transaction.cryptocurrency.toUpperCase()) : (transaction.amount_fiat ? amount : bxc_('Free')), transaction.amount ? amount : (transaction.amount_fiat ? slugToString(BOXCoin.baseCode(transaction.cryptocurrency)) : '')];
                    if (responsive) {
                        responsive_labels = ['ID', 'Date', 'From', 'To', 'Status', 'Amount'];
                        for (var y = 0; y < responsive_labels.length; y++) {
                            responsive_labels[y] = '<div class="bxc-label">' + bxc_(responsive_labels[y]) + '</div>';
                        }
                    }
                    if (transaction.vat_details) {
                        vat = JSON.parse(transaction.vat_details);
                        vat = ` data-vat="${transaction.currency} ${vat.amount} (${vat.percentage}%, ${vat.country})"`;
                    }
                    if (BXC_ADMIN_SETTINGS.apps.includes('shop')) {
                        code_shop = ` data-license-key="${transaction.license_key ? transaction.license_key : ''}" data-license-key-status="${transaction.license_key_status ? transaction.license_key_status : ''}" data-customer-id="${transaction.customer_id ? transaction.customer_id : ''}"`;
                    }
                    code += `<tr data-id="${transaction.id}"${code_shop} data-checkout-id="${transaction.checkout_id}" data-title="${transaction.title}" data-currency="${transaction.currency}" data-cryptocurrency="${transaction.cryptocurrency}" data-hash="${transaction.hash}" data-notes="${transaction.description.join('<br>')}" data-status="${transaction.status}"${transaction.billing ? ' data-invoice="true"' : ''}${vat} data-discount-code="${transaction.discount_code ? transaction.discount_code : ''}" data-amount="${transaction.amount_fiat ? transaction.amount_fiat : transaction.amount}" data-type="${transaction.type ? transaction.type : 1}"><td class="bxc-td-id">${responsive_labels[0]}${transaction.id}</td><td class="bxc-td-time">${responsive_labels[1]}<div class="bxc-title">${BOXCoin.beautifyTime(transaction.creation_time, true)}</div></td><td class="bxc-td-from">${from ? responsive_labels[2] : ''}${from}</td><td class="bxc-td-to">${transaction.to ? responsive_labels[3] : ''}${transaction.to ? `<a href="${BXCAdmin.explorer(transaction.cryptocurrency, transaction.to)}" target="_blank" class="bxc-link">${transaction.to}</a>` : ''}</td><td class="bxc-td-status">${responsive_labels[4]}<span class="bxc-status-${transaction.status}">${bxc_(transaction_statuses[transaction.status])}</span></td><td class="bxc-td-amount">${responsive_labels[5]}<div class="bxc-title"><div>${amounts[0]}</div><div>${!transaction.cryptocurrency || !amounts[1].includes(transaction.cryptocurrency.toUpperCase()) ? amounts[1] : ''}</div></div></td><td><i class="bxc-transaction-menu-btn bxc-icon-menu"></i></td></tr>`;
                }
                print(transactions_table.find('tbody'), code, true);
                if (onSuccess) {
                    onSuccess(response);
                }
            }, search, status, cryptocurrency, date_range, checkout_id);
        },

        query: function (icon = false) {
            if (loading(transactions_table)) return;
            transactions_table.find('tbody').html('');
            pagination = 0;
            transactions_filters[0] = _(main).find('#bxc-search-transactions').val().toLowerCase().trim();
            transactions_filters[1] = _(main).find('.bxc-filter-status li.bxc-active').data('value');
            transactions_filters[2] = _(main).find('.bxc-filter-cryptocurrency li.bxc-active').data('value');
            transactions_filters[3] = datepicker ? datepicker.getDates('yyyy-mm-dd') : false;
            transactions_filters[4] = _(main).find('.bxc-filter-checkout li.bxc-active').data('value');
            this.print(() => {
                if (icon) {
                    loading(icon, false);
                }
                loading(transactions_table, false);
            });
        },

        download: function (onSuccess) {
            ajax('download-transactions', { search: transactions_filters[0], status: transactions_filters[1], cryptocurrency: transactions_filters[2], date_range: transactions_filters[3], checkout_id: transactions_filters[4] }, (response) => {
                onSuccess(response);
            });
        }
    }

    /*
    * ----------------------------------------------------------
    * BXCCheckout
    * ----------------------------------------------------------
    */

    var BXCCheckout = {

        row: function (checkout) {
            return `<tr data-checkout-id="${checkout.id}"><td><div class="bxc-title"><span>${checkout.id}</span><span>${checkout.title}</span></div></td><td><div class="bxc-text">${checkout.currency ? checkout.currency : BXC_CURRENCY} ${checkout.price}</div></td></tr>`;
        },

        embed: function (id = false) {
            let index = WP ? 3 : 2;
            for (var i = 0; i < index; i++) {
                let elements = active_area.find('#bxc-checkout-' + (i == 0 ? 'payment-link' : (i == 1 ? 'embed-code' : 'shortcode'))).find('div, i');
                if (id) {
                    let content = '';
                    if (i == 0) {
                        content = `${BXC_CLOUD.custom_domain ? BXC_CLOUD.custom_domain : BXC_URL}${BXC_CLOUD.cloud ? '/checkout/' : (BXC_ADMIN_SETTINGS.url_rewrite_checkout ? BXC_ADMIN_SETTINGS.url_rewrite_checkout : 'pay.php?checkout_id=')}${id}`;
                        content += BXC_CLOUD.custom_domain ? '' : cloudURL(content);
                    } else if (i == 1) {
                        content = `<div data-bxc="${id}"></div>${BXC_CLOUD.cloud ? ' <script id="bxc-cloud" src="' + BXC_URL + 'js/client.js?cloud=' + BXC_CLOUD.cloud + '"></script>' : ''}`;
                    } else {
                        content = `[boxcoin id="${id}"]`;
                    }
                    content = content.replace('//checkout', '/checkout');
                    _(elements.e[0]).html(i == 0 ? `<a href="${content}" target="_blank">${content}</a>` : content.replace(/</g, '&lt;'));
                    _(elements.e[1]).data('text', window.btoa(content));
                } else {
                    _(elements.e[0]).html('');
                    _(elements.e[1]).data('text', '');
                }
            }
        },

        get: function (id, remove = false) {
            for (var i = 0; i < BXC_CHECKOUTS.length; i++) {
                if (id == BXC_CHECKOUTS[i].id) {
                    if (remove) {
                        BXC_CHECKOUTS.splice(i, 1);
                        return true;
                    }
                    return BXC_CHECKOUTS[i];
                }
            }
            return false;
        }
    }

    /*
    * ----------------------------------------------------------
    * BXCAdmin
    * ----------------------------------------------------------
    */

    var BXCAdmin = {
        active_element: false,

        card: function (message, type = false) {
            var card = main.find('.bxc-info-card');
            card.removeClass('bxc-info-card-error bxc-info-card-warning bxc-info-card-info');
            if (!type) {
                clearTimeout(timeout);
            } else if (type == 'error') {
                card.addClass('bxc-info-card-error');
            } else {
                card.addClass('bxc-info-card-info');
            }
            card.html(bxc_(message));
            timeout = setTimeout(() => { card.html('') }, 5000);
        },

        error: function (message, loading_area = false) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            main.find('.bxc-info').html(message);
            if (loading_area) {
                loading(loading_area, false);
            }
        },

        confirm: function (message, onSuccess, loading_element, ignore = false) {
            if (ignore) {
                return onSuccess();
            }
            BOXCoin.lightbox(bxc_('Are you sure?'), '<div class="bxc-text">' + bxc_(message) + '</div>', [['bxc-confirm-btn', 'Confirm'], ['bxc-cancel-btn', 'Cancel']], 'confirm');
            confirm_on_success = [onSuccess, loading_element];
        },

        balance: function (area) {
            if (!loading(area)) {
                let table = main.find('#bxc-table-balances tbody');
                if (!table.e.length) return;
                if (!table.html()) area.addClass('bxc-loading-first');
                ajax('get-balances', {}, (response) => {
                    let code = '';
                    let balances = response.balances;
                    main.find('#bxc-balance-total').html(`${BXC_CURRENCY} ${response.total}`);
                    for (var key in balances) {
                        let img = key in response.token_images ? response.token_images[key] : `${BXC_URL}media/icon-${key}.svg`;
                        code += `<tr data-cryptocurrency="${key}"><td><div class="bxc-flex"><img src="${img}" /> ${balances[key].name}${BOXCoin.network(key, true, true)}</div></td><td><div class="bxc-balance bxc-title">${balances[key].amount} ${BOXCoin.baseCode(key).toUpperCase()}</div><div class="bxc-text">${BXC_CURRENCY} ${balances[key].fiat}</div></td></tr>`;
                    }
                    code ? table.html(code) : table.parent().html('<p class="bxc-text">Add your addresses from the Settings area.</p>');
                    area.removeClass('bxc-loading-first');
                    loading(area, false);
                });
            }
        },

        explorer: function (cryptocurrency, value, type = 'address') {
            let explorer = '';
            let replace = type == 'address' ? 'address' : (['ltc', 'usdt_tron'].includes(cryptocurrency) ? 'transaction' : 'tx');
            if (value.indexOf('cus_') === 0) {
                cryptocurrency = 'stripe';
                replace = value;
            }
            switch (cryptocurrency) {
                case 'btc':
                    explorer = BXC_ADMIN_SETTINGS.testnet_btc ? 'https://mempool.space/testnet/{R}/{V}' : 'https://www.blockchain.com/btc/{R}/{V}';
                    break;
                case 'eth':
                    explorer = 'https://www.blockchain.com/eth/{R}/{V}';
                    break;
                case 'doge':
                    explorer = 'https://dogechain.info/{R}/{V}';
                    break;
                case 'link':
                case 'bat':
                case 'shib':
                case 'usdc':
                case 'usdt':
                    explorer = 'https://etherscan.io/{R}/{V}' + (type == 'address' ? '' : '#tokentxns');
                    break;
                case 'usdt_tron':
                    explorer = 'https://tronscan.org/#/{R}/{V}';
                    break;
                    break;
                case 'algo':
                    explorer = 'https://algoexplorer.io/{R}/{V}';
                    break;
                case 'stripe':
                    explorer = 'https://dashboard.stripe.com/' + (BXC_ADMIN_SETTINGS.stripe_test_mode ? 'test/' : '') + 'customers/{R}'
                    break;
                case 'verifone':
                    explorer = 'https://secure.2checkout.com/cpanel/order_info.php?refno={V}';
                    break;
                case 'usdt_bsc':
                case 'busd':
                case 'bnb':
                    explorer = 'https://bscscan.com/{R}/{V}';
                    break;
                case 'ltc':
                    explorer = 'https://blockchair.com/litecoin/{R}/{V}'
                    break;
                case 'bch':
                    explorer = 'https://www.blockchain.com/bch/{R}/{V}'
                    break;
                case 'xrp':
                    replace = type == 'address' ? 'accounts' : 'transactions';
                    explorer = 'https://livenet.xrpl.org/{R}/{V}'
                    break;
                case 'sol':
                    replace = type == 'address' ? 'account' : 'tx';
                    explorer = 'https://solscan.io/{R}/{V}';
                    break;
                case 'xmr':
                    if (type == 'address') {
                        return false;
                    }
                    explorer = 'https://blockchair.com/monero/transaction/{V}';
                    break;
                default:
                    let network = BOXCoin.network(cryptocurrency, 'code');
                    if (network == 'eth') {
                        explorer = 'https://etherscan.io/{R}/{V}' + (type == 'address' ? '' : '#tokentxns');
                    }
                    if (network == 'bsc') {
                        explorer = 'https://bscscan.com/{R}/{V}';
                    }
            }
            return explorer.replace('{R}', replace).replace('{V}', value);
        },

        datepicker: function (input) {
            if (datepicker) {
                datepicker.destroy();
            }
            datepicker = new DateRangePicker(_(input).parent().e[0], date_picker_settings);
            datepicker.datepickers[0].show();
        },

        repeater: {
            add: function (button, print = true, index = false) {
                let parent = _(button).parent();
                let items = parent.find('[data-type], hr');
                let code = '<div class="bxc-repater-line"><hr /><i class="bxc-icon-close"></i></div>';
                index = index ? index : parseInt(_(button).data('index'));
                for (var i = 0; i < items.e.length; i++) {
                    if (items.e[i].nodeName == 'HR') {
                        break;
                    } else {
                        let id = _(items.e[i]).attr('id');
                        code += items.e[i].outerHTML.replace(id, id + '-' + index);
                    }
                }
                if (print) {
                    code += button.outerHTML.replace('"' + index + '"', '"' + (index + 1) + '"');
                } else {
                    return code;
                }
                button.remove();
                parent.append(code);
            },

            open: function (settings, area) {
                area.find('.bxc-btn-repater').e.forEach(e => {
                    let index = 2;
                    let code = '';
                    let id = _(e.parentElement).find('[id]').attr('id');
                    while (id + '-' + index in settings) {
                        code += this.add(e, false, index);
                        index++;
                    }
                    if (code) {
                        code += e.outerHTML.replace('"2"', '"' + index + '"');
                        _(e).parent().append(code);
                        e.remove();
                    }
                });
            },

            delete: function (i) {
                let is_first = _(i).hasClass('bxc-first-repeater-item');
                let parent = _(i).parent();
                let items = Array.from(parent.parent().e[0].childNodes);
                let index = items.indexOf(parent.e[0]) + 1;
                let button = parent.parent().find('.bxc-btn-repater');
                let button_index = parseInt(button.data('index')) - 1;
                let is_reset = false;
                for (var i = index; i < items.length; i++) {
                    let item = _(items[i]);
                    if (is_reset || item.hasClass('bxc-repater-line') || item.hasClass('bxc-btn-repater')) {
                        is_reset = true;
                        let item_id = item.attr('id');
                        let item_index = parseInt(item_id && item_id.includes('-') ? item_id.substr(item_id.lastIndexOf('-') + 1) : false);
                        if (item_index) {
                            item.attr('id', item_id.replace('-' + item_index, '-' + (item_index - 1)));
                        }
                    } else if (is_first) {
                        inputValue(inputGet(item), '');
                    } else {
                        item.remove();
                    }
                }
                button.data('index', button_index < 2 ? 2 : button_index);
                if (!is_first) {
                    parent.remove();
                }
            }
        }
    }

    window.BXCTransactions = BXCTransactions;
    window.BXCCheckout = BXCCheckout;
    window.BXCAdmin = BXCAdmin;

    /*
    * ----------------------------------------------------------
    * Functions
    * ----------------------------------------------------------
    */

    function loading(element, action = -1) {
        return BOXCoin.loading(element, action);
    }

    function ajax(function_name, data = {}, onSuccess = false) {
        return BOXCoin.ajax(function_name, data, onSuccess);
    }

    function activate(element, activate = true) {
        return BOXCoin.activate(element, activate);
    }

    function card(message, type = false) {
        BXCAdmin.card(message, type);
    }

    function bxc_(text) {
        return BXC_TRANSLATIONS && text in BXC_TRANSLATIONS ? BXC_TRANSLATIONS[text] : text;
    }

    function showError(message, loading_area = false) {
        BXCAdmin.error(message, loading_area);
    }

    function scrollBottom() {
        window.scrollTo(0, document.body.scrollHeight - 800);
    }

    function inputValue(input, value = -1) {
        if (!input || !_(input).e.length) {
            return '';
        }
        input = _(input).e[0];
        if (value === -1) {
            return _(input).is('checkbox') ? input.checked : input.value.trim();
        }
        if (_(input).is('checkbox')) {
            input.checked = value == 0 ? false : value;
        } else {
            input.value = value;
        }
        if (_(input).is('textarea')) {
            resizeOnInput(input);
        }
    }

    function inputGet(parent) {
        return _(parent).find('input, select, textarea');
    }

    function openURL() {
        let url = window.location.href;
        if (url.includes('#')) {
            let anchor = url.substr(url.indexOf('#'));
            if (anchor.includes('?')) anchor = anchor.substr(0, anchor.indexOf('?'));
            if (anchor.length > 1) {
                let item = main.find('.bxc-nav ' + anchor);
                if (item.e.length) {
                    nav(item);
                    return true;
                }
            }
        }
        return false;
    }

    function nav(nav_item) {
        if (!_(nav_item).e.length) return;
        let items = main.find('main > div');
        let index = nav_item.index();
        active_area = _(items.e[index]);
        active_area_id = nav_item.attr('id');
        main.removeClass('bxc-area-transactions bxc-area-checkouts bxc-area-balances bxc-area-settings').addClass('bxc-area-' + active_area_id);
        activate(items, false);
        activate(nav_item.siblings(), false);
        activate(nav_item);
        activate(active_area);
        window.scrollTo(0, 0);
        if (!window.location.href.includes(active_area_id)) {
            window.history.pushState('', '', '#' + active_area_id);
        }
        switch (active_area_id) {
            case 'transactions':
                if (!loading(active_area)) {
                    if (datepicker) {
                        datepicker.destroy();
                        datepicker = false;
                    }
                    loading(active_area);
                    pagination = 0;
                    transactions_table.find('tbody').html('');
                    BXCTransactions.print(() => {
                        loading(items.e[index], false);
                        setTimeout(() => { window.scrollTo(0, 0) }, 100);
                    });
                }
                break;
            case 'checkouts':
                if (loading(active_area, 'check')) {
                    ajax('get-checkouts', {}, (response) => {
                        let code = '';
                        BXC_CHECKOUTS = response;
                        for (var i = 0; i < response.length; i++) {
                            code += BXCCheckout.row(response[i]);
                        }
                        print(main.find('#bxc-table-checkouts tbody'), code);
                        loading(items.e[index], false);
                    });
                }
                break;
            case 'balances':
                BXCAdmin.balance(active_area);
                break;
            case 'settings':
                if (loading(active_area, 'check')) {
                    ajax('get-settings', {}, (response) => {
                        if (response) {
                            if (BXC_ADMIN_SETTINGS.apps.includes('exchange')) {
                                let name = 'custom-token-code';
                                let code_2 = '';
                                let i = 0;
                                while (response[name + (i ? '-' + (i + 1) : '')]) {
                                    code_2 += `<option value="${response[name + (i ? '-' + (i + 1) : '')]}">${response['custom-token-name' + (i ? '-' + (i + 1) : '')]}</option>`;
                                    i++;
                                }
                                if (code_2) {
                                    active_area.find('#exchange-default-get select').append(code_2);
                                }
                            }
                            BXCAdmin.repeater.open(response, active_area);
                            for (var key in response) {
                                let item = main.find('#' + key);
                                if (item) {
                                    inputValue(inputGet(item), response[key]);
                                }
                            }
                            active_area.find('#btc-wallet-key input, #eth-wallet-key input, #ln-macaroon input').e.forEach(e => {
                                if (e.value) {
                                    e.value = '********';
                                }
                            });
                            BOXCoin.event('SettingsLoaded', response);
                        }
                        loading(items.e[index], false);
                    });
                }
                break;
            case 'analytics':
                if (datepicker) {
                    datepicker.destroy();
                    datepicker = false;
                }
                BXCAdminShop.analytics(datepicker);
                break;
        }
    }

    function slugToString(string) {
        string = string.replace(/_/g, ' ').replace(/-/g, ' ');
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    function stringToSlug(string) {
        string = string.trim();
        string = string.toLowerCase();
        var from = "åàáãäâèéëêìíïîòóöôùúüûñç·/_,:;";
        var to = "aaaaaaeeeeiiiioooouuuunc------";
        for (var i = 0, l = from.length; i < l; i++) {
            string = string.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
        }
        return string.replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').replace(/^-+/, '').replace(/-+$/, '').replace(/ /g, '');
    }

    function print(area, code, append = false) {
        if (!code) return area.html() ? false : area.html(`<p class="bxc-not-found">${bxc_('There\'s nothing here yet.')}</p>`);
        area.find('.bxc-not-found').remove();
        area.html((append ? area.html() : '') + code);
    }

    function resizeOnInputListener() {
        resizeOnInput(this);
    }

    function resizeOnInput(e) {
        e.style.height = 'auto';
        e.style.height = (e.scrollHeight) + 'px';
    }

    function cloudURL(url = '') {
        return BXC_CLOUD && BXC_CLOUD.cloud && !BXC_CLOUD.custom_domain ? (url.includes('?') ? '&' : '?') + 'cloud=' + BXC_CLOUD.cloud : '';
    }

    document.addEventListener('DOMContentLoaded', () => {

        /*
        * ----------------------------------------------------------
        * Init
        * ----------------------------------------------------------
        */

        body = _(document.body);
        main = body.find('.bxc-main');
        if (!main.e.length) {
            return;
        }
        areas = main.find('main > div');
        active_area = areas.e[0];
        transactions_table = main.find('#bxc-table-transactions');
        upload_input = body.find('#bxc-upload-form input');
        WP = typeof BXC_WP != 'undefined';
        if (BOXCoin.cookie('BXC_LOGIN') && !main.hasClass('bxc-installation')) {
            active_area_id = 'transactions';
            if (!openURL()) {
                BXCTransactions.print(() => { loading(active_area, false) });
            }
            if (localStorage.getItem('bxc-cron') != today.getDate()) {
                ajax('cron', { domain: BXC_URL });
                localStorage.setItem('bxc-cron', today.getDate());
            }
            let textareas = document.getElementsByTagName('textarea');
            for (let i = 0; i < textareas.length; i++) {
                textareas[i].setAttribute('style', 'height:' + (textareas[i].scrollHeight) + 'px;overflow-y:hidden;');
                textareas[i].addEventListener('input', resizeOnInputListener, false);
            }
            BXCAdmin.balance(main.find('main > [data-area="balance"]'));
        }

        /*
        * ----------------------------------------------------------
        * Transactions
        * ----------------------------------------------------------
        */

        transactions_table.on('click', 'tr', function (e) {
            if (['A', 'I', 'LI'].includes(e.target.nodeName)) return;
            let hash = _(this).data('hash');
            if (hash) {
                let cryptocurrency = _(this).data('cryptocurrency');
                let url = BXCAdmin.explorer(cryptocurrency, hash, 'tx');
                if (url) {
                    window.open(url);
                }
            }
        });

        main.on('click', '#bxc-filters', function () {
            active_area.find('.bxc-nav-filters').toggleClass('bxc-active');
        });

        main.on('click', '.bxc-filter-date', function () {
            if (!datepicker_loaded) {
                _.load(BXC_URL + 'vendor/datepicker/datepicker.min.css', false);
                _.load(BXC_URL + 'vendor/datepicker/datepicker.min.js', true, () => {
                    if (BXC_LANG) {
                        date_picker_settings.language = BXC_LANG;
                        _.load(`${BXC_URL}vendor/datepicker/locales/${BXC_LANG}.js`, true, () => {
                            datepicker_loaded = true;
                            BXCAdmin.datepicker(this);
                        });
                    } else {
                        datepicker_loaded = true;
                        BXCAdmin.datepicker(this);
                    }
                });
            } else if (!datepicker) {
                BXCAdmin.datepicker(this);
            }
        });

        main.on('click', '.bxc-filter-status li, .bxc-filter-cryptocurrency li, .bxc-filter-checkout li, .datepicker-cell, .datepicker .clear-btn', function () {
            setTimeout(() => {
                if (active_area_id == 'transactions') {
                    BXCTransactions.query();
                } else {
                    BXCAdminShop.analytics(datepicker);
                }
            }, 100);
        });

        main.on('click', '#bxc-download-transitions', function () {
            if (loading(this)) return;
            BXCTransactions.download((response) => {
                window.open(response);
                loading(this, false);
            });
        });

        transactions_table.on('click', '.bxc-transaction-menu-btn:not(.bxc-loading)', function () {
            let active = _(this).hasClass('bxc-active');
            let row = _(this.closest('tr'));
            let code = `<ul class="bxc-transaction-menu bxc-ul"><li data-value="details">${bxc_('Details')}</li>`;
            let status = row.data('status');
            let cryptocurrency_code = row.data('cryptocurrency');
            activate(transactions_table.find('.bxc-transaction-menu-btn'), false);
            transactions_table.find('.bxc-transaction-menu').remove();
            if (!active) {
                activate(this);
                if (status == 'P' && row.data('amount')) {
                    code += `<li data-value="payment-link">${bxc_('Payment link')}</li>`;
                } else if (BXC_ADMIN_SETTINGS.invoice) {
                    code += `<li data-value="invoice">${bxc_('Invoice')}</li>`;
                }
                if (['C', 'X'].includes(status) && ((BXC_REFUNDS.includes('coinbase') && ['btc', 'eth', 'xrp', 'usdt', 'usdc', 'busd', 'bnb', 'link', 'doge', 'shib', 'ltc', 'algo', 'bat', 'bch', 'sol'].includes(cryptocurrency_code)) || (BXC_REFUNDS.includes('btc') && cryptocurrency_code == 'btc') || (BXC_REFUNDS.includes('eth') && ['eth', 'usdt', 'usdc', 'link', 'shib', 'bat'].includes(cryptocurrency_code)))) {
                    code += `<li data-value="refund">${bxc_('Issue a refund')}</li>`;
                }
                for (var key in transaction_statuses) {
                    if (key != status) {
                        code += `<li data-value="${key}">${bxc_('Mark as ' + transaction_statuses[key].toLowerCase())}</li>`;
                    }
                }
                _(this).parent().append(code + `<li data-value="delete">${bxc_('Delete')}</li></ul>`);
            }
        });

        transactions_table.on('click', '.bxc-transaction-menu li', function () {
            let row = _(this.closest('tr'));
            let menu = row.find('.bxc-transaction-menu-btn');
            if (loading(menu)) return;
            let value = _(this).data('value');
            let id = row.data('id');
            if (['C', 'P', 'R'].includes(value)) {
                let message;
                if (value == 'C') {
                    switch (row.data('type')) {
                        case '2':
                        case '1':
                            message = 'The transaction will be processed, and any user purchase associated with it will be processed and finalized.';
                            break;
                        case '3':
                            message = 'The exchange will be processed and the amount to be exchanged will be automatically sent to the user.';
                            break;
                    }
                }
                BXCAdmin.confirm(message, () => {
                    ajax('update-transaction', { transaction_id: id, values: { status: value } }, (response) => {
                        if (response === true) {
                            row.data('status', value).find('.bxc-td-status span').attr('class', 'bxc-status-' + value).html(bxc_(transaction_statuses[value]));
                        } else {
                            card(response, 'error');
                        }
                        loading(menu, false);
                    });
                }, menu, !message);
            }
            if (value == 'invoice') {
                ajax('encryption', { string: id }, (response) => {
                    window.open((BXC_CLOUD.custom_domain ? BXC_CLOUD.custom_domain : BXC_URL) + (BXC_ADMIN_SETTINGS.url_rewrite_invoice ? BXC_ADMIN_SETTINGS.url_rewrite_invoice : 'pay.php?invoice=') + response + cloudURL());
                    loading(menu, false);
                });
            }
            if (value == 'payment-link') {
                ajax(value, { transaction_id: id }, (response) => {
                    window.open(response + cloudURL(response));
                    loading(menu, false);
                });
            }
            if (value == 'details') {
                let code = '<div class="bxc-text-list bxc-transaction-details-list">';
                let details = [['ID', 'id'], ['Checkout', 'data-title'], ['Hash', 'data-hash'], ['Time', 'time'], ['Notes', 'data-notes'], ['Status', 'status'], ['From', 'from'], ['To', 'to'], ['Amount', 'amount'], ['VAT', 'data-vat'], ['License key', 'data-license-key']];
                for (var i = 0; i < details.length; i++) {
                    let slug = details[i][1];
                    let value = slug.includes('data') ? row.attr(slug) : row.find('.bxc-td-' + slug).html();
                    if (value) {
                        value = value.trim();
                        if (slug == 'data-hash') {
                            value = `<a href="${BXCAdmin.explorer(row.data('cryptocurrency'), value, 'tx')}" target="_blank">${value}</a>`;
                        } else if (slug == 'data-title') {
                            value = value + ' - ID ' + row.data('checkout-id');
                        } else if (slug == 'data-notes') {
                            value = value.replaceAll('\\r\\n', '\n').replaceAll('\\r', '\n').replaceAll('\\n', '<br>').replaceAll('\n', '<br>');
                        } else if (slug == 'data-license-key') {
                            let status = row.attr('data-license-key-status').trim();
                            if (status != 1) {
                                value = '<div class="bxc-label-code">' + value + '</div><div class="bxc-label">' + bxc_('Disabled') + '</div>';
                            }
                            value += `<div id="bxc-btn-license-key-status" class="bxc-btn-text bxc-underline" data-status="${status}" data-id="${row.data('id')}">${bxc_(status == 1 ? 'Disable' : 'Enable')}</div>`;
                        }
                        code += `<div data-name="${slug}"><div>${bxc_(details[i][0])}</div><div>${value}</div></div>`;
                    }
                }
                if (BXC_ADMIN_SETTINGS.apps.includes('shop')) {
                    if (row.data('discount-code')) {
                        code += `<div><div>${bxc_('Discount code')}</div><div>${row.data('discount-code')}</div></div>`;
                    }
                    if (row.data('customer-id')) {
                        code += `<div data-name="customer"><div>${bxc_('Customer details')}</div><div class="bxc-loading"></div></div>`;
                        ajax('get-customer', { customer_id: row.data('customer-id') }, (response) => {
                            let name = response.first_name + (response.last_name ? ' ' + response.last_name : '');
                            body.find('#bxc-lightbox [data-name="customer"] .bxc-loading').html((name ? name + ' - ' : '') + 'ID ' + response.id + (response.email ? '<br>' + response.email : '') + (response.phone ? '<br>' + response.phone : '') + (response.country ? '<br>' + response.country + ' ' + response.country_code : '')).removeClass('bxc-loading');
                        });
                    }
                }
                BOXCoin.lightbox('Transaction details', code);
                loading(menu, false);
            }
            if (value == 'refund') {
                card(bxc_('Sending refund in 5 seconds.') + ' <span id="cancel-refund">' + bxc_('Cancel') + '</span>', 'info');
                refund_timeout = setTimeout(() => {
                    ajax(value, { transaction_id: id }, (response) => {
                        if (response.status === true) {
                            row.data('status', value).find('.bxc-td-status span').attr('class', 'bxc-status-R').html(bxc_(transaction_statuses['R']));
                            card(response.message);
                            let link = main.find('.bxc-info-card a');
                            link.attr('href', BXCAdmin.explorer(row.data('cryptocurrency'), link.data('hash'), 'tx'));
                        } else {
                            card(response.message, 'error');
                        }
                        loading(menu, false);
                    });
                }, 5000);
            }
            if (value == 'delete') {
                BXCAdmin.confirm('The conversation will be deleted permanently.', () => {
                    ajax('delete-transaction', { transaction_id: id }, (response) => {
                        if (response === true) {
                            row.remove();
                        } else {
                            card(response, 'error');
                        }
                        loading(menu, false);
                    });
                }, menu);
            }
            row.find('.bxc-ul').remove();
            activate(menu, false);
        });

        main.on('click', '#cancel-refund', function () {
            clearTimeout(refund_timeout);
            loading(transactions_table.find('.bxc-loading'), false);
        });

        main.on('click', '#bxc-request-payment', function () {
            let code = '';
            let fields = [['price', 'Price', 'number'], ['currency', 'Currency code', 'text'], ['pay', 'Cryptocurrency code', 'text'], ['redirect', 'Redirect URL', 'url'], ['note', 'Notes', 'text']];
            for (var i = 0; i < fields.length; i++) {
                code += `<div class="bxc-input"><span>${bxc_(fields[i][1])}</span><input data-url-attribute="${bxc_(fields[i][0])}" type="${bxc_(fields[i][2])}"></div>`;
            }
            BOXCoin.lightbox('Create a payment request', code + `<div id="bxc-create-payment-link" class="bxc-btn">${bxc_('Create payment link')}</div>`);
        });

        body.on('click', '#bxc-create-payment-link', function () {
            let inputs = _(this).parent().find('input').e;
            let url = '';
            let cloud_url = cloudURL();
            for (var i = 0; i < inputs.length; i++) {
                let slug = _(inputs[i]).data('url-attribute');
                let value = _(inputs[i]).val();
                if (value) {
                    if (slug == 'redirect' || slug == 'note') {
                        value = encodeURIComponent(value);
                    }
                    url += '&' + slug + '=' + value;
                }
            }
            if (url) {
                url = `${BXC_CLOUD.custom_domain ? BXC_CLOUD.custom_domain : BXC_URL}${BXC_ADMIN_SETTINGS.url_rewrite_checkout ? BXC_ADMIN_SETTINGS.url_rewrite_checkout : 'pay.php?checkout_id='}custom-${Math.floor(Date.now() / 1000)}${cloud_url}${url && BXC_ADMIN_SETTINGS.url_rewrite_checkout && !cloud_url ? '?' + url.substring(1) : url}`;
                _(this).parent().find('#bxc-payment-request-url-box').remove();
                _(this).insert(`<div id="bxc-payment-request-url-box" class="bxc-input"><a href="${url}" target="_blank">${url.replace(/&/g, '&amp')}</a><i class="bxc-icon-copy bxc-clipboard" data-text="${window.btoa(url)}"></i></div>`);
            }
        });

        body.on('click', '#bxc-btn-license-key-status', function () {
            let status = _(this).data('status') == 1 ? 2 : 1;
            let id = _(this).data('id');
            ajax('update-license-key-status', { transaction_id: id, status: status }, () => {
                _(this).html(bxc_(status == 1 ? 'Disable' : 'Enable')).data('status', status);
                transactions_table.find(`[data-id="${id}"]`).data('license-key-status', status);
                _(this).parent().find('.bxc-label').remove();
                if (status != 1) {
                    _(this).insert('<div class="bxc-label">' + bxc_('Disabled') + '</div>');
                }
            });
        });

        /*
        * ----------------------------------------------------------
        * Checkouts
        * ----------------------------------------------------------
        */

        main.on('click', '#bxc-create-checkout, #bxc-table-checkouts td', function () {
            let checkout = false;
            main.addClass('bxc-area-create-checkout');
            if (_(this).is('td')) {
                let id = _(this).parent().data('checkout-id');
                checkout = BXCCheckout.get(id);
                active_area.data('checkout-id', id);
                for (var key in checkout) {
                    let input = inputGet(active_area.find(`#bxc-checkout-${key}`));
                    let value = checkout[key];
                    inputValue(input, value);
                }
                BXCCheckout.embed(checkout.slug ? checkout.slug : id);
            } else {
                inputGet(active_area).e.forEach(e => {
                    inputValue(e, '');
                });
                active_area.find('#bxc-checkout-type select').val('I');
                active_area.data('checkout-id', '');
                BXCCheckout.embed();
            }
            if (BXC_ADMIN_SETTINGS.apps.includes('shop')) {
                BXCAdminShop.checkoutOpen(checkout, active_area);
            }
        });

        main.on('click', '#bxc-checkouts-list', function () {
            main.removeClass('bxc-area-create-checkout');
            active_area.data('checkout-id', '');
        });

        main.on('click', '#bxc-save-checkout', function () {
            if (loading(this)) return;
            let error = false;
            let checkout = {};
            let inputs = active_area.find('.bxc-input');
            let checkout_id = active_area.data('checkout-id');
            main.find('.bxc-info').html('');
            inputs.removeClass('bxc-error');

            // Get values
            inputs.e.forEach(e => {
                let id = _(e).attr('id');
                let input = _(e).find('input, select');
                let value = inputValue(input);
                if (!value && input.e.length && input.e[0].hasAttribute('required')) {
                    error = true;
                    _(e).addClass('bxc-error');
                }
                checkout[id.replace('bxc-checkout-', '')] = value;
            });
            if (error) {
                showError('Fields in red are required.', this);
                return;
            }
            if (checkout_id) {
                checkout.id = checkout_id;
            }
            if (BXC_ADMIN_SETTINGS.apps.includes('shop')) {
                checkout = BXCAdminShop.checkoutSave(checkout, active_area);
            }

            // Slug
            if (BXC_ADMIN_SETTINGS.url_rewrite_checkout && !checkout.slug && checkout.title) {
                let slug = stringToSlug(checkout.title);
                checkout.slug = slug;
            }
            for (var i = 0; i < BXC_CHECKOUTS.length; i++) {
                if (BXC_CHECKOUTS[i].slug == checkout.slug && (!checkout.id || checkout.id != BXC_CHECKOUTS[i].id)) {
                    checkout.slug = '';
                }
            }
            active_area.find('#bxc-checkout-slug input').val(checkout.slug);

            // Save changes
            ajax('save-checkout', { checkout: JSON.stringify(checkout) }, (response) => {
                loading(this, false);
                if (Number.isInteger(response)) {
                    checkout.id = response;
                    active_area.data('checkout-id', response);
                    active_area.find('#bxc-table-checkouts tbody').append(BXCCheckout.row(checkout));
                    BXCCheckout.embed(checkout.slug ? checkout.slug : response);
                    BXC_CHECKOUTS.push(checkout);
                    card('Checkout saved successfully');
                } else if (response === true) {
                    BXCCheckout.get(checkout_id, true);
                    BXC_CHECKOUTS.push(checkout);
                    active_area.find(`tr[data-checkout-id="${checkout_id}"]`).replace(BXCCheckout.row(checkout));
                    BXCCheckout.embed(checkout.slug ? checkout.slug : response);
                    card('Checkout saved successfully');
                } else {
                    showError(response, this.closest('form'));
                }
            });
        });

        main.on('click', '#bxc-delete-checkout', function () {
            if (loading(this)) return;
            let id = active_area.data('checkout-id');
            ajax('delete-checkout', { checkout_id: id }, () => {
                loading(this, false);
                active_area.data('checkout-id', '');
                active_area.find(`tr[data-checkout-id="${id}"]`).remove();
                active_area.find('#bxc-checkouts-list').e[0].click();
                card('Checkout deleted', 'error');
            });
        });

        main.on('click', '#checkout-downloads .bxc-repater-line i', function () {
            let file_name = _(this).parent().next().find('input').val();
            if (file_name) {
                ajax('delete-file', { file_name: file_name, folder: 'checkout/' });
            }
        });

        main.on('click', '#checkout-downloads input', function () {
            if (_(this).val()) {
                window.open(BXC_CLOUD.aws ? BXC_CLOUD.aws + _(this).val() : BXC_URL + 'uploads' + (BXC_CLOUD ? BXC_CLOUD.path_part : '') + '/checkout/' + _(this).val());
            }
        });

        /*
        * ----------------------------------------------------------
        * Settings
        * ----------------------------------------------------------
        */

        main.on('click', '#bxc-save-settings', function () {
            if (loading(this)) return;
            let settings = {};
            main.find('[data-area="settings"]').find('.bxc-input[id]:not([data-type="multi-input"]),[data-type="multi-input"] [id]').e.forEach(e => {
                settings[_(e).attr('id')] = inputValue(inputGet(e));
            });
            ajax('save-settings', { settings: JSON.stringify(settings) }, (response) => {
                card(response === true ? 'Settings saved' : response, response === true ? false : 'error');
                loading(this, false);
            });
        });

        main.on('click', '#update-btn a', function (e) {
            if (loading(this)) return;
            ajax('update', { domain: BXC_URL }, (response) => {
                loading(this, false);
                let errors = false;
                let latest_all = true;
                if (typeof response === 'string') {
                    card(slugToString(response), 'error');
                } else {
                    for (var key in response) {
                        if (response[key] !== true && response[key] !== '' && response[key] !== 'latest-version-installed') {
                            errors = true;
                        } else if (response[key] === true) {
                            latest_all = false;
                        }
                    }
                    if (latest_all) {
                        card('You have the latest version!');
                    } else if (!errors) {
                        card('Update completed. Reload in progress...');
                        setTimeout(() => { location.reload(); }, 500);
                    } else {
                        card(slugToString(JSON.stringify(response)), 'error');
                    }
                }
            });
            e.preventDefault();
            return false;
        });

        main.on('click', '#email-test-btn a', function (e) {
            if (loading(this)) return;
            ajax('email-test', {}, (response) => {
                card(response === true ? 'Email successfully sent.' : response);
                loading(this, false);
            });
            e.preventDefault();
            return false;
        });

        main.on('click', '.bxc-btn-repater', function () {
            BXCAdmin.repeater.add(this);
        });

        main.on('click', '.bxc-repater-line i', function () {
            BXCAdmin.repeater.delete(this);
        });

        main.on('click', '#two-fa-pairing a', function (e) {
            ajax('2fa', {}, (response) => {
                window.open(response);
            });
            e.preventDefault();
            return false;
        });

        main.on('click', '#two-fa-validation a', function (e) {
            let input = main.find('#two-fa-code input');
            let code = input.val().trim();
            if (code) {
                ajax('2fa', { code: code }, (response) => {
                    card(response === true ? '2FA activated!' : 'Invalid code.', response === true ? 'info' : 'error');
                    input.val('');
                });
            }
            e.preventDefault();
            return false;
        });

        /*
        * ----------------------------------------------------------
        * Responsive
        * ----------------------------------------------------------
        */

        if (responsive) {
            main.on('click', '.bxc-icon-menu', function () {
                let area = _(this).parent();
                activate(area, !area.hasClass('bxc-active'));
            });
        }

        /*
        * ----------------------------------------------------------
        * Miscellaneous
        * ----------------------------------------------------------
        */

        main.on('click', '#bxc-submit-installation', function () {
            if (loading(this)) return;
            let error = false;
            let installation_data = {};
            let url = window.location.href.replace('/admin', '').replace('.php', '').replace(/#$|\/$/, '');
            let inputs = main.find('.bxc-input');
            inputs.removeClass('bxc-error');
            main.find('.bxc-info').html('');
            inputs.e.forEach(e => {
                let id = _(e).attr('id');
                let input = _(e).find('input');
                let value = input.val().trim();
                if (!value && input.e[0].hasAttribute('required')) {
                    error = true;
                    _(e).addClass('bxc-error');
                }
                installation_data[id] = value;
            });
            if (error) {
                error = 'Username, password and the database details are required.';
                let password = installation_data.password;
                if (password) {
                    if (password.length < 8) {
                        error = 'Minimum password length is 8 characters.';
                    } else if (password != installation_data['password-check']) {
                        error = 'The passwords do not match.';
                    }
                }
                showError(error, this);
                return;
            }
            if (url.includes('?')) url = url.substr(0, url.indexOf('?'));
            installation_data['url'] = url + '/';
            ajax('installation', { installation_data: JSON.stringify(installation_data) }, (response) => {
                if (response === true) {
                    location.reload();
                } else {
                    showError(response, this);
                }
            });
        });

        main.on('click', '.bxc-nav > div', function () {
            nav(_(this));
        });

        main.on('click', '#bxc-submit-login', function () {
            if (loading(this)) return;
            ajax('login', { username: main.find('#username input').val().trim(), password: main.find('#password input').val().trim(), code: main.find('#two-fa input').val().trim() }, (response) => {
                let error = false;
                if (response == 'ip-ban') {
                    error = 'Too many login attempts. Please retry again in a few hours.';
                }
                if (response == '2fa') {
                    error = 'Invalid verification code.';
                }
                if (!response) {
                    error = 'Invalid username or password.';
                }
                if (error) {
                    main.find('.bxc-info').html(bxc_(error));
                    return loading(this, false);
                }
                BOXCoin.cookie('BXC_LOGIN', response, 365, 'set');
                location.reload();
            });
        });

        main.on('click', '#bxc-logout', function () {
            BOXCoin.cookie('BXC_LOGIN', false, false, 'delete');
            BOXCoin.cookie('BXC_CLOUD', false, false, 'delete');
            location.reload();
        });

        main.on('click', '#bxc-card', function () {
            _(this).html('');
        });

        main.on('input', '.bxc-search-input', function () {
            BOXCoin.search(this, (search, icon) => {
                if (active_area_id == 'transactions') {
                    BXCTransactions.query(icon, search);
                }
            });
        });

        main.on('click', '#bxc-table-balances tr', function () {
            let cryptocurrency = _(this).data('cryptocurrency');
            for (var i = 0; i < BXC_ADDRESS[cryptocurrency].length; i++) {
                let url = BXCAdmin.explorer(cryptocurrency, BXC_ADDRESS[cryptocurrency][i]);
                if (url) {
                    window.open(url);
                }
            }
        });

        window.onscroll = function () {
            if (active_area && (document.documentElement || document.body.parentNode || document.body).scrollTop + window.innerHeight == _.documentHeight() && pagination_count) {
                if (active_area_id == 'transactions') {
                    if (!_(active_area).find('> .bxc-loading-global').e.length) {
                        BXCTransactions.print(() => {
                            scrollBottom();
                            _(active_area).find('> .bxc-loading-global').remove();
                        });
                        _(active_area).append('<div class="bxc-loading-global bxc-loading"></div>');
                    }
                }
            }
        };

        window.onpopstate = function () {
            openURL();
        }

        document.addEventListener('click', function (e) {
            if (BXCAdmin.active_element && !BXCAdmin.active_element.contains(e.target)) {
                activate(BXCAdmin.active_element, false);
                BXCAdmin.active_element = false;
            }
        });

        main.on('click', '[data-type="upload-file"] > div', function () {
            if (loading(this, 'check')) {
                return;
            }
            upload_input.val('')
            upload_input.e[0].click();
            upload_target = this;
        });

        if (upload_input && upload_input.e.length) {
            upload_input.e[0].addEventListener('change', function (data) {
                let files = this.files;
                loading(upload_target);
                for (var i = 0; i < files.length; i++) {
                    let file = files[i];
                    let size_mb = file.size / (1024 ** 2);
                    let max_size = BXC_ADMIN_SETTINGS.max_file_size;
                    let form = new FormData();
                    let target = _(upload_target).parent().data('upload-target');
                    if (size_mb > max_size) {
                        return showError(bxc_('Maximum upload size is {R}MB. File size: {R2}MB.').replace('{R}', max_size).replace('{R2}', size_mb.toFixed(2)), this);
                    }
                    form.append('file', file);
                    fetch(BXC_URL + 'upload.php?target=' + target, {
                        method: 'POST',
                        body: form
                    }).then((response) => response.json()).then(function (response) {
                        loading(upload_target, false);
                        if (response[0]) {
                            let parent = _(upload_target).parent();
                            let input = parent.find('input');
                            if (input.val()) {
                                ajax('delete-file', { file_name: input.val(), folder: target == 'checkout-file' ? 'checkout/' : '' });
                            }
                            input.val(response[2]);
                            if (parent.hasClass('bxc-upload-image')) {
                                parent.find('.bxc-btn-icon').e[0].style.backgroundImage = 'url("' + response[1] + '")';
                            }
                        } else {
                            showError(response[1]);
                        }
                        BOXCoin.event('BXCUpload', response);
                    });
                }
                this.value = '';
            });
        }

        main.on('click', '.bxc-upload-image > i', function () {
            let prev = _(this).prev();
            prev.attr('style', '');
            if (prev.prev().val()) {
                ajax('delete-file', { file_name: prev.prev().val() });
            }
            prev.prev().val('');
        });

        body.on('click', '#bxc-confirm-btn, #bxc-cancel-btn', function () {
            if (_(this).attr('id') == 'bxc-cancel-btn') {
                if (confirm_on_success[1]) {
                    loading(confirm_on_success[1], false);
                };
            } else {
                confirm_on_success[0]();
            }
            BOXCoin.lightboxClose();
        });
    });
}());