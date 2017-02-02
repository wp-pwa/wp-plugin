function validateEmail(email) {
  var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(email);
}

function getIframeUrl(e) {
  var url;

  var props = "";
  props += "&wp-version=" + jQuery('input[name=wp-version]').val();
  props += "&wp-url=" + jQuery('input[name=wp-url]').val();
  props += "&site-name=" + jQuery('input[name=site-name]').val();
  props += "&user-name=" + jQuery('input[name=user-name]').val();
  props += "&email=" + jQuery('input[name=email]').val();
  props += "&wp-lan=" + jQuery('input[name=wp-lan]').val();
  props += "&worona-version=" + jQuery('input[name=worona-version]').val();
  props += "&worona-siteid=" + jQuery('input[name=worona-siteid]').val();

  if (e == "change-site-id") {
      props += "&worona-siteid-new=" +jQuery('input#worona-siteid').val();
  }

  url = "https://plugin.worona.org/?event=" + e + props;

  return url;
}

jQuery(document).on('ready', function () {
    //disabling # links
    jQuery('a[href^="#"]').click(function(e) {
      e.preventDefault();
    });

    //Show "Insert siteid form"
    jQuery('.open-change-siteid').on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      jQuery('#lateral-change-siteid').show();
    });

    jQuery('.close-change-siteid').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      jQuery('#lateral-change-siteid').hide();
      jQuery('#lateral-error-siteid').hide();
    });

    jQuery('.close-error-siteid').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      jQuery('#lateral-error-siteid').hide();
    });

    //Create App via AJAX
    jQuery('#sync-with-worona').on('click', function (e) {
      jQuery('#sync-with-worona').addClass('is-loading');
      e.preventDefault();
      e.stopPropagation();

      var name = jQuery('#param-name').val();
      var email = jQuery('#param-email').val();
      var siteURL = jQuery('#param-siteURL').val();
      var siteName = jQuery('#param-siteName').val();
      var siteId = jQuery('#param-siteId').val();

      var registerURL = "https://dashboard.worona.org/register";

      registerURL += "?name=" + name;
      registerURL += "&email=" + email;
      registerURL += "&siteURL=" + siteURL;
      registerURL += "&siteName=" + siteName;
      registerURL += "&siteId=" + siteId;

      var win = window.open(registerURL, '_blank');
      win.focus();

      jQuery.ajax({
          url: ajaxurl,
          method: "POST",
          data: {
              action: 'sync_with_worona',
          },
          success: function (response) {
            if (response.hasOwnProperty('status') && response.status == 'ok' ) {
              jQuery('#label-create-buttons').toggle();
              jQuery('#label-created').toggle();
              jQuery('progress')[0].value = 100;
              jQuery('#step-message').text('You are on step 4/4');
              jQuery('#worona-siteid-lateral').show();
              jQuery('span#worona-siteid-span').text(response.siteId);
              jQuery('input#worona-siteid').val(response.siteId);

              jQuery('#dashboard-button').removeClass('disabled');
              jQuery('#dashboard-button').addClass('button-primary button-hero');

              var siteid = jQuery('#worona-siteid-span').text();
              var url = "https://dashboard.worona.org/" + "site/" + siteid;
              jQuery('#dashboard-button').on('click', function(e){window.open(url)});
            }
          },
          error: function () {

          }
      });
    });


    //Change App ID via ajax
    jQuery('#change-siteid').on('click', function(e) {
      jQuery('#change-siteid').addClass('is-loading');
      e.preventDefault();
      e.stopPropagation();
      var id = jQuery('input#worona-siteid').val();

      if ( id.length !=17 || id.includes(' ')){
        jQuery('#lateral-error-siteid').show();
        jQuery('#siteid-error-message').text("Invalid App ID");
        jQuery('#change-siteid').removeClass('is-loading');
      } else {
        jQuery.ajax({
          url: ajaxurl,
          method: "POST",
          data: {
              action: 'worona_change_siteid',
              siteid: jQuery('input#worona-siteid').val()
          },
          success: function (response) {
            if (response.hasOwnProperty('status') && response.status == 'ok' ) {

              jQuery('#gtm-iframe').attr('src',getIframeUrl('change-site-id'));

              jQuery('#change-siteid').removeClass('is-loading');
              jQuery('#lateral-error-siteid').hide();
              jQuery('#lateral-change-siteid').hide();
              jQuery('#label-create-buttons').hide(); //they can be hidden already
              jQuery('#label-created').show(); //it can be displayed already
              jQuery('progress')[0].value = 100;
              jQuery('#step-message').text('You are on step 4/4');
              jQuery('#worona-siteid-lateral').show();
              jQuery('span#worona-siteid-span').text(jQuery('input#worona-siteid').val());

              jQuery('#dashboard-button').removeClass('disabled');
              jQuery('#dashboard-button').addClass('button-primary button-hero');

              var siteid = jQuery('#worona-siteid-span').text();
              jQuery('#dashboard-button').on('click', function(e){window.open(url)});

              var dashboard_url = 'https://dashboard.worona.org/check-site/' + siteid;
              jQuery('#dashboard-button').prop('href',dashboard_url);

            } else if( response.hasOwnProperty('status') && response.status == 'error') {
              jQuery('#lateral-error-siteid').show();
              jQuery('#siteid-error-message').text(response.reason);
              jQuery('#change-siteid').removeClass('is-loading');
            }
          },
          error: function (response) {
            jQuery('#lateral-error-siteid').show();
            jQuery('#siteid-error-message').text("The Site ID couldn't be modified. Please try again.");
            jQuery('#change-siteid').removeClass('is-loading');
          }
        });
      }
    });

    jQuery('#checkbox-plugin-support').on('change',function() {
      jQuery('#checkbox-plugin-support').attr('disabled',true);


      if ( jQuery('#checkbox-plugin-support').prop('checked')) {
        //subscribe to plugin support
        if(jQuery('#gtm-iframe').length) {
          jQuery('#gtm-iframe').attr('src',getIframeUrl('plugin-support-subscribe'));
        } else {
          var iframe = '<iframe id="gtm-iframe" src="' + getIframeUrl('plugin-support-subscribe') + '" width="1" height="1"></iframe>';
          jQuery('.wrap').append(iframe);
        }

      } else {
        //unsubscribe from plugin support
        jQuery('#gtm-iframe').attr('src',getIframeUrl('plugin-support-unsubscribe'));
      }

      jQuery.ajax({
          url: ajaxurl,
          method: "POST",
          data: {
              action: 'plugin_support',
          },
          success: function (response) {
            if (response.hasOwnProperty('status') && response.status == 'ok' ) {
              jQuery('#checkbox-plugin-support').removeAttr('disabled');
            }
          },
          error: function () {

          }
      });

    });


});