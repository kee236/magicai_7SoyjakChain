/*
 * ==========================================================
 * CLIENT SCRIPT
 * ==========================================================
 *
 * Main JavaScript file used on both admin and client sides. © 2022-2024 boxcoin.dev. All rights reserved.
 * 
 */

'use strict';
(function () {

    var BXC_VERSION = '1.2.7';
    var body;
    var checkouts;
    var timeout = false;
    var previous_search = '';
    var countdown;
    var intervals = {};
    var busy = {};
    var ND = 'undefined';
    var admin = typeof BXC_ADMIN !== ND || document.getElementsByClassName('bxc-admin').length;
    var active_checkout;
    var active_checkout_id;
    var active_button;
    var scripts = document.getElementsByTagName('script');
    var language = typeof BXC_LANGUAGE !== ND ? BXC_LANGUAGE : false;
    var responsive = window.innerWidth < 769;
    var cloud = false;
    var exchange = false;

    /*
    * ----------------------------------------------------------
    * _query
    * ----------------------------------------------------------
    */

    var _ = function (selector) {
        return typeof selector === 'object' && 'e' in selector ? selector : (new _.init(typeof selector === 'string' ? document.querySelectorAll(selector) : selector));
    }

    _.init = function (e) {
        this.e = e.tagName != 'SELECT' && (typeof e[0] !== 'undefined' || NodeList.prototype.isPrototypeOf(e)) ? e : [e];
    }

    _.ajax = function (url, paramaters = false, onSuccess = false, method = 'POST') {
        let xhr = new XMLHttpRequest();
        let fd = '';
        xhr.open(method, url, true);
        if (paramaters) {
            if (paramaters.action == 'bxc_wp_ajax') {
                for (var key in paramaters) {
                    if (typeof paramaters[key] === 'object') paramaters[key] = JSON.stringify(paramaters[key]);
                }
                fd = new URLSearchParams(paramaters).toString();
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            } else {
                fd = new FormData();
                fd.append('data', JSON.stringify(paramaters));
            }
        }
        xhr.onload = () => { if (onSuccess) onSuccess(xhr.responseText) };
        xhr.onerror = () => { return false };
        xhr.send(fd);
    }

    _.extend = function (a, b) {
        for (var key in b) if (b.hasOwnProperty(key)) a[key] = b[key];
        return a;
    }

    _.documentHeight = function () {
        let body = document.body, html = document.documentElement;
        return Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight);
    }

    _.init.prototype.on = function (event, sel, handler) {
        for (var i = 0; i < this.e.length; i++) {
            this.e[i].addEventListener(event, function (event) {
                var t = event.target;
                while (t && t !== this) {
                    if (t.matches(sel)) {
                        handler.call(t, event);
                    }
                    t = t.parentNode;
                }
            });
        }
    }

    _.init.prototype.addClass = function (value) {
        for (var i = 0; i < this.e.length; i++) {
            this.e[i].classList.add(value);
        }
        return _(this.e);
    }

    _.init.prototype.removeClass = function (value) {
        value = value.trim().split(' ');
        for (var i = 0; i < value.length; i++) {
            for (var j = 0; j < this.e.length; j++) {
                this.e[j].classList.remove(value[i]);
            }
        }
        return _(this.e);
    }

    _.init.prototype.toggleClass = function (value) {
        for (var i = 0; i < this.e.length; i++) {
            this.e[i].classList.toggle(value);
        }
        return _(this.e);
    }

    _.init.prototype.setClass = function (class_name, add = true) {
        for (var i = 0; i < this.e.length; i++) {
            if (add) {
                _(this.e[i]).addClass(class_name);
            } else {
                _(this.e[i]).removeClass(class_name);
            }
        }
        return _(this.e);
    }

    _.init.prototype.hasClass = function (value) {
        return this.e.length && this.e[0].classList ? this.e[0].classList.contains(value) : false;
    }

    _.init.prototype.find = function (selector) {
        if (selector.indexOf('>') === 0) selector = ':scope' + selector;
        try {
            return this.e.length && this.e[0].querySelectorAll ? _(this.e[0].querySelectorAll(selector)) : false;
        } catch (e) {
            console.warn(e);
            return false;
        }
    }

    _.init.prototype.parent = function () {
        return this.e.length ? _(this.e[0].parentElement) : false;
    }

    _.init.prototype.prev = function () {
        return this.e.length ? _(this.e[0].previousElementSibling) : false;
    }

    _.init.prototype.next = function () {
        return this.e.length ? _(this.e[0].nextElementSibling) : false;
    }

    _.init.prototype.attr = function (name, value = false) {
        if (!this.e.length || typeof this.e[0].getAttribute !== 'function') {
            return;
        }
        if (value === false) {
            return this.e[0].getAttribute(name);
        }
        if (value) {
            this.e[0].setAttribute(name, value);
        } else {
            this.e[0].removeAttribute(name);
        }
        return _(this.e);
    }

    _.init.prototype.data = function (attribute = false, value = false) {
        if (!this.e.length) {
            return;
        }
        let response = {};
        let el = this.e[0];
        if (attribute) {
            return _(this).attr('data-' + attribute, value);
        }
        for (var i = 0, atts = el.attributes, n = atts.length; i < n; i++) {
            response[atts[i].nodeName.substr(5)] = atts[i].nodeValue;
        }
        return response;
    }

    _.init.prototype.html = function (value = false) {
        if (!this.e.length) {
            return;
        }
        if (value === false) {
            return this.e[0].innerHTML;
        }
        if (typeof value === 'string' || typeof value === 'number') {
            this.e[0].innerHTML = value;
        } else {
            this.e[0].appendChild(value);
        }
        return _(this.e);
    }

    _.init.prototype.append = function (value) {
        if (!this.e.length) {
            return;
        }
        var template = document.createElement('template');
        template.innerHTML = value.trim();
        while (template.content.childNodes.length) {
            this.e[0].appendChild(template.content.firstChild);
        }
        return _(this.e);
    }

    _.init.prototype.prepend = function (value) {
        this.e[0].innerHTML = value + this.e[0].innerHTML;
        return _(this.e);
    }

    _.init.prototype.insert = function (value, before = true) {
        var template = document.createElement('template');
        template.innerHTML = value.trim();
        this.e[0].parentNode.insertBefore(template.content.firstChild, before ? this.e[0] : this.e[0].nextElementSibling);
        return _(this.e);
    }

    _.init.prototype.replace = function (content) {
        this.e[0].outerHTML = content;
        return _(this.e);
    }

    _.init.prototype.remove = function () {
        for (var i = 0; i < this.e.length; i++) {
            this.e[i].remove();
        }
    }

    _.init.prototype.is = function (type) {
        if (this.e[0].nodeType == 1 && this.e.length) {
            type = type.toLowerCase();
            return this.e[0].tagName.toLowerCase() == type || _(this).attr('type') == type;
        }
        return false;
    }

    _.init.prototype.index = function () {
        return [].indexOf.call(this.e[0].parentElement.children, this.e[0]);
    }

    _.init.prototype.siblings = function () {
        return this.e[0].parentNode.querySelectorAll(':scope > *')
    }

    _.init.prototype.closest = function (selector) {
        return _(this.e[0].closest(selector));
    }

    _.init.prototype.scrollTo = function () {
        this.e[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    _.init.prototype.val = function (value = null) {
        if (value !== null) {
            for (var i = 0; i < this.e.length; i++) {
                this.e[i].value = value;
            }
        }
        return this.e.length ? this.e[0].value : '';
    }

    _.load = function (src = false, js = true, onLoad = false, content = false) {
        let resource = document.createElement(js ? 'script' : 'link');
        if (src) {
            if (js) resource.src = src; else resource.href = src;
            resource.type = js ? 'text/javascript' : 'text/css';
        } else {
            resource.innerHTML = content;
        }
        if (onLoad) {
            resource.onload = function () {
                onLoad();
            }
        }
        if (!js) {
            resource.rel = 'stylesheet';
        }
        document.head.appendChild(resource);
    }

    _.storage = function (name, value = -1, default_value = false) {
        if (value === -1) {
            let value = localStorage.getItem(name);
            return value ? JSON.parse(value) : default_value;
        }
        localStorage.setItem(name, JSON.stringify(value));
    }

    _.download = function (url) {
        var anchor = document.createElement('a');
        var checks = ['?', '#', '&'];
        anchor.href = url;
        url = url.substr(url.lastIndexOf('/') + 1);
        for (var i = 0; i < checks.length; i++) {
            if (url.includes(checks[i])) {
                url = url.substr(0, url.lastIndexOf(checks[i]));
            }
        }
        anchor.download = url;
        document.body.appendChild(anchor);
        anchor.click();
        document.body.removeChild(anchor);
    }

    _.init.prototype.i = function (index) {
        return _(this.e[index]);
    }

    window._query = _;

    /*
    * ----------------------------------------------------------
    * Functions
    * ----------------------------------------------------------
    */

    var BOXCoin = {
        loading: function (element, action = -1) {
            element = _(element);
            let is_loading = element.hasClass('bxc-loading');
            if (action == 'check') {
                return is_loading;
            }
            if (action !== -1) {
                return element.setClass('bxc-loading', action === true);
            }
            if (is_loading) {
                return true;
            } else {
                this.loading(element, true);
            }
            return false;
        },

        activate: function (element, activate = true) {
            return _(element).setClass('bxc-active', activate);
        },

        ajax: function (function_name, data = false, onSuccess = false) {
            data.function = function_name;
            data.language = language;
            data.cloud = cloud;
            _.ajax(BXC_URL + 'ajax.php', data, (response) => {
                let error = false;
                let result = false;
                try {
                    result = JSON.parse(response);
                    error = !(result && 'success' in result && result.success);
                } catch (e) {
                    error = true;
                }
                if (error) {
                    body.find('.bxc-loading:not([data-area])').removeClass('bxc-loading');
                    console.error(response);
                    busy[active_checkout_id] = false;
                } else if (onSuccess) {
                    onSuccess(result.response);
                }
            });
        },

        cookie: function (name, value = false, expiration_days = false, action = 'get', seconds = false) {
            let https = location.protocol == 'https:' ? 'SameSite=None;Secure;' : '';
            if (action == 'get') {
                let cookies = document.cookie.split(';');
                for (var i = 0; i < cookies.length; i++) {
                    var cookie = cookies[i];
                    while (cookie.charAt(0) == ' ') {
                        cookie = cookie.substring(1);
                    }
                    if (cookie.indexOf(name) == 0) {
                        let value = cookie.substring(name.length + 1, cookie.length);
                        return value ? value : false;
                    }
                }
                return false;
            } else if (action == 'set') {
                let date = new Date();
                date.setTime(date.getTime() + (expiration_days * (seconds ? 1 : 86400) * 1000));
                document.cookie = name + "=" + value + ";expires=" + date.toUTCString() + ";path=/;" + https;
            } else if (this.cookie(name)) {
                document.cookie = name + "=" + value + ";expires=Thu, 01 Jan 1970 00:00:01 GMT;path=/;" + https;
            }
        },

        beautifyTime: function (datetime, extended = false, future = false) {
            let date;
            if (datetime == '0000-00-00 00:00:00') return '';
            if (datetime.indexOf('-') > 0) {
                let arr = datetime.split(/[- :]/);
                date = new Date(arr[0], arr[1] - 1, arr[2], arr[3], arr[4], arr[5]);
            } else {
                let arr = datetime.split(/[. :]/);
                date = new Date(arr[2], arr[1] - 1, arr[0], arr[3], arr[4], arr[5]);
            }
            let now = new Date();
            let date_string = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), date.getHours(), date.getMinutes(), date.getSeconds()));
            let diff_days = ((now - date_string) / 86400000) * (future ? -1 : 1);
            let time = date_string.toLocaleTimeString('en-EN', { hour: '2-digit', minute: '2-digit' });
            if (time.charAt(0) === '0' && (time.includes('PM') || time.includes('AM'))) {
                time = time.substring(1);
            }
            if (diff_days < 1 && now.getDate() == date_string.getDate()) {
                return `<span>${bxc_('Today')}</span>${extended ? ` <span>${time}</span>` : ''}`;
            } else {
                return `<span>${date_string.toLocaleDateString()}</span>${extended ? ` <span>${time}</span>` : ''}`;
            }
        },

        search: function (input, searchFunction) {
            let icon = _(input).parent().find('i');
            let search = _(input).val().toLowerCase().trim();
            if (loading(icon, 'check')) {
                return;
            }
            if (search == previous_search) {
                this.loading(icon, false);
                return;
            }
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                previous_search = search;
                searchFunction(search, icon);
                this.loading(icon);
            }, 500);
        },

        searchClear: function (icon, onSuccess) {
            let search = _(icon).next().val();
            if (search) {
                _(icon).next().val('');
                onSuccess();
            }
        },

        getURL: function (name = false, url = false) {
            if (!url) url = location.href;
            if (!name) {
                var c = url.split('?').pop().split('&');
                var p = {};
                for (var i = 0; i < c.length; i++) {
                    var d = c[i].split('=');
                    p[d[0]] = BOXCoin.escape(d[1]);
                }
                return p;
            }
            return BOXCoin.escape(decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(url) || [, ""])[1].replace(/\+/g, '%20') || ""));
        },

        escape: function (string) {
            if (!string) return string;
            return string.replaceAll('<script', '&lt;script').replaceAll('</script', '&lt;/script').replaceAll('javascript:', '').replaceAll('onclick=', '').replaceAll('onerror=', '');
        },

        checkout: {
            settings: function (checkout) {
                checkout = _(checkout);
                let checkout_id = checkout.data('bxc');
                if (!checkout_id) checkout_id = checkout.data('boxcoin'); // Deprecated
                let settings = { checkout_id: checkout_id };
                if (checkout_id.includes('custom')) {
                    return _.extend(settings, _(checkout).data());
                }
                return settings;
            },

            init: function (settings, area, onSuccess = false) {
                let active_transaction = this.storageTransaction(settings.checkout_id);
                let user_details = storage('bxc-user-details', -1, {});
                let is_fiat_payment = active_transaction.id && (BOXCoin.getURL('cc') || BOXCoin.isFiat(active_transaction.external_reference) || BOXCoin.isFiat(active_transaction.cryptocurrency));
                let payment_status = BOXCoin.getURL('payment_status');
                let redirect = BOXCoin.getURL('redirect').replace('payment_status=paid', '');
                let exchange_quote = storage('bxc-quote');
                if (redirect) {
                    redirect += (payment_status ? (redirect.includes('?') ? '&' : '?') + 'payment_status=' + payment_status : '');
                }

                // Exchange
                if (exchange_quote && redirect && (payment_status == 'cancelled' || BOXCoin.getURL('cc'))) {
                    return document.location = redirect;
                }

                // Checkout init
                _.ajax(BXC_URL + 'init.php', { checkout: settings, language: language, cloud: cloud }, (response) => {
                    area = _(area);
                    area.html(response);
                    if (active_transaction) {
                        active_checkout_id = area.data('bxc');
                        if (!active_transaction) active_transaction = checkout.data('boxcoin'); // Deprecated
                        active_checkout = area.find('> .bxc-main');
                        if (active_transaction.detected || is_fiat_payment) {
                            if (payment_status == 'cancelled') {
                                return this.cancelTransaction(true);
                            } else {
                                this.monitorTransaction(active_transaction.encrypted);
                            }
                        } else {
                            let time = parseInt((Date.now() - active_transaction.storage_time) / 1000);
                            let minutes = BXC_SETTINGS.countdown - Math.floor(time / 60);
                            let seconds = 60 - (time % 60);
                            if (seconds) minutes--;
                            this.initTransaction(active_transaction.id, active_transaction.amount, active_transaction.to, active_transaction.cryptocurrency, active_transaction.external_reference, [minutes, seconds], active_transaction.custom_token, active_transaction.redirect, active_transaction.vat, active_transaction.encrypted, active_transaction.amount_extra, active_transaction.confirmations);
                        }
                        if (active_checkout.hasClass('bxc-popup')) {
                            this.openPopup(active_checkout_id);
                        }
                    }
                    for (var key in user_details) {
                        area.find(`.bxc-user-details [name="${key}"]`).val(user_details[key]);
                    };
                    if (storage('bxc-billing')) {
                        BOXCoin.checkout.showInvoiceBox(area.find('#bxc-btn-invoice').e[0]);
                    }
                    if (is_fiat_payment) {
                        active_checkout.removeClass('bxc-tx-cnt-active').addClass('bxc-complete-cnt-active');
                        active_checkout.find('.bxc-complete-cnt .bxc-text').addClass('bxc-order-processing');
                        this.showCancelButton('.bxc-complete-cnt');
                    } else if (BOXCoin.getURL('pay') && !active_transaction) {
                        if (!payment_status) {
                            area.find('.bxc-payment-methods > div').addClass('bxc-hidden');
                            area.find(`.bxc-payment-methods [data-cryptocurrency="${BOXCoin.getURL('pay').toLowerCase()}"]`).removeClass('bxc-hidden').e[0].click();
                            if (!exchange_quote || (!BOXCoin.getURL('checkout_id').includes(exchange_quote.id) && !location.href.includes(exchange_quote.id))) {
                                body.removeClass('bxc-loading');
                            }
                        } else if (redirect) {
                            return document.location = redirect
                        }
                    }
                    if (BOXCoin.metamask.active()) {
                        body.find('#metamask').removeClass('bxc-hidden');
                    }
                    if (onSuccess) {
                        onSuccess(response);
                    }
                });
            },

            initTransaction: function (transaction_id, amount, address, cryptocurrency, external_reference, countdown_partial = false, custom_token = false, redirect = false, vat = false, encrypted_transaction = false, amount_extra = false, min_confirmations = 3) {
                let pay_cnt = active_checkout.find('.bxc-pay-cnt');
                let area = pay_cnt.find('.bxc-pay-address');
                let cryptocurrency_uppercase = BOXCoin.baseCode(cryptocurrency.toUpperCase());
                let countdown_area = pay_cnt.find('[data-countdown]');
                let data = { id: transaction_id, amount: amount, to: address, cryptocurrency: cryptocurrency, external_reference: external_reference, custom_token: custom_token, redirect: redirect, vat: vat, encrypted: encrypted_transaction, amount_extra: amount_extra, min_confirmations: min_confirmations };
                let network_label = BOXCoin.network(cryptocurrency, false, true);
                let ln = cryptocurrency == 'btc_ln';
                active_checkout.addClass('bxc-pay-cnt-active').data('active', cryptocurrency).data('custom-token', custom_token ? custom_token.type : '');
                area.find('.bxc-text').html(ln ? bxc_('Lightning Invoice') : cryptocurrency_uppercase + network_label + ' ' + bxc_('address'));
                area.find('.bxc-title').html(address);
                area.find('.bxc-clipboard').data('text', window.btoa(address));
                area = pay_cnt.find('.bxc-pay-amount');
                area.find('.bxc-title').html(`${amount} ${cryptocurrency_uppercase}<div>${amount_extra && !amount_extra.toUpperCase().includes(cryptocurrency_uppercase) ? amount_extra.toUpperCase() : ''}</div>`);
                area.find('.bxc-clipboard').data('text', window.btoa(amount));
                active_checkout.data('transaction-id', transaction_id);
                area = pay_cnt.find('.bxc-qrcode-text');
                pay_cnt.find('.bxc-qrcode-link').attr('href', BXC_SETTINGS.names[cryptocurrency][0] + ':' + address + '&amount=' + amount);
                pay_cnt.find('.bxc-qrcode').attr('src', BXC_URL + 'vendor/qr.php?s=qr&d=' + BXC_SETTINGS.names[cryptocurrency][0] + '%3A' + address + '%3Famount%3D' + amount + '&md=1&wq=0&fc=' + BXC_SETTINGS.qr_code_color);
                area.find('img').attr('src', custom_token ? custom_token.img : `${BXC_URL}media/icon-${cryptocurrency}.svg`);
                area.find('div').html(bxc_(ln ? 'Bitcoin Lightning Network' : 'Only send {T} to this address').replace('{T}', cryptocurrency_uppercase + network_label));
                countdown_area.html('');
                countdown = countdown_partial ? [countdown_partial[0], countdown_partial[1], true] : [BXC_SETTINGS.countdown, 0, true];
                clearInterval(intervals[active_checkout_id]);
                intervals[active_checkout_id] = setInterval(() => {
                    countdown_area.html(`${countdown[0]}:${countdown[1] < 10 ? '0' : ''}${countdown[1]}`);
                    countdown[1]--;
                    if (countdown[1] <= 0) {
                        if (countdown[0] <= 0) {
                            setTimeout(() => {
                                this.initTransaction_cancel(data, transaction_id);
                            }, 500);
                        }
                        if (countdown[0] < 5 && countdown[2]) {
                            countdown_area.parent().addClass('bxc-countdown-expiring');
                            countdown[2] = false;
                        }
                        countdown[0]--;
                        countdown[1] = 59;
                    }
                }, 1000);
                clearInterval(intervals['check-' + active_checkout_id]);
                intervals['check-' + active_checkout_id] = setInterval(() => {
                    if (!busy[active_checkout_id]) {
                        busy[active_checkout_id] = true;
                        ajax('check-transactions', { transaction_id: transaction_id }, (response) => {
                            busy[active_checkout_id] = false;
                            if (response) {
                                if (response == 'expired') {
                                    this.initTransaction_cancel(data, transaction_id, 'Transaction expired');
                                    return console.error(response);
                                }
                                if (Array.isArray(response) && response[0] == 'error') {
                                    return console.error(response[1]);
                                }
                                if (ln) {
                                    if (response.confirmed) {
                                        this.completeTransaction(response, this.storageTransaction(active_checkout_id));
                                    }
                                } else {
                                    this.monitorTransaction(response);
                                }
                            }
                        });
                    }
                }, ln ? 1000 : 5000);
                this.storageTransaction(active_checkout_id, data);
                BOXCoin.event('TransactionStarted', data);
                loading(pay_cnt, false);
                loading(body, false);
            },

            initTransaction_cancel: function (data, transaction_id, title = false, text = false) {
                BOXCoin.event('TransactionCancelled', data);
                active_checkout.find('#bxc-expired-tx-id').html(transaction_id);
                if (title) {
                    active_checkout.find('.bxc-failed-cnt .bxc-title').html(bxc_(title));
                }
                if (text) {
                    active_checkout.find('.bxc-failed-cnt .bxc-title + .bxc-text').html(bxc_(text));
                }
                active_checkout.removeClass('bxc-tx-cnt-active bxc-pay-cnt-active').addClass('bxc-failed-cnt-active');
                this.cancelTransaction(true);
            },

            monitorTransaction: function (encrypted_transaction) {
                let active_transaction = this.storageTransaction(active_checkout_id);
                let interval_id = 'check-' + active_checkout_id;
                clearInterval(intervals[active_checkout_id]);
                clearInterval(intervals[interval_id]);
                intervals[interval_id] = setInterval(() => {
                    if (active_checkout && !busy[active_checkout_id]) {
                        busy[active_checkout_id] = true;
                        ajax('check-transaction', { transaction: encrypted_transaction }, (response) => {
                            busy[active_checkout_id] = false;
                            active_checkout.find('.bxc-tx-confirmations').html(response.confirmations + ' / ' + active_transaction.min_confirmations);
                            if (response.confirmations) {
                                active_checkout.find('.bxc-tx-status').addClass('bxc-tx-status-confirmed').html(bxc_('Confirmed'));
                            }
                            if (response.confirmed) {
                                if (response.underpayment) {
                                    active_checkout.removeClass('bxc-tx-cnt-active').addClass('bxc-underpayment-cnt-active');
                                    clearInterval(intervals[interval_id]);
                                    active_checkout.find('#bxc-underpaid-tx-id').html(active_transaction.id);
                                    this.cancelTransaction();
                                } else {
                                    this.completeTransaction(active_transaction, response, interval_id);
                                }
                            }
                        });
                    }
                }, 3000);
                if (active_transaction) {
                    active_transaction.detected = true;
                    active_transaction.encrypted = encrypted_transaction;
                }
                if (active_checkout_id) {
                    this.storageTransaction(active_checkout_id, active_transaction);
                }
                if (active_checkout) {
                    active_checkout.addClass('bxc-tx-cnt-active');
                    active_checkout.find('.bxc-tx-status').removeClass('bxc-tx-status-confirmed').html(bxc_('Pending'));
                    active_checkout.find('.bxc-tx-confirmations').html('0 / ' + active_transaction.min_confirmations);
                    this.showCancelButton('.bxc-tx-cnt', BOXCoin.isFiat(active_transaction.external_reference) || BOXCoin.isFiat(active_transaction.cryptocurrency) ? 30000 : 5000);
                }
            },

            completeTransaction: function (active_transaction, response, interval_id = false) {
                let area = active_checkout.find('.bxc-complete-cnt');
                if (response.invoice) {
                    area.append(`<a href="${response.invoice}" target="_blank" class="bxc-link bxc-underline">${bxc_('View Invoice')}</div>`);
                }
                if (response.redirect || BXC_SETTINGS.redirect) {
                    setTimeout(() => { document.location.href = response.redirect ? response.redirect : BXC_SETTINGS.redirect }, 300);
                }
                if (response.downloads_url) {
                    ajax('shop-downloads', { encrypted_transaction_id: BOXCoin.getURL('downloads', response.downloads_url) }, (response) => {
                        for (var i = 0; i < response[0].length; i++) {
                            _.download(response[0][i]);
                        }
                        setTimeout(() => { ajax('shop-delete-downloads', { file_names: response[1] }) }, 1000);
                    });
                }
                if (response.license_key) {
                    area.append(`<div class="bxc-input bxc-input-license-key"><span>${bxc_('License Key')}</span><input value="${response.license_key}" type="text" readonly></div>`);
                }
                if (active_transaction.redirect) {
                    setTimeout(() => { document.location.href = encodeURI(`${active_transaction.redirect}${active_transaction.redirect.includes('?') ? '&' : '?'}transaction_id=${active_transaction.id}&amount=${active_transaction.amount}&address=${active_transaction.address}&cryptocurrency=${active_transaction.cryptocurrency}&external_reference=${active_transaction.external_reference}`) }, 300);
                } else {
                    active_checkout.removeClass('bxc-tx-cnt-active').addClass('bxc-complete-cnt-active');
                }
                clearTimeout(timeout);
                clearInterval(intervals[interval_id]);
                area.find('.bxc-text').removeClass('bxc-order-processing');
                area.find('.bxc-cancel-transaction').remove();
                BOXCoin.event('TransactionCompleted', active_transaction);
                this.cancelTransaction();
            },

            cancelTransaction: function (delete_db_transaction = false) {
                if (delete_db_transaction && !BOXCoin.getURL('demo')) {
                    let active_transaction = this.storageTransaction(active_checkout_id);
                    if (active_transaction && active_transaction.encrypted && !active_transaction.prevent_cancel) {
                        ajax('cancel-transaction', { transaction: active_transaction.encrypted });
                    }
                }
                clearInterval(intervals[active_checkout_id]);
                clearInterval(intervals['check-' + active_checkout_id]);
                active_checkout.removeClass('bxc-pay-cnt-active');
                busy[active_checkout_id] = false;
                this.storageTransaction(active_checkout_id, 'delete');
                active_checkout_id = false;
                active_checkout = false;
            },

            storageTransaction: function (checkout_id, transaction = false) {
                let transactions = storage('bxc-active-transaction', -1, {});
                let exists = checkout_id in transactions;
                if (transaction) {
                    if (transaction == 'delete') {
                        delete transactions[checkout_id];
                    } else {
                        if (exists) {
                            transaction = Object.assign(transactions[checkout_id], transaction);
                            transaction.storage_time = transactions[checkout_id].storage_time;
                        } else {
                            transaction.storage_time = Date.now();
                        }
                        transaction.storage_time = exists ? transactions[checkout_id].storage_time : Date.now();
                        transactions[checkout_id] = transaction;
                    }
                } else {
                    if (exists) {
                        if (transactions[checkout_id].detected || ((transactions[checkout_id].storage_time + (BXC_SETTINGS.countdown * 60000)) > Date.now())) {
                            return transactions[checkout_id];
                        } else {
                            delete transactions[checkout_id];
                        }
                    }
                }
                storage('bxc-active-transaction', transactions);
                return false;
            },

            show: function (checkout_id) {
                body.find(`[data-bxc="${checkout_id}"] > div, [data-boxcoin="${checkout_id}"] > div`).removeClass('bxc-hidden'); // Deprecated: remove , [data-boxcoin="${checkout_id}"] > div
            },

            openPopup(checkout_id, open = true) {
                activate(body.find(`[data-bxc="${checkout_id}"],[data-boxcoin="${checkout_id}"]`).find('.bxc-popup,.bxc-popup-overlay'), open); // Deprecated: remove ,[data-boxcoin="${checkout_id}"]
            },

            getBillingDetails: function (area) {
                if (area.find('#bxc-billing [name="name"]').val().trim()) {
                    let billing = {};
                    area.find('#bxc-billing input, #bxc-billing select').e.forEach(e => {
                        billing[_(e).attr('name')] = _(e).val();
                    });
                    storage('bxc-billing', billing);
                    return billing;
                }
                return '';
            },

            vat: function (checkout) {
                let vat = checkout.find('.bxc-vat');
                let select = checkout.find('#bxc-billing [name="country"]').e[0];
                if (vat.e.length) {
                    loading(vat);
                    ajax('vat', {
                        amount: checkout.data('discount-price') ? checkout.data('discount-price') : checkout.data('start-price'),
                        country_code: _(select.options[select.selectedIndex]).data('country-code'),
                        currency: checkout.data('currency'),
                        vat_number: checkout.find('[name="vat"]').val()
                    }, (response) => {
                        vat.html(response[4]);
                        vat.attr('data-country', response[3]).attr('data-country-code', response[2]).attr('data-amount', response[1]).attr('data-percentage', response[5]);
                        checkout.attr('data-price', response[0]);
                        checkout.find('.bxc-amount-fiat-total').html(response[0]);
                        loading(vat, false);
                    });
                }
            },

            showInvoiceBox: function (btn) {
                let billing = storage('bxc-billing');
                let checkout = checkoutParent(btn);
                let countries = checkout.find('#bxc-billing [name="country"]');
                if (billing) {
                    for (var key in billing) {
                        checkout.find(`#bxc-billing [name="${key}"]`).val(billing[key]);
                    }
                } else {
                    countries.val(checkout.find('.bxc-vat').attr('data-country'));
                }
                _(btn).addClass('bxc-hidden');
                this.vat(checkout);
                _('#bxc-billing').removeClass('bxc-hidden');
            },

            showCancelButton: function (area, timeout_seconds = 5000) {
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    active_checkout.find(area).append(`<div class="bxc-cancel-transaction bxc-underline">${bxc_('Cancel')}</div>`);
                }, timeout_seconds);
            }
        },

        event: function (name, data = {}) {
            data['checkout_id'] = active_checkout_id;
            document.dispatchEvent(new CustomEvent('BXC' + name, { detail: data }));
        },

        baseCode: function (cryptocurrency_code) {
            return cryptocurrency_code.replace('_tron', '').replace('_TRON', '').replace('_bsc', '').replace('_BSC', '').replace('_ln', '').replace('_LN', '').replace('_sol', '').replace('_SOL', '');
        },

        network: function (cryptocurrency_code, label = true, exclude_optional = false) {
            let networks = admin ? BXC_ADMIN_SETTINGS.cryptocurrencies : BXC_SETTINGS.cryptocurrencies;
            let code = label === 'code';
            cryptocurrency_code = cryptocurrency_code.toLowerCase();
            if (exclude_optional && ['btc', 'eth', 'ltc', 'xrp', 'algo', 'bch', 'doge', 'bnb', 'sol'].includes(cryptocurrency_code)) {
                return '';
            }
            for (var key in networks) {
                if (networks[key].includes(cryptocurrency_code)) {
                    if (code) return key.toLowerCase();
                    let text = key.replace('TRX', 'TRON').replace('SOL', 'SOLANA') + ' ' + bxc_('network');
                    return label ? `<span class="bxc-label">${text}</span>` : ' ' + bxc_('on') + ' ' + text;
                }
            }
            return code ? cryptocurrency_code : '';
        },

        isFiat: function (value) {
            return ['stripe', 'verifone', 'paypal'].includes(value) || (value.length == 3 && !BOXCoin.network(value));
        },

        lightbox: function (title, content, buttons = [], id = '') {
            let lightbox = body.find('#bxc-lightbox');
            let code = '';
            for (var i = 0; i < buttons.length; i++) {
                code += `<div id="${buttons[i][0]}" class="bxc-btn">${buttons[i][1]}</div>`;
            }
            lightbox.find('.bxc-title').html(title);
            lightbox.find('.bxc-lightbox-buttons').html(code);
            lightbox.find('#bxc-lightbox-main').html(content).e[0].style.maxHeight = (window.innerHeight - (responsive ? 130 : 183)) + 'px';
            lightbox.data('lightbox-id', id);
            activate(body.find('#bxc-lightbox-loading'), false);
            activate(lightbox);
        },

        lightboxClose: function () {
            activate(body.find('#bxc-lightbox'), false);
        },

        metamask: {
            accounts: false,

            transactionRequest: async function (value, to, cryptocurrency_code = false, onSuccess = false) {
                let params = {
                    from: this.accounts[0],
                    to: cryptocurrency_code ? BOXCoin.web3.tokens[cryptocurrency_code][0] : to,
                    value: cryptocurrency_code ? '' : value,
                    data: cryptocurrency_code ? BOXCoin.web3.getData(to, value) : ''
                };
                let active_cryptocurrency = BOXCoin.checkout.storageTransaction(active_checkout_id);
                if (active_cryptocurrency && !['BAT', 'ETH'].includes(active_cryptocurrency.cryptocurrency.toUpperCase())) {
                    params.gas = "10000";
                }
                await ethereum.request({
                    method: 'eth_sendTransaction',
                    params: [params]
                }).then((hash) => {
                    if (onSuccess) onSuccess(hash);
                });
            },

            transaction: async function (value, to, token = false, onSuccess = false) {
                let chainID = [1, '1'];
                if (token) {
                    token = token.toUpperCase();
                    if (['ETH', 'BNB'].includes(token)) {
                        if (token == 'BNB') {
                            chainID = [56, '38'];
                        }
                        token = false;
                    } else {
                        if (typeof Web3 === ND) {
                            return _.load(BXC_URL + 'vendor/web3.js', true, () => { this.transaction(value, to, token, onSuccess) });
                        }
                        if (!BOXCoin.web3.tokens) {
                            return BOXCoin.web3.getTokens(() => { this.transaction(value, to, token, onSuccess) });
                        }
                    }
                }
                if (!value.substr(0, 2) !== '0x') {
                    value = BOXCoin.web3.toHex(value, token ? BOXCoin.web3.tokens[token][1] : 18);
                }
                if (ethereum.networkVersion != chainID[0]) {
                    try {
                        await ethereum.request({
                            method: 'wallet_switchEthereumChain',
                            params: [{ chainId: '0x' + chainID[1] }]
                        }).then(() => {
                            this.transactionCheckAccount(value, to, token, onSuccess);
                        });
                    } catch (error) {
                        if (error.code === 4902) {
                            await ethereum.request({
                                method: 'wallet_addEthereumChain',
                                params: [
                                    {
                                        chainName: 'Polygon Mainnet',
                                        chainId: web3.toHex(chainId),
                                        nativeCurrency: { name: 'MATIC', decimals: 18, symbol: 'MATIC' },
                                        rpcUrls: ['https://polygon-rpc.com/']
                                    }
                                ]
                            });
                        } else {
                            console.error(error);
                        }
                    }
                } else {
                    this.transactionCheckAccount(value, to, token, onSuccess);
                }
                if (active_button) {
                    loading(active_button, false);
                    active_button = false;
                }
            },

            transactionCheckAccount: async function (value, to, token = false, onSuccess = false) {
                if (this.accounts) {
                    this.transactionRequest(value, to, token, onSuccess);
                } else {
                    await ethereum.request({ method: 'eth_requestAccounts' }).then((response) => {
                        this.accounts = response;
                        this.transactionRequest(value, to, token, onSuccess);
                    });
                }
            },

            active: function () {
                return window.ethereum && window.ethereum.isMetaMask;
            }
        },

        web3: {
            tokens: false,

            getData: function (to, amount) {
                const web3 = new Web3();
                return web3.eth.abi.encodeFunctionCall({ constant: false, inputs: [{ name: '_to', type: 'address' }, { name: '_value', type: 'uint256' }], name: 'transfer', outputs: [], payable: false, stateMutability: 'nonpayable', type: 'function' }, [to, amount]);
            },

            toHex: function (value, decimals) {
                return '0x' + (parseFloat(value) * (10 ** decimals)).toString(16);
            },

            getTokens: function (onSuccess = false) {
                ajax('get-tokens', {}, (response) => {
                    BOXCoin.web3.tokens = response;
                    if (onSuccess) onSuccess();
                });
            }
        }
    }

    window.BOXCoin = BOXCoin;

    function bxc_(text) {
        return BXC_TRANSLATIONS && text in BXC_TRANSLATIONS ? BXC_TRANSLATIONS[text] : text;
    }

    function loading(element, action = -1) {
        return BOXCoin.loading(element, action);
    }

    function ajax(function_name, data = {}, onSuccess = false) {
        return BOXCoin.ajax(function_name, data, onSuccess);
    }

    function activate(element, activate = true) {
        return BOXCoin.activate(element, activate);
    }

    function checkoutParent(element) {
        return _(element.closest('.bxc-main'));
    }

    function getScriptParameters(url) {
        var c = url.split('?').pop().split('&');
        var p = {};
        for (var i = 0; i < c.length; i++) {
            var d = c[i].split('=');
            p[d[0]] = d[1]
        }
        return p;
    }

    function storage(name, value = -1, default_value = false) {
        return _.storage(name, value, default_value);
    }

    /*
    * ----------------------------------------------------------
    * Init
    * ----------------------------------------------------------
    */

    document.addEventListener('DOMContentLoaded', () => {
        body = _(document.body);
        if (!admin) {
            if (typeof BXC_URL != ND) {
                return;
            }
            if (BOXCoin.getURL('pay')) {
                body.addClass('bxc-loading');
            }
            cloud = BOXCoin.getURL('cloud');
            exchange = body.find('#boxcoin-exchange-init');
            for (var i = 0; i < scripts.length; i++) {
                if (['boxcoin', 'boxcoin-js', 'bxc-cloud'].includes(scripts[i].id)) {
                    let url = scripts[i].src.replace('js/client.js', '').replace('js/client.min.js', '');
                    let parameters = getScriptParameters(url);
                    if (url.includes('?')) {
                        url = url.substr(0, url.indexOf('?'));
                    }
                    _.load(url + 'css/client.css?v=' + BXC_VERSION, false);
                    if ('lang' in parameters) {
                        language = parameters.lang;
                    }
                    if ('cloud' in parameters) {
                        cloud = parameters.cloud;
                    } else if (typeof BXC_CLOUD_TOKEN !== ND) {
                        cloud = BXC_CLOUD_TOKEN;
                    }
                    if (url.includes('?')) {
                        url = url.substr(0, url.indexOf('?'));
                    }
                    checkouts = admin ? [] : body.find('[data-bxc],[data-boxcoin]'); // Deprecated: remove ,[data-boxcoin]
                    let parameters_ajax = { language: language, cloud: cloud };
                    parameters_ajax['init'] = true;
                    _.ajax(url + 'init.php', parameters_ajax, (response) => {
                        if (response === 'no-credit-balance') {
                            return console.warn('Boxcoin: No credit balance.');
                        }
                        _.load(false, true, false, response);
                        if (exchange.e.length) {
                            _.ajax(url + 'init.php', { language: language, cloud: cloud, init_exchange: true }, (response) => {
                                _.load(url + 'apps/exchange/exchange.css?v=' + BXC_VERSION, false, () => {
                                    exchange.html(response);
                                    _.load(url + 'apps/exchange/exchange.js?v=' + BXC_VERSION);
                                });
                            });
                        } else {
                            checkouts.e.forEach(e => {
                                BOXCoin.checkout.init(BOXCoin.checkout.settings(e), e);
                            });
                        }
                    });
                    globalInit();
                    BOXCoin.event('Init');

                    /*
                    * ----------------------------------------------------------
                    * Checkout
                    * ----------------------------------------------------------
                    */

                    body.on('click', '.bxc-payment-methods > div', function () {
                        let checkout = checkoutParent(this);
                        let id = checkout.parent().attr('data-bxc');
                        if (!id) id = checkout.parent().attr('data-boxcoin'); // Deprecated
                        let user_details = {};
                        let custom_fields = {};
                        let user_details_cnt = checkout.find('.bxc-user-details');
                        let custom_fields_cnt = checkout.find('.bxc-custom-fields');
                        let error = false;
                        let error_box = checkout.find('#bxc-error-message');
                        let cryptocurrency_code = _(this).attr('data-cryptocurrency');
                        let external_reference = checkout.attr('data-external-reference');
                        let amount = checkout.attr('data-price');
                        let input = checkout.find('#user-amount');
                        let custom_token = _(this).attr('data-custom-coin');
                        let vat = checkout.find('.bxc-vat');
                        let discount = checkout.find('#bxc-discount-field input').val().trim();
                        let url = document.location.href;
                        let notes = '';
                        checkout.find('.bxc-input').removeClass('bxc-error');
                        if (active_checkout_id && id != active_checkout_id) {
                            error = 'Another transaction is being processed. Complete the transaction or cancel it to start a new one.';
                        }
                        if (user_details_cnt.e.length) {
                            user_details_cnt.find('input').e.forEach(e => {
                                let value = e.value.trim();
                                if (value) {
                                    user_details[e.getAttribute('name')] = value;
                                } else if (!BOXCoin.getURL('currency')) {
                                    e.parentElement.classList.add('bxc-error');
                                    error = 'Please provide your personal information';
                                }
                            });
                            storage('bxc-user-details', user_details);
                        }
                        if (custom_fields_cnt.e.length) {
                            custom_fields_cnt.find('input, select, textarea').e.forEach(e => {
                                let value = e.type == 'checkbox' ? e.checked : e.value.trim();
                                if (value) {
                                    let field_name = e.getAttribute('name');
                                    custom_fields[field_name] = value;
                                    notes += field_name + ': ' + value + '\n';
                                } else if (e.hasAttribute('required')) {
                                    e.parentElement.classList.add('bxc-error');
                                    error = 'Please provide the required information';
                                }
                            });
                            storage('bxc-custom-fields', custom_fields);
                        }
                        if (!amount || amount == -1 || amount == '0') {
                            amount = input.find('input');
                            if (amount) {
                                amount = amount.val();
                                if (!amount) {
                                    input.addClass('bxc-error');
                                    error = 'Please specify the desired amount';
                                }
                            }
                        }
                        error_box.html(error ? bxc_(error) : '');
                        if (error) {
                            body.removeClass('bxc-loading');
                            error_box.scrollTo();
                            return;
                        }
                        active_checkout = checkout;
                        active_checkout_id = id;
                        notes += active_checkout.attr('data-note');
                        if (custom_token) {
                            custom_token = { type: custom_token, img: _(this).find('img').attr('src') };
                        }
                        let billing = BOXCoin.checkout.getBillingDetails(active_checkout);
                        if (vat.e.length && vat.attr('data-amount')) {
                            vat = { amount: vat.attr('data-amount'), percentage: vat.attr('data-percentage'), country: vat.attr('data-country'), country_code: vat.attr('data-country-code') }
                        } else {
                            vat = false;
                        }
                        if (BOXCoin.getURL('cc')) {
                            url = url.replace('cc=' + BOXCoin.getURL('cc'), '');
                            if (url.slice(-1) == '?') {
                                url = url.slice(0, -1);
                            }
                            window.history.replaceState({}, document.title, url);
                        }
                        active_checkout.addClass('bxc-pay-cnt-active');
                        loading(active_checkout.find('.bxc-pay-cnt'));
                        ajax('create-transaction', {
                            amount: amount,
                            cryptocurrency_code: cryptocurrency_code,
                            currency_code: active_checkout.attr('data-currency'),
                            external_reference: active_checkout.attr('data-external-reference'),
                            title: active_checkout.attr('data-title'),
                            note: notes.trim(),
                            custom_token: custom_token,
                            url: url,
                            billing: billing ? JSON.stringify(billing) : '',
                            vat: vat,
                            checkout_id: active_checkout_id,
                            user_details: user_details,
                            discount_code: checkout.data('discount-code'),
                            type: BXC_SETTINGS.exchange && BOXCoin.getURL('type') == 3 ? 3 : (BXC_SETTINGS.shop && !active_checkout_id.includes('custom-') ? 2 : 1)
                        }, (response) => {
                            if (response[0] == 'error') {
                                error_box.html(bxc_('Something went wrong. Please try again or select another cryptocurrency.'));
                                return BOXCoin.checkout.cancelTransaction(true);
                            }
                            if (!checkout.data('price')) {
                                return BOXCoin.checkout.completeTransaction({}, response);
                            }
                            if (BOXCoin.isFiat(cryptocurrency_code)) {
                                let data = { id: response[0], amount: amount, external_reference: cryptocurrency_code, redirect: active_checkout.attr('data-redirect'), encrypted: response[3] };
                                BOXCoin.checkout.storageTransaction(active_checkout_id, data);
                                BOXCoin.event('TransactionStarted', data);
                                document.location = response[2];
                            } else {
                                BOXCoin.checkout.initTransaction(response[0], response[1], response[2], cryptocurrency_code, external_reference, false, custom_token, active_checkout.attr('data-redirect'), vat, response[4], amount + ' ' + active_checkout.attr('data-currency'), response[3]);
                            }
                        });
                    });

                    body.on('click', '.bxc-back', function () {
                        active_checkout.find('.bxc-pay-top-main').addClass('bxc-hidden');
                    });

                    body.on('click', '#bxc-abort-cancel, #bxc-confirm-cancel', function () {
                        active_checkout.find('.bxc-pay-top-main').removeClass('bxc-hidden');
                    });

                    body.on('click', '#bxc-confirm-cancel', function () {
                        let transaction = BOXCoin.checkout.storageTransaction(active_checkout_id);
                        BOXCoin.event('TransactionCancelled', transaction);
                        BOXCoin.checkout.cancelTransaction(true);
                        if (storage('bxc-quote') && transaction.checkout_id.includes(storage('bxc-quote').id)) {
                            let url = transaction.redirect;
                            body.addClass('bxc-loading');
                            document.location = url.includes('payment_status') ? url : (url + (url.includes('?') ? '&' : '?') + 'payment_status=cancelled');
                        }
                    });

                    body.on('click', '.bxc-failed-cnt .bxc-btn', function () {
                        body.find('.bxc-failed-cnt-active').removeClass('bxc-failed-cnt-active');
                    });

                    body.on('click', '#metamask', function () {
                        if (loading(this)) return;
                        active_button = this;
                        let active_transaction = BOXCoin.checkout.storageTransaction(active_checkout_id);
                        BOXCoin.metamask.transaction(active_transaction.amount, active_transaction.to, active_transaction.cryptocurrency);
                    });

                    body.on('click', '.bxc-cancel-transaction', function () {
                        storage('bxc-active-transaction', {});
                        location.reload();
                    });

                    /*
                    * ----------------------------------------------------------
                    * Miscellaneous
                    * ----------------------------------------------------------
                    */

                    body.on('click', '.bxc-btn-popup,.bxc-popup-close', function () {
                        activate(_(this.closest('[data-bxc],[data-boxcoin]')).find('.bxc-popup,.bxc-popup-overlay'), _(this).hasClass('bxc-btn-popup')); // Deprecated: remove ,[data-boxcoin]
                    });

                    body.on('click', '.bxc-collapse-btn', function () {
                        _(this).parent().removeClass('bxc-collapse');
                        _(this).remove();
                    });

                    /*
                    * ----------------------------------------------------------
                    * Invoice & VAT
                    * ----------------------------------------------------------
                    */

                    body.on('click', '#bxc-btn-invoice', function () {
                        BOXCoin.checkout.showInvoiceBox(this);
                    });

                    body.on('click', '#bxc-btn-invoice-close', function () {
                        let checkout = checkoutParent(this);
                        _(this).parent().addClass('bxc-hidden');
                        checkout.find('#bxc-btn-invoice').removeClass('bxc-hidden');
                        checkout.find('#bxc-billing').find('input, select').val('');
                        storage('bxc-billing', false);
                        BOXCoin.checkout.vat(checkout);
                    });

                    body.on('change', '#bxc-billing [name="country"]', function () {
                        BOXCoin.checkout.vat(checkoutParent(this));
                    });

                    body.on('focusout', '#bxc-billing [name="vat"]', function () {
                        BOXCoin.checkout.vat(checkoutParent(this));
                    });

                    /*
                    * ----------------------------------------------------------
                    * Shop
                    * ----------------------------------------------------------
                    */

                    body.on('click', '#bxc-discount-field .bxc-btn', function () {
                        let discount_code = _(this).parent().find('input').val().trim();
                        let checkout = checkoutParent(this);
                        let amount = parseFloat(checkout.data('start-price'));
                        if (!discount_code || loading(this)) {
                            return;
                        }
                        checkout.data('discount-code', '');
                        ajax('apply-discount', {
                            discount_code: discount_code,
                            checkout_id: checkout.data('checkout-id'),
                            amount: amount,
                        }, (response) => {
                            loading(this, false);
                            let valid = response !== false && response !== amount && !isNaN(response) && parseFloat(response) < amount;
                            checkout.find('.bxc-amount-fiat-total').html(valid ? response : checkout.data('start-price'));
                            checkout.data('price', valid ? response : checkout.data('start-price')).data('discount-code', valid ? discount_code : '');
                            checkout.data('discount-price', valid ? (response === 0 ? '0' : response) : '');
                            checkout.find('#bxc-error-message').html(valid ? '' : bxc_('Invalid discount code'));
                            if (valid) {
                                BOXCoin.checkout.vat(checkout);
                            }
                        });
                    });
                    break;
                }
            }
        } else {
            globalInit();
        }

        body.on('click', '#bxc-lightbox-close', function () {
            BOXCoin.lightboxClose();
        });

        body.on('click', '.bxc-select', function () {
            let ul = _(this).find('ul');
            let active = ul.hasClass('bxc-active');
            activate(ul, !active);
            if (admin && !active) {
                setTimeout(() => { BXCAdmin.active_element = ul.e[0] }, 300);
            }
        });

        body.on('click', '.bxc-select li', function () {
            let select = _(this.closest('.bxc-select'));
            let value = _(this).attr('data-value');
            var item = select.find(`[data-value="${value}"]`);
            activate(select.find('li'), false);
            select.find('p').attr('data-value', value).html(item.html());
            activate(item, true);
            if (admin) BXCAdmin.active_element = false;
        });

    }, false);

    /*
    * ----------------------------------------------------------
    * Global
    * ----------------------------------------------------------
    */

    function globalInit() {
        body.on('click', '.bxc-clipboard', function () {
            let tooltip = _(this).find('span');
            let text = tooltip.html();
            navigator.clipboard.writeText(window.atob(_(this).attr('data-text')));
            tooltip.html(bxc_('Copied'));
            activate(this);
            setTimeout(() => {
                activate(this, false);
                tooltip.html(text);
            }, 2000);
        });
    }
}());