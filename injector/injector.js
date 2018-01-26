// Uglify using "npx uglify-js injector.js --output injector.min.js --compress --mangle"
(function(document, window, navigator) {
  var isIpad = /ipad.*?OS (?![1-8]_|X)/i; // from iOS 9
  var isIphone = /ip(hone|od).*?OS (?![1-8]_|X)/i; // from iOS 9
  var isAndroidMobile = /android (?![1-3]\.)(?!4\.[0-3]).* mobile/i; // from Android 4.4
  var isAndroidTablet = /android (?![1-3]\.)(?!4\.[0-3]).* (?!mobile)/i; // from Android 4.4

  window['wp-pwa'].ssr = window['wp-pwa'].ssr.replace(/\/$/g, '') + '/';
  window['wp-pwa'].static = window['wp-pwa'].static.replace(/\/$/g, '') + '/';

  var isMobile = function(ua) {
    return isIphone.test(ua) || isAndroidMobile.test(ua);
  };
  var isTablet = function(ua) {
    return isIpad.test(ua) || isAndroidTablet.test(ua);
  };

  var setCookie = function(name, value, minutes) {
    var expires = '';
    if (minutes) {
      var d = new Date();
      d.setTime(d.getTime() + minutes * 60 * 1000);
      expires = 'expires=' + d.toUTCString() + ';';
    }
    document.cookie = name + '=' + value + ';' + expires + 'path=/';
  };

  var readCookie = function(name) {
    var nameEQ = name + '=';
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  };

  var loadScript = function(options) {
    if (document.getElementById(options.id)) return;
    var ref = document.getElementsByTagName(options.tag)[0];
    var js = document.createElement(options.tag);
    js.id = options.id;
    js.src = options.src;
    ref.parentNode.insertBefore(js, ref);
  };

  var loadHtml = function(html) {
    var newDoc = document.open('text/html', 'replace');
    newDoc.write(html);
    newDoc.close();
    document.body.scrollTop = 0;
  };

  if (readCookie('wppwaClassicVersion')) {
    var options = {
      tag: 'script',
      id: 'wppwaClassic',
      src: window['wp-pwa'].ssr + 'dynamic/wp-org-connection-app-extension-worona/go-back-to-wppwa.js',
    };
    loadScript(options);
  } else if (!readCookie('wppwaInjectorFailed') && navigator && isMobile(navigator.userAgent)) {
    window.stop();
    // This is the escaped html from loader.html.
    var html =
      '%3Chead%3E%0A%20%20%20%20%20%20%3Cstyle%3E%0A%20%20%20%20%20%20%20%20@keyframes%20progress%20%7B%0A%20%20%20%20%20%20%20%20%20%20from%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20width%3A%200%25%3B%0A%20%20%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20%20%20to%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20width%3A%2080%25%3B%0A%20%20%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%3C/style%3E%0A%20%20%20%20%3C/head%3E%0A%0A%20%20%20%20%3Cbody%20style%3D%22height%3A100%25%3Bbackground%3A%23FDFDFD%3Bdisplay%3Aflex%3Bjustify-content%3Acenter%3Balign-items%3Acenter%3Bmargin%3A0%3B%22%3E%0A%20%20%20%20%20%20%3Cdiv%20style%3D%22animation%3A6s%20ease-out%201s%201%20forwards%20progress%3Bheight%3A1px%3Bbackground%3A%23000%3B%22%3E%3C/div%3E%0A%20%20%20%20%3C/body%3E';
    document.write(unescape(html));

    var query = '?siteId=' + window['wp-pwa'].siteId
      + '&static=' + encodeURIComponent(window['wp-pwa'].static)
      + '&env=' + window['wp-pwa'].env
      + '&initialUrl=' + encodeURIComponent(window['wp-pwa'].initialUrl || window.location.href);
    if (window['wp-pwa'].listType) query += '&listType=' + window['wp-pwa'].listType;
    if (window['wp-pwa'].listId) query += '&listId=' + window['wp-pwa'].listId;
    if (window['wp-pwa'].page) query += '&page=' + window['wp-pwa'].page;
    if (window['wp-pwa'].singleType) query += '&singleType=' + window['wp-pwa'].singleType;
    if (window['wp-pwa'].singleId) query += '&singleId=' + window['wp-pwa'].singleId;
    if (window['wp-pwa'].dev === false) query += '&dev=false';
    if (window['wp-pwa'].dev === true) query += '&dev=true';

    var loadWorona = function() {
      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            loadHtml(xhr.responseText);
          } else {
            var rollbarXhr = new XMLHttpRequest();
            rollbarXhr.open('POST', 'https://api.rollbar.com/api/1/item/', true);
            rollbarXhr.send(
              JSON.stringify({
                access_token: 'd64fbebfade643439dad144ccb8c3635',
                data: {
                  environment: 'injector',
                  platform: 'browser',
                  body: {
                    message: {
                      body: 'Error loading the injector on: ' + window.location.href,
                      error: xhr.statusText,
                    },
                  },
                },
              })
            );
            console.error('Error loading the injector on: ' + window.location.href, xhr.statusText);
            setCookie('wppwaInjectorFailed', 'true', 1);
            window.location.reload(true);
          }
        }
      };
      xhr.open('GET', window['wp-pwa'].ssr + query, true);
      xhr.send();
    };
    loadWorona();
  }
})(document, window, navigator);
