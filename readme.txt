=== GI-Media Library ===
Contributors: zishanj
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=HQ2DHNS7TQNZ8
Tags: html,table,data,media,library,e-learning,online education,course,audio,video,media library,course library
Requires at least: 3.6
Tested up to: 4.2.2
Stable tag: 3.0.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

GI-Media Library enables you to display your course/media library in tabular form without the effort of custom creating html tables & layout.

== Description ==

GI-Media Library is a WordPress plugin developed especially for institutions providing online education. With this plugin, it's easy to create your course/media library in a tabular form without any effort of custom building pages and layouts. You can organize it into a group (course) and subgroup (subjects), create combo items (chapters), create playlist section (sections) under that item and then add your links to course materials or media's under that section. You can create your own table with desired number of columns like topic, duration, files etc. It supports all type of libraries like audio, video, pdf, doc etc. As of version 3.0, this plugin uses WordPress built-in audio player:

*       HTML5: mp3, mp4 (AAC/H.264), ogg (Vorbis/Theora), webm (Vorbis/VP8)

Playing external videos (YouTube and Vimeo) are also supported. 

You can fully customize the layout by providing CSS stylesheet class and change the text direction from LTR to RTL, if you want to use Arabic, Persian, Urdu languages.

You can download User's Manual with complete step by step usage instructions from http://www.glareofislam.com/softwares/gimedialibrary.html

If you are looking for advanced feature with Student Registration and LMS (Learning Management System), then check our <a href="http://www.glareofislam.com/#gi-lms">GI-Learning Management System</a> which will work with GI-Media Library to provide you with complete Learning Management System.

**Note: As of version 3.0, PHP version of at least 5.4 is required otherwise the plugin will not work.**

Following are the complete list of features:

* Admin Section:
1. Add/Modify/Delete Groups and manage their CSS class and direction
1. Add/Modify/Delete Sub-Groups and manage their CSS class and direction
1. Link one Subgroup to multiple Groups
1. You can specify Main Text, right and left heading text that will appear on top of playlist page. Can assign CSS class and text direction
1. Add/Modify/Delete playlist table columns and manage their styles by CSS class and text direction to LTR or RTL
1. Add/Modify/Delete Sections (Playlists can be displayed section wise)
1. Add/Modify/Delete Section columns
1. Show/Hide Combo box and Filter box on page. (Filter will allow you to filter playlist by section)
1. Add/Modify/Delete combo box items and manage combo box styles by CSS class and text direction to LTR or RTL
1. Add/Modify/Delete Playlists
1. Download media by subgroups, combo items and sections
1. Add/Modify/Delete multiple records at the same time
1. Drag and Drop widget on any sidebar

* Front End (Shortcode and Widget):
1. Add shortcode on any Page/Post
1. Can make any subgroup as a default resource to load initially on page loading
1. User can filter the list by section
1. Filesize will be displayed automatically for every downloadable file
1. User will be able to download the media from different areas (by section, combo item selection, by subgroup)
1. HTML5 player has been included that can play all popular audio file types and also support mp4 videos
1. As of version 2.2.0, playing external videos (YouTube and Vimeo) are also supported
1. To use search bar, use the following shortcode:

> [giml_searchbar show_pagination="true" items_per_page="10" max_size="5"]

Where:

> *show_pagination*: Whether to display the page with pagination or not. Default is *true*.

> *items_per_page*: Number of rows to display per page. Default is *10*.

> *max_size*: Number of pagination buttons to display. Default is *5*.

= More information =
Please visit the plugin website at http://www.glareofislam.com/softwares/gimedialibrary.html for more information.

= Supporting future development =
If you like the GI-Media Library plugin, please rate and review it here in the WordPress Plugin Directory, support it with your [donation](http://www.glareofislam.com/softwares/gimedialibrary.html). Thank you!

== Screenshots ==

1. An example playlist table (as it can be seen on the [GI-Media Library plugin demo website](http://wpdemos.glareofislam.com/giml-demo1/))
2. "Creating Group/Subgroup" screen
3. "Creating Playlist" screen 1
4. "Creating Playlist" screen 2
5. "Creating Playlist" screen 3

== Installation ==

The easiest way to install GI-Media Library is via your WordPress Dashboard. Go to the "Plugins" page and search for "GI-Media Library" in the WordPress Plugin Directory. Then click "Install Now" and the following steps will be done for you automatically. You'll just have to activate GI-Media Library.

Manual installation works just as for other WordPress plugins:

1. Download and extract the ZIP file and move the folder "gi-media-libarary" into the "wp-content/plugins/" directory of your WordPress installation.
1. Activate the plugin "GI-Media Library" on the "Plugins" page of your WordPress Dashboard.
1. Create and manage course/media library by going to the "Settings" and then "GI-Media Library" section in the admin menu.
1. Follow the simple step by step usage instructions in the User's Manual which you can download from http://www.glareofislam.com/softwares/gimedialibrary.html.

== Frequently Asked Questions ==

= Support? =

For support questions, bug reports, or feature requests, please use the forum at [WordPress Support Forum](http://wordpress.org/support/plugin/gi-media-library). Please [search](http://wordpress.org/support/) through the forums first, and only [open a new thread](http://wordpress.org/support/plugin/gi-media-library) if you don't find an existing answer. Thank you!

= Requirements? =

In short: WordPress 3.6 or higher, while the latest version of WordPress is always recommended.

== Acknowledgements ==

Thanks to every donor, supporter and bug reporter!

== License ==

This plugin is Free Software, released and licensed under the GPL, version 2 (http://www.gnu.org/licenses/gpl-2.0.html).
You may use it free of charge for any purpose.
I kindly ask you for link somewhere on your website to http://www.glareofislam.com/. This is not required!
I'm also happy about [donations](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=HQ2DHNS7TQNZ8)! Thanks!

== Changelog ==

= Version 3.0.1 =
- Minor fixes

= Version 3.0 =
- Added search and pagination feature.
- Included floating player with playlist and download current track.
- Can link one Subgroup to multiple Groups.
- User can now modify Widget CSS directly from Widgets page when adding Widget in sidebar.
- Lots of fixes and improvements.

= Version 2.2.2 =
- Minor fixes in GI-LMS integration and to support upto WP version 4.0

= Version 2.2.1 =
- Minor improvements to support our GI-Learning Management System plugin

= Version 2.2.0 =
- Revised the code to support our future streamline of GI plugins for online education.
- Added support for playing External Videos (YouTube, Vimeo)
- Fixes minor bugs in the admin section

= Version 2.1.0 =
- Fixes compatibility issue with latest version of jQuery which fails to edit/update the playlist.
- Improved the visual layout of admin section.
- Included About page in admin for ease of access to User's Manual.

= Version 2.0.0 =
- Major update with bug fixes and layout improvements.

= Version 1.0.326 =
- Fixes bug in shortcode that completely hides the sidebar when only subgroup is displayed.
- Updated CSS to create standard layout of table

= Version 1.0.300 =
Initial release

== Upgrade Notice ==

= Version 2.0.0 =
- You must replace the old shortcode "gi-medialibrary" with "gi_medialibrary" on every page/post you have created.
