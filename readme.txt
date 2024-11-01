=== Ultimate Post by Mail ===
Contributors: comwes
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XMTTFER2CM9PA
Tags: Ultimate post by mail, publish, post, emailpost, postie, post by mail, mail
Requires at least: 3.1.2
Tested up to: 4.3.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Replace and add security to the default post by mail included in WP.

== Description ==

The objective of this plugin is to offset some deficiencies of the WordPress post by mail features.
It would be useful for those who want to post on their blog by mail or those who want to allow people to publish post anonymously by just sending emails.

Major features in Ultimate Post by Mail includes:

* Mails from users are published under their author name. 
* Mails from unknown users are depending on your settings, published or saved in pending mode.
* Ability to specify category for the article sent by mail. To specify this, the mail subject have to fit this format `Category]] The title of the post`.
* Ability to set a default user, to be owner of mails sent by unregistered users. 
* Spam filter,
* All html tags are deleted, `<div>` tags are replaces by `<p>` tags.

== Installation ==

This section describes how to install this plugin and get it working.

1. Upload `ultimate-post-by-mail` directory (including all files within) to your plugin directory, this must be the `/wp-content/plugins/` directory by default.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure the plugin settings by visiting `Settings=>U Post by Mail`.
1. Configure server settings in order to make this work.
2. Configure posts settings for custom behavior.

== Frequently Asked Questions ==

= No pictures/attachements? =
Unfortunately not. This feature will is not yet implemented and but should be included in the upcoming release.

= Is this working with Gmail? =
Sorry for that, but currently, we have not find out the best default configuration for Gmail. 
But we are working on the way a process to allow fetching mails from Gmail. If you find out the way to do that, let us know.


== Changelog ==

= 2.0.0 =
*Release Date - 6th November, 2015*

* Renamed GERRYWORKS Post by Mail to Ultimate Post by Mail and Code rewriting.
* Feature: Added the ability to select the default author.
* Feature: Added the ability to select the default category.
* Feature: Added Outlook IMAP configuration.
* Bugfix: Check mail before saving configuration.
* Bugfix: Corrected the drag and drop issue.
* Deprecation: Removed mail video url detection support until full support in next releases.
* Deprecation: Removed embedded video support until full support in next releases.
* Deprecation: Removed Gmail default configuration support.


= 1.0 = 
*Release Date - 15th May, 2011*

* Known as GERRYWORKS Post by Mail 1.0

== Feedback ==

We are developing this plugin for you. 
If you discover a bug, you need a feature or have any idea, let us know by posting your suggestion on the plugin forum.