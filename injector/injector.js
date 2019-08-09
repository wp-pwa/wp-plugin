// Uglify using "npx uglify-js injector.js --output injector.min.js --compress --mangle"
(function(document, window, navigator) {
  var cookieExpire = 3; // seconds

  var isIphone = /ip(hone|od).*?OS (?![1-8]_|X)/i; // from iOS 9
  var isIpad = /ipad.*?OS (?![1-8]_|X)/i; // from iOS 9
  var isAndroidMobile = /android.+chrome\/(?![123]\d\.)(.+mobile)/i; // from Chrome 40
  var isAndroidTablet = /android.+chrome\/(?![123]\d\.)(?!.+mobile)/i; // from Chrome 40

  window["wp-pwa"].ssr_server =
    window["wp-pwa"].ssr_server.replace(/\/$/g, "") + "/";
  window["wp-pwa"].static_server =
    window["wp-pwa"].static_server.replace(/\/$/g, "") + "/";

  var isMobile = function(ua) {
    return isIphone.test(ua) || isAndroidMobile.test(ua);
  };
  var isTablet = function(ua) {
    return isIpad.test(ua) || isAndroidTablet.test(ua);
  };

  var setCookie = function(name, value, seconds) {
    var expires = "";
    if (seconds) {
      var d = new Date();
      d.setTime(d.getTime() + seconds * 1000);
      expires = "expires=" + d.toUTCString() + ";";
    }
    document.cookie = name + "=" + value + ";" + expires + "path=/";
  };

  var readCookie = function(name) {
    var nameEQ = name + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(";");
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == " ") c = c.substring(1, c.length);
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
    var openDoc = document.open();
    openDoc.write(html);

    // Remove old content (wow. such magic 🐶)
    openDoc.write('<plaintext id="before-frontity" style="display:none">');
    openDoc.body.removeChild(openDoc.getElementById("before-frontity"));

    openDoc.close();
  };

  if (readCookie("wppwaClassicVersion")) {
    var options = {
      tag: "script",
      id: "wppwaClassic",
      src:
        window["wp-pwa"].ssr_server +
        "dynamic/wp-org-connection-app-extension-worona/go-back-to-wppwa.js",
    };
    loadScript(options);
  } else if (
    !readCookie("wppwaInjectorFailed") &&
    navigator &&
    isMobile(navigator.userAgent)
  ) {
    var query =
      "?siteId=" +
      window["wp-pwa"].site_id +
      "&type=" +
      window["wp-pwa"].type +
      "&id=" +
      window["wp-pwa"].id +
      "&static=" +
      encodeURIComponent(window["wp-pwa"].static_server) +
      "&perPage=" +
      window["wp-pwa"].per_page +
      "&device=" +
      (isTablet(navigator && navigator.userAgent) ? "tablet" : "mobile") +
      "&initialUrl=" +
      encodeURIComponent(
        window["wp-pwa"].initial_url ||
          window.location.origin + window.location.pathname
      );
    if (window["wp-pwa"].page) query += "&page=" + window["wp-pwa"].page;

    var injectorFailed = function(_xhr, error) {
      var rollbarXhr = new XMLHttpRequest();
      rollbarXhr.open("POST", "https://api.rollbar.com/api/1/item/", true);
      rollbarXhr.send(
        JSON.stringify({
          access_token: "d64fbebfade643439dad144ccb8c3635",
          data: {
            environment: "injector",
            platform: "browser",
            body: {
              message: {
                body: 'Injector "' + error + '" on: ' + window.location.href,
                error: error,
              },
            },
          },
        })
      );
      console.error('Injector "' + error + '" on: ' + window.location.href);
    };

    var loadWorona = function() {
      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            loadHtml(xhr.responseText);
          } else {
            setCookie("wppwaInjectorFailed", "true", cookieExpire);
            window.location.reload(true);
          }
        }
      };
      xhr.onerror = function() {
        injectorFailed(xhr, "network error");
        setCookie("wppwaInjectorFailed", "true", cookieExpire);
        window.location.reload(true);
      };
      xhr.open("GET", window["wp-pwa"].ssr_server + query, false);
      xhr.send();
    };
    loadWorona();
  }
})(document, window, navigator);
