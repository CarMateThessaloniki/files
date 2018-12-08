=== BP My Home ===
Contributors: imath
Donate link: http://imathi.eu/donations/
Tags: BuddyPress, widget, home, dashboard, members
Requires at least: 3.8
Tested up to: 3.8
License: GNU/GPL 2
Stable tag: 2.1

BP My Home makes it possible to add moveable and collapsible widgets to BuddyPress Member's home.

== Description ==

BP My Home requires <a href="http://buddypress.org/">BuddyPress 1.7+</a> and adds to it the ability to add moveable and collapsible custom widgets to Member's home (siteurl/members/name_of_user).
Logged in users can see their Home and choose in their settings area the widgets they want to use. They can also set their Home to be the homepage of the website.

4 widgets comes with this plugin (My Feeds, My Notepad, My Comments, My Bookmarks ). Since version 2.0 of the plugin, you can also add regular WordPress widgets as BP My Home now uses the built in WordPress way of managing widgets.

To set the widgets, the administrator needs to go to the Appearance / Widgets area of his backend to drag and drop the BPMH widgets (or others) into one of the two BPMH available sidebars. In the Settings of the plugin, he can also customize the names and slugs of the plugin.

This plugin is available in French and English.

http://vimeo.com/68876253

== Installation ==

You can download and install BP My Home using the built in WordPress plugin installer. If you download BP My Home manually, make sure it is uploaded to "/wp-content/plugins/bp-my-home/".

Activate BP My Home in the "Plugins" admin panel using the "Network Activate" (or "Activate" if you are not running a network) link.

== Frequently Asked Questions ==

= Is BP My Home BuddyPress 1.7 ready ? =

Yes, and it works fine in 1.9 beta 2 !

= Is it possible to add a WordPress sidebar widget ? =

Yes, simply drag it into one of the two BPMH sidebars.

= What are becoming the BPMH widgets i've built for the previous version of BP My Home in version 2.0 ? =

It's important to note that they won't be loaded anymore. All the data they've created in usermetas will still be there, and you can easily transform your old BPMH widget in a new one by using the <a href="https://gist.github.com/imath/5840625">widget skeleton</a> i've put on github.

= If you have more questions =

Ask it <a href="http://imathi.eu/tag/bp-my-home/">here</a>

== Screenshots ==

1. Example of a user's page.
2. The BPMH sidebars in WordPress Widgets administration

== Changelog ==

= 2.1 =
* adapts the (admin) widgets.js for WordPress 3.8
* fixes the bug about subdomain multisite config and the user setting that allows to make his profile page the home page of the site.
* fixes a bug when Simple pie doesn't manage to fetch a feed, by stopping the process instead of causing a fatal error
* now not only blogs the user is member of are available in the widget comment select box, but all blogs.

= 2.0 =
* BuddyPress 1.7+ ready
* now uses WordPress built in widgets management
* customizable names and slugs
* now uses the WordPress built in jquery-ui-sortable javascript 

= 1.2.2 =
* BP My Home will run on BuddyPress 1.2.9+ and <b>1.5</b> !
* BP My Home is now the default component of the 'siteurl/members/user' area
* fixing the rss widget bug when using the select box to change feed

= 1.2.1 =
* fixing the bug when viewing other member area (BP My Home is no more the default component of 'siteurl/members/user' area)

= 1.2 =
* new widget to display the latest posts of the blogs
* BP My Home is now the default component of the 'siteurl/members/user' area
* It's now possible to use this tag &lt;?php if(function_exists('the_bpmh_bkmks_tag')) the_bpmh_bkmks_tag() ;?&gt; to display the "Add to my bookmarks" link on page or post.
* Widget developers can add translations to widgets using the hook add_action( 'load_widget_language_files', 'your_custom_get_locale_function' ); (check bpmh_example for more infos)
* I also added 4 hooks so you can easily add content above or under the user widgets or above or under the user settings :
	* add_action ('bp_my_home_before_widgets', 'your_function_to_add_content_above_widgets'); 
	* add_action ('bp_my_home_after_widgets', 'your_function_to_add_content_under_widgets'); 
	* add_action ('bp_my_home_before_widgets_setting', 'your_function_to_add_content_above_settings'); 
	* add_action ('bp_my_home_after_widgets_setting', 'your_function_to_add_content_under_settings');

= 1.1.1 =
* fixes php warning message bug
* fixes add to my rss widget bug
* fixes notepad widget special char bug
* new widget added : My Comments

= 1.1 =
* From the BPMH Manager submenu (in the WordPress backend - BuddyPress menu), you can activate the option to display a subscribe tooltip for your rss feed to allow members to add them in their widget My Feeds
* From the BPMH Manager submenu, you can activate the option to automatically add a link in the blog(s) posts or pages in order to let your members add them to their widget My Bookmarks
* it is now possible to directly upload a zip archive of a widget from the BPMH Manager submenu
* new widget added : My Notepad

= 1.0 =
* Plugin birth..

== Upgrade Notice ==

= 2.1 =
requires at least WordPress 3.8 & BuddyPress 1.8.1

= 2.0 =
This is a major change! Previous BPMH widgets won't work as this new version now uses WordPress built in widgets management. The good new is that you'll be able to easily add WordPress widgets. <b>This version requires at least BuddyPress 1.7</b>

= 1.2.2 =
Before upgrading, you can back up the widgets folder up (wp-content/uploads/bpmh-widgets). After upgrade, you will have to upgrade the widgtes from the BPMH Manager

= 1.2.1 =
if you upgrade from 1.1.1 or lower versions :
Very Important ! Before upgrading, make sure to back up the wp-content/plugins/bp-my-home/widgets folder if you uploaded your own widgets there. After the upgrade, you will be able to put the widgets you built into the wp-content/uploads/bpmh-widgets directory.

= 1.2 =
Very Important ! Before upgrading, make sure to back up the wp-content/plugins/bp-my-home/widgets folder if you uploaded your own widgets there. After the upgrade, you will be able to put the widgets you built into the wp-content/uploads/bpmh-widgets directory.

= 1.1.1 =
Very Important ! Before upgrading, make sure to back up the wp-content/plugins/bp-my-home/widgets folder if you uploaded your own widgets there.

= 1.1 =
Very Important ! Before upgrading, make sure to back up the wp-content/plugins/bp-my-home/widgets folder if you uploaded your own widgets there.

= 1.0 =
no upgrades, just a first install..