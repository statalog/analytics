(function () {
    'use strict';

    // ── Heatmap preview mode ──────────────────────────────────────────────────
    // When the dashboard loads a tracked page in an iframe with ?_statalog_preview=1
    // we skip all tracking and instead render incoming click data on a canvas overlay.
    if (window.location.search.indexOf('_statalog_preview=1') !== -1) {
        function renderHeatmapOverlay(clicks) {
            var GRID = 20;
            var old = document.getElementById('_st_heatmap');
            if (old) old.remove();

            var canvas = document.createElement('canvas');
            canvas.id = '_st_heatmap';
            var W = Math.max(document.documentElement.scrollWidth, document.body ? document.body.scrollWidth : 0);
            var H = Math.max(document.documentElement.scrollHeight, document.body ? document.body.scrollHeight : 0);
            canvas.width  = W;
            canvas.height = H;
            canvas.style.cssText = 'position:absolute;top:0;left:0;pointer-events:none;z-index:2147483647';
            if (document.body) {
                if (getComputedStyle(document.body).position === 'static') {
                    document.body.style.position = 'relative';
                }
                document.body.appendChild(canvas);
            }

            var ctx = canvas.getContext('2d');
            var max = 0;
            clicks.forEach(function (c) { if (+c.c > max) max = +c.c; });
            if (max === 0) return;

            clicks.forEach(function (c) {
                var alpha = Math.min(1, Math.sqrt(+c.c / max));
                var x = (+c.cell_x + 0.5) * GRID;
                var y = (+c.cell_y + 0.5) * GRID;
                var r = 32;
                var g = ctx.createRadialGradient(x, y, 0, x, y, r);
                g.addColorStop(0, 'rgba(208,74,31,' + (0.75 * alpha) + ')');
                g.addColorStop(1, 'rgba(208,74,31,0)');
                ctx.fillStyle = g;
                ctx.beginPath();
                ctx.arc(x, y, r, 0, Math.PI * 2);
                ctx.fill();
            });
        }

        window.addEventListener('message', function (ev) {
            if (!ev.data || ev.data.type !== 'statalog_heatmap') return;
            renderHeatmapOverlay(ev.data.clicks || []);
        });

        function signalReady() {
            var H = Math.max(document.documentElement.scrollHeight, document.body ? document.body.scrollHeight : 0);
            window.parent.postMessage({ type: 'statalog_ready', height: H }, '*');
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', signalReady);
        } else {
            signalReady();
        }
        return; // skip all tracking
    }
    // ─────────────────────────────────────────────────────────────────────────

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
    var siteId = scriptTag.getAttribute('data-site-id')
        || (src.match(/\/js\/t\/([A-Za-z0-9_-]+)\.js/) || [])[1]
        || (src.match(/[?&]id=([^&]+)/) || [])[1];
    if (!siteId) return;

    var endpoint = src.replace(/\/js\/(tracker\.js|t\/[^/?#]+\.js).*$/, '/api/collect');
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
            var t = performance.timing;
            var safe = function(a, b) { return (a > 0 && b > 0 && a >= b) ? a - b : 0; };
            payload.load_time            = safe(t.loadEventEnd,            t.navigationStart);
            payload.network_time         = safe(t.responseStart,           t.fetchStart);
            payload.server_time          = safe(t.responseStart,           t.requestStart);
            payload.transfer_time        = safe(t.responseEnd,             t.responseStart);
            payload.dom_processing_time  = safe(t.domComplete,             t.domInteractive);
            payload.dom_completion_time  = safe(t.domContentLoadedEventEnd,t.navigationStart);
            payload.on_load_time         = safe(t.loadEventEnd,            t.loadEventStart);
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
