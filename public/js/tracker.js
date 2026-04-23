(function () {
    'use strict';

    var scriptTag = document.currentScript || (function () {
        var scripts = document.getElementsByTagName('script');
        for (var i = scripts.length - 1; i >= 0; i--) {
            if (scripts[i].src && scripts[i].src.indexOf('tracker.js') !== -1) {
                return scripts[i];
            }
        }
        return null;
    })();

    if (!scriptTag) return;

    var src = scriptTag.src || '';
    var siteId = scriptTag.getAttribute('data-site-id') || (src.match(/[?&]id=([^&]+)/) || [])[1];
    if (!siteId) return;

    var endpoint = src.replace(/\/js\/tracker\.js.*$/, '/api/collect');
    var trackErrors = scriptTag.getAttribute('data-no-errors') === null;
    var pageEnteredAt = Date.now();
    var heartbeatSent = false;

    function randomId(len) {
        var chars = '0123456789abcdef';
        var result = '';
        for (var i = 0; i < len; i++) {
            result += chars[Math.floor(Math.random() * 16)];
        }
        return result;
    }

    function getSessionId() {
        try {
            var key = 'sa_sid_' + siteId;
            var id = sessionStorage.getItem(key);
            if (!id) {
                id = randomId(32);
                sessionStorage.setItem(key, id);
            }
            return id;
        } catch (e) {
            return randomId(32);
        }
    }

    var sessionId = getSessionId();

    function getUTMParams() {
        var params = {};
        var search = window.location.search;
        var utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
        for (var i = 0; i < utmKeys.length; i++) {
            var key = utmKeys[i];
            var regex = new RegExp('[?&]' + key + '=([^&]*)');
            var m = search.match(regex);
            if (m) params[key] = decodeURIComponent(m[1]);
        }
        return params;
    }

    function send(payload) {
        payload.site_id = siteId;
        var body = JSON.stringify(payload);
        if (navigator.sendBeacon) {
            navigator.sendBeacon(endpoint, body);
        } else {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', endpoint, true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.send(body);
        }
    }

    function sendEvent(type, data) {
        var utm = getUTMParams();
        var payload = {
            type: type,
            session_id: sessionId,
            url: window.location.href,
            hostname: window.location.hostname,
            referrer: document.referrer || '',
            utm_source: utm.utm_source || '',
            utm_medium: utm.utm_medium || '',
            utm_campaign: utm.utm_campaign || '',
            utm_term: utm.utm_term || '',
            utm_content: utm.utm_content || '',
            screen_width: window.screen.width,
            screen_height: window.screen.height,
            language: navigator.language || '',
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || ''
        };

        if (performance && performance.timing) {
            payload.load_time = Math.max(0, performance.timing.loadEventEnd - performance.timing.navigationStart);
        }

        if (data) {
            for (var key in data) {
                if (data.hasOwnProperty(key)) payload[key] = data[key];
            }
        }

        send(payload);
    }

    function sendHeartbeat() {
        if (heartbeatSent) return;
        var duration = Math.round((Date.now() - pageEnteredAt) / 1000);
        if (duration < 1) return;
        heartbeatSent = true;
        sendEvent('heartbeat', { duration: duration });
    }

    function trackPageview() {
        if (pageEnteredAt && !heartbeatSent) sendHeartbeat();
        pageEnteredAt = Date.now();
        heartbeatSent = false;
        sendEvent('pageview');
    }

    sendEvent('pageview');

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'hidden') sendHeartbeat();
    });
    window.addEventListener('pagehide', sendHeartbeat);

    var originalPushState = history.pushState;
    if (originalPushState) {
        history.pushState = function () {
            originalPushState.apply(history, arguments);
            trackPageview();
        };
    }

    var originalReplaceState = history.replaceState;
    if (originalReplaceState) {
        history.replaceState = function () {
            originalReplaceState.apply(history, arguments);
            trackPageview();
        };
    }

    window.addEventListener('popstate', trackPageview);

    window.Statalog = {
        track: function (eventName, properties) {
            sendEvent('event', {
                event_name: eventName,
                properties: properties ? JSON.stringify(properties) : ''
            });
        }
    };

    // ── JS error capture (on by default; opt out with data-no-errors) ──
    if (trackErrors) {
        var MAX_ERR_PER_MIN = 10;
        var STACK_LIMIT = 2000;
        var errWindow = Date.now();
        var errCount = 0;
        function allowErr() {
            var n = Date.now();
            if (n - errWindow > 60000) { errWindow = n; errCount = 0; }
            if (errCount >= MAX_ERR_PER_MIN) return false;
            errCount++;
            return true;
        }
        function sendErr(p) {
            if (!allowErr()) return;
            p.type = 'error';
            p.session_id = sessionId;
            p.url = window.location.href;
            p.hostname = window.location.hostname;
            send(p);
        }
        window.addEventListener('error', function (e) {
            var err = e.error;
            sendErr({
                error_type: 'error',
                message: (err && err.message) || e.message || 'Unknown error',
                source: e.filename || '',
                line: e.lineno || 0,
                col: e.colno || 0,
                stack: ((err && err.stack) || '').slice(0, STACK_LIMIT)
            });
        }, true);
        window.addEventListener('unhandledrejection', function (e) {
            var r = e.reason, msg = '', stack = '';
            if (r instanceof Error) { msg = r.message; stack = (r.stack || '').slice(0, STACK_LIMIT); }
            else if (typeof r === 'string') { msg = r; }
            else { try { msg = JSON.stringify(r); } catch (_) { msg = String(r); } }
            sendErr({ error_type: 'unhandledrejection', message: msg || 'Unhandled rejection', source: '', line: 0, col: 0, stack: stack });
        });
    }
})();
