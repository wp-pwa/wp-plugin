=== WordPress PWA ===
Contributors: poliuk, luisherranz, rmartinezduque, orballo, davidarenas
Tags: html5, pwa, webapp, app, progressive web app
Author URI: https://frontity.com/?utm_source=plugin-repository&utm_medium=link&utm_campaign=plugin-description
Requires at least: 4.4
Tested up to: 4.9.6
Stable tag: 1.4.10
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

The simplest way to create Progressive Web Apps with WordPress. No coding skills required. Free forever.

== Description ==

WordPress PWA (or **[Frontity](https://frontity.com/?utm_source=plugin-repository&utm_medium=link&utm_campaign=plugin-description)**) is a plugin that turns any publisher's site into a Progressive Web App (PWA).

Our PWAs use the mobile web to deliver user experiences that are fast and highly engaging. They support Google AMP, can be accessed instantly from any device, and are frictionless to use.

We aim to help publishers boost their traffic and maximize their ad revenue by making use of the latest mobile technologies.

<strong>IMPORTANT</strong>
The access to our platform is currently limited to publishers / WordPress blogs with certain volume of traffic. You can request a demo at [frontity.com/#request-demo](https://frontity.com/?utm_source=plugin-repository&utm_medium=link&utm_campaign=plugin-description#request-demo).

== Installation ==

**From your WordPress Control Panel:**

- Go to Plugins > Add new, and search for “WordPress PWA”.
- Click the “Install now” button.
- Once the plugin has been installed, click “Activate Plugin”.

**From the WordPress Plugin Directory:**

- Click the “Download” button to get a .zip file.
- Once the download is completed, go to your WordPress Control Panel > Plugins > Add new.
- Upload the previous .zip file.
- Activate the plugin after its installation.

Once the WordPress PWA plugin is installed and activated, follow our [instructions to configure it](https://docs.wp-pwa.com/wp-pwa-plugin-installation.html).

In order to get a site ID you need to be a Frontity customer first. If you are not, you can request a demo [here](https://frontity.com/?utm_source=plugin-repository&utm_medium=link&utm_campaign=plugin-description#request-demo). However, please note that the access to our platform is limited right now to blogs with certain volume of traffic. You can learn more at [https://frontity.com/get-help](https://frontity.com/get-help?utm_source=plugin-repository&utm_medium=link&utm_campaign=plugin-description).

== Frequently Asked Questions ==

= How do I get a Site ID? =

You have to be a Frontity user. If you are interested in Frontity, you can request a demo at [frontity.com/#request-demo](https://frontity.com/?utm_source=plugin-repository&utm_medium=link&utm_campaign=plugin-description#request-demo)

= Can I see a demo? =

Sure! Visit [blog.frontity.com](https://blog.frontity.com/?utm_source=plugin-repository&utm_medium=link&utm_campaign=plugin-description) from a mobile browser. You can also take a look at this **[video](https://youtu.be/W9X2R-6lhEc)**

== Changelog ==
= 1.4.10 =
* Decode url images before searching in DB

= 1.4.9 =
* Don't require simple_html_dom if it exists

= 1.4.8 =
* Change get_attachment_id function

= 1.4.7 =
* If it is a static home page use get_query_var('page')

= 1.4.6 =
* Add per_page to wp:contentmedia

= 1.4.5 =
* Fix bug on latest version of Chrome where document.body doesn't exist after document.write

= 1.4.4 =
* Do the purify first, then add the img ids

= 1.4.3 =
* Remove unnecessary files of htmlpurifier

= 1.4.2 =
* Use purifier in all cpt
* Add ids to all images in content, then embed the media objects in the REST API
* Remove support for pages > 1 and custom taxomonies for the moment

= 1.4.1 =
* Add filter to add cpt to latest

= 1.4.0 =
* Migrate to new type and id

= 1.3.2 =
* Don't use Htmlpurifier if it returns an empty string (happens in some servers)
* Fix bug removing source tags
* Filter font tag
* Improve injector error message

= 1.3.1 =
* Go back to initialUrl
* Support from Chrome >= 40 on Android
* Add 10 sec timeout to injector
* Don't send rollbar error on refresh
* White list data-attachement-ids on Htmlpurifier

= 1.3.0 =
* Added latest special taxonomy
* Added htmlpurifier
* Switch to initialUri

= 1.2.4 =
* Add url to site-info endpoint

= 1.2.3 =
* Change force to pwa and add dev query
* Add dev, amp and ampUrl queries to amphtml tag

= 1.2.2 =
* Remove host from AMP url

= 1.2.1 =
* Remove query and system from the queries
* Fix bug on user agent regexp

= 1.2.0 =
* Don't inject attachments
* Add system and device to the query
* Add AMP enabler

= 1.1.1 =
* Change ssr and static queries to ssrUrl and staticUrl (static was causing problems with WP)
* Encode urls (intialUrl and static) before sending them to the server

= 1.1.0 =
* Add excludes (use regexp to exclude urls from using WP PWA)
* Send the current url to the WP PWA server to retrieve <head> fields
* Add media ids to galleries

= 1.0.13 =
* Remove html from unminified injector

= 1.0.12 =
* Add version to admin javascript to avoid browser caching

= 1.0.11 =
* Add option to force FrontPage to retrieve Latest posts instead of page

= 1.0.10 =
* Add break option to debug injector code

= 1.0.9 =
* Add dev (development) variable to query

= 1.0.8 =
* Removed unused code from older versions of the plugin

= 1.0.7 =
* Improved plugin compatibility with old PHP versions

= 1.0.6 =
* Added "On/Off" option

= 1.0.5 =
* Don't send search and dates to PWA.

= 1.0.4 =
* New url for go-back javascript.

= 1.0.3 =
* /wp-pwa/site-info/ endpoint available again.

= 1.0.2 =
* SiteId is not autogenerated on plugin activation anymore.

= 1.0.1 =
* Added "env" variable to advanced settings.

= 1.0.0 =
* First release of the plugin.
