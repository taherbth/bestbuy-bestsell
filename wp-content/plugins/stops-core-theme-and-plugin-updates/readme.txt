=== Easy Updates Manager ===
Contributors: kidsguide, ronalfy, pixolin, roary86
Tags: updates manager, easy updates manager, disable updates manager, disable updates, update control, plugin updates, theme updates, core updates, automatic updates, multisite
Requires at least: 4.3
Tested up to: 4.4
Stable tag: 5.4.2
License: GPLv2 or later

Manage all your WordPress updates, including individual plugin/theme updates, automatic updates, and loads more. Also works with WordPress Multisite.

== Description ==
Easy Updates Manager is a light yet powerful plugin which enables you to manage all types of updates on your single site install or in WordPress Multisite. With loads of settings making endless possibilities for configuration, Easy Updates Manager is an obvious choice for anyone wanting to take control of their websites updates.

[youtube https://www.youtube.com/watch?v=MmNrNAkCI0g&list=PL7YF6bIhLLz0HzndENZZAdoh2nJBefTeA]

= Features Include =
<ul>
<li>All Updates - This setting quite easily just overrides all other settings and disables everything.</li>
<li>WordPress Core Updates- This setting is used to toggle on and off the WordPress core updates.</li>
<li>Plugin Updates - This setting is used to disable all plugin updates on your website.</li>
<li>Theme Updates - This setting is used to disable all theme updates on your website.</li>
<li>Major Releases - This setting toggles whether or not you want the major WordPress core versions to automatically update themselves.</li>
<li>Minor Releases - This setting toggles whether or not you want the minor WordPress core versions to automatically update themselves.</li>
<li>Development Updates - This setting toggles whether or not you want the bleeding edge version of WordPress to automatically update itself.</li>
<li>Plugin Updates - This setting can either automatically update all your plugins, or automatically update any select plugins you want.</li>
<li>Theme Updates - This setting can either automatically update all your themes, or automatically update any select themes you want.</li>
<li>Translation Updates - This setting can unable automatic updating for translation updates.</li>
<li>Core Update E-mails - This setting disables the core update e-mails.</li>
<li>Browser Nag - This setting removes the WordPress browser nag which appears when you are using an older browser.</li>
<li>WordPress Version in Footer - This setting will remove the WordPress version in the admin footer on your website.</li>
<li>Disable plugin updates and automatic updates individually by selecting which plugins on a table. Also able to bulk select plugins.</li>
<li>Disable theme updates and automatic updates individually by selecting which themes on a table. Also able to bulk select themes.</li>
<li>The ability to block users from configuring the settings.</li>
<li>The ability to select which users can still see and perform updates.</li>
<li>The ability to update some third party plugins.</li>
</ul>

For more information on how to use Easy Updates Manager, check out our <a href="https://github.com/easy-updates-manager/easy-updates-manager/wiki">wiki</a>.

If you want to contribute to the translation, please go to https://translate.wordpress.org/, select your language and search for plugin Easy Updates Manager. We appreciate all the translation help we can get.

== Screenshots ==
1. Single site install settings page location
2. Multisite install settings page location
3. Dashboard tab full
4. Dashboard tab small
5. Global settings in General tab
6. Automatic Update settings in General tab
7. Notification and Miscellaneous settings in Global tab
8. Enable/Disable updates and automatic updates individually for plugins
9. Enable/Disable updates and automatic updates individually for themes
10. Advanced options tab
11. Help tab

== Installation ==
<strong>Installing Easy Updates Manager in your WordPress Dashboard</strong> (recommended)

1. You can download Easy Updates Manager by going into your 'Plugins' section and selecting add a new plugin
2. Search for the plugin by typing 'Easy Updates Manager' into the search bar
3. Install and activate the plugin by pushing the 'Install' button and then the 'activate plugin' link
4. Configure the plugin by going to the 'Updates Options' section in your admin area under the Dashboard

<strong>Installing Easy Updates Manager through FTP</strong>

1. Upload the Easy Updates Manager folder to the '/wp-content/plugins/' directory (make sure it is unzipped)
2. Activate the Easy Updates Manager plugin through the 'Plugins' menu in WordPress
3. Configure the plugin by going to the 'Update Options' section in your admin area under the Dashboard

Activate the plugin after installation.  If you are on Multisite, the plugin must be network-activated.

== Frequently Asked Questions ==
= Do you support older WordPress versions? =

Version 5.0.x should support WP versions 4.0 - 4.2.x. Version 4.7.0 should support WP versions 2.3 - 4.0.

Since WordPress is constantly changing, this plugin will always strive to support the latest version of WordPress. As there are many security vulnerabilities reported, please always keep WordPress as up-to-date as possible.

Unfortunately, we do not support older versions of WordPress or the non-current release of this plugin.

= How do I change the WordPress update notification e-mail? =

There's some documentation about changing the notification e-mail here: http://www.iwebcontrol.co.uk/2013/10/change-wordpress-auto-update-email-address/

A plugin was also created to change the background updates email address: https://wordpress.org/plugins/background-update-notification-email-address/

= Automatic Updates =

Check out our video on how the automatic updating works in WordPress.
[youtube https://www.youtube.com/watch?v=V8AWwBc6GrU]

= Additional Information and FAQ =

For additional information and FAQs for Easy Updates Manager check out our <a href="https://github.com/easy-updates-manager/easy-updates-manager/wiki">wiki</a>.

== Changelog ==

= 5.4.2 =
Released 2015-11-25

* Removed e-mail options that didn't make it into WordPress 4.4
* Fixed GoDaddy issue where plugin wasn't showing up

= 5.4.1 =
Released 2015-10-31

* Fixing styles being used elsewhere besides the EUM dashboard

= 5.4.0 =
Released 2015-10-30

* Major Dashboard Update (Props roary86 and ronalfy)
* List Table Changes (Props ramiy)
* WordPress 4.4 preparation

= 5.3.2 =
Released 2015-10-13

* Fixed translation errors.
* Welcomed Bego Mario Garde (pixolin) as a official contributor.

= 5.3.1 =
Released 2015-09-27

* Fixing automatic updates dashboard widget.

= 5.3.0 =
Released 2015-09-27

* New Dashboard View
* Support for WordPress 4.4 Email Notifications

= 5.2.0 =
Released 2015-09-19

* Added Force check in the Advanced Tab.
* Better support for third-party plugins.
* Updating the filter priority for better update experience with third-party plugins.

= 5.1.1 =
Released 2015-08-24

* Fixed internationalization in plugin files.
* Added German translation.
* Updated internal plugin documentation.
* Fixing errant status messages.

= 5.1.0 =
Released 2015-08-13

* WordPress 4.3 tested and is now the minimum supported version.
* Added default option to plugin/theme automatic updates.
* Updated internal HTML to be WordPress 4.3 compatible.
* Updated internal list tables to be WordPress 4.3 compatible.
* Moved menus in multisite to Dashboard for WordPress 4.3 compatibility.

= 5.0.0 =
Updated 2015-04-23 to ensure WordPress 4.2 compatibility Released 2015-03-24

In version 5.0.0 we completely re-wrote the plugin to offer a faster and more secure experience. You will also notice that we added lots more settings to cover almost every aspect of managing updates.

* Complete re-write of Disable Updates Manager with new user interface.
* Now compatible with WordPress Multisite installations.
* New name: Easy Updates Manager
* New contributor: <a href="https://profiles.wordpress.org/ronalfy">ronalfy</a>

== Upgrade Notice ==

= 5.4.2 =
Fixing GoDaddy permissions issue. Removing options that didn't make it into WordPress 4.4.

= 5.4.1 =
Fixing styling issues where EUM styles were being used elsewhere.

= 5.4.0 =
Major dashboard overhaul and WordPress 4.4 preparation

= 5.3.2 =
Fixed translation errors. 

= 5.3.1 =
Fixing automatic updates in dashboard view.

= 5.3.0 =
New Dashboard View. Support for WordPress 4.4 Email Notifications.

= 5.2.0 =
Force update button available. Better support for third-party solutions.

= 5.1.1 =
Fixing internationalization issues and adding German translation.

= 5.1.0 =
Only upgrade if using WordPress 4.3. Many changes to ensure WordPress 4.3 compatibility.

= 5.0.0 =
Major plugin rewrite and update. Updating will port over your old options. We recommend you perform a backup of your site before updating. Now you can configure just about any aspect of WordPress updates.
