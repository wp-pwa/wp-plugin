// Uglify using "npx uglify-js injector.js --output injector.min.js --compress --mangle"
(function(document, window, navigator) {
  var cookieExpire = 3; // seconds
  var timeout = 30000 // miliseconds

  var isIphone = /ip(hone|od).*?OS (?![1-8]_|X)/i; // from iOS 9
  var isIpad = /ipad.*?OS (?![1-8]_|X)/i; // from iOS 9
  var isAndroidMobile = /android.+chrome\/(?![123]\d\.)(.+mobile)/i; // from Chrome 40
  var isAndroidTablet = /android.+chrome\/(?![123]\d\.)(?!.+mobile)/i; // from Chrome 40

  window['wp-pwa'].ssr = window['wp-pwa'].ssr.replace(/\/$/g, '') + '/';
  window['wp-pwa'].static = window['wp-pwa'].static.replace(/\/$/g, '') + '/';

  var isMobile = function(ua) {
    return isIphone.test(ua) || isAndroidMobile.test(ua);
  };
  var isTablet = function(ua) {
    return isIpad.test(ua) || isAndroidTablet.test(ua);
  };

  var setCookie = function(name, value, seconds) {
    var expires = '';
    if (seconds) {
      var d = new Date();
      d.setTime(d.getTime() + seconds * 1000);
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
  };

  if (readCookie('wppwaClassicVersion')) {
    var options = {
      tag: 'script',
      id: 'wppwaClassic',
      src: window['wp-pwa'].ssr + 'dynamic/wp-org-connection-app-extension-worona/go-back-to-wppwa.js',
    };
    loadScript(options);
  } else if (!readCookie('wppwaInjectorFailed') && navigator && (isMobile(navigator.userAgent))) {
    window.stop();
    var html =
    '%3Chead%3E%0A%20%20%20%20%20%20%3Cstyle%3E%0A%20%20%20%20%20%20%20%20@keyframes%20progress%20%7B%0A%20%20%20%20%20%20%20%20%20%20from%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20width%3A%200%25%3B%0A%20%20%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20%20%20to%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20width%3A%2080%25%3B%0A%20%20%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%3C/style%3E%0A%20%20%20%20%3C/head%3E%0A%0A%20%20%20%20%3Cbody%20style%3D%22height%3A100%25%3Bbackground%3A%23FDFDFD%3Bdisplay%3Aflex%3Bjustify-content%3Acenter%3Balign-items%3Acenter%3Bmargin%3A0%3B%22%3E%0A%20%20%20%20%20%20%3Cdiv%20style%3D%22animation%3A6s%20ease-out%201s%201%20forwards%20progress%3Bheight%3A1px%3Bbackground%3A%23000%3B%22%3E%3C/div%3E%0A%20%20%20%20%3C/body%3E';
    document.write(unescape(html));

    var query = '?siteId=' + window['wp-pwa'].siteId
      + '&type=' + window['wp-pwa'].type
      + '&id=' + window['wp-pwa'].id
      + '&dev=' + window['wp-pwa'].dev
      + '&static=' + encodeURIComponent(window['wp-pwa'].static)
      + '&env=' + window['wp-pwa'].env
      + '&perPage=' + window['wp-pwa'].perPage
      + '&device=' + (isTablet((navigator && navigator.userAgent)) ? 'tablet' : 'mobile')
      + '&initialUrl=' + encodeURIComponent(window['wp-pwa'].initialUrl || window.location.origin + window.location.pathname);
    if (window['wp-pwa'].page) query += '&page=' + window['wp-pwa'].page;

    var injectorFailed = function(xhr, error) {
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
                body: 'Injector "' + error + '" on: ' + window.location.href,
                error: error
              },
            },
          },
        })
      );
      console.error('Injector "' + error + '" on: ' + window.location.href);
    };

    var loadWorona = function() {
      var xhr = new XMLHttpRequest();
      xhr.timeout = timeout;
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            loadHtml(xhr.responseText);
          }
          else {
            setCookie('wppwaInjectorFailed', 'true', cookieExpire);
            window.location.reload(true);
          }
        }
      };
      xhr.ontimeout = function () {
        injectorFailed(xhr, 'timeout');
        setCookie('wppwaInjectorFailed', 'true', cookieExpire);
        window.location.reload(true);
      };
      xhr.onerror = function() {
        injectorFailed(xhr, 'network error');
        setCookie('wppwaInjectorFailed', 'true', cookieExpire);
        window.location.reload(true);
      };
      xhr.open('GET', window['wp-pwa'].ssr + query, true);
      xhr.send();
    };
    loadWorona();
  }
})(document, window, navigator);
