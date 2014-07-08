=== Featured Comments ===
Contributors: mordauk, Utkarsh
Donate link: http://pippinsplugins.com/support-the-site
Tags: comments, featured comments, feature comments, Pippin's Plugins, pippinsplugins, recent comments
Requires at least: 3.5
Tested up to: 4.0
Stable tag: 1.2.2

Lets the admin add "featured" or "buried" css class to selected comments. Handy to highlight comments that add value to your post. Also includes a dedicated widget for showing recently featured comments

== Description ==

Lets the admin add "featured" or "buried" css class to selected comments. Handy to highlight comments that add value to your post.

This plugin makes use of the meta_query option added in WordPress 3.5 to the WP_Comment_Query class, so it is no longer compatible with earlier versions of WordPress.

Please report bugs and suggestions on [Github](https://github.com/pippinsplugins/Featured-Comments).

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload 'feature-comments' directory to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

All the options will be automatically added to the edit comments table, and single comment edit screen

== Screenshots ==

1. Comment Edit Table
2. Single Comment Edit
3. Class added to comment, as seen on the frontend (screenshot shows source viewed in Firebug)

== Changelog ==

= 1.2.2 =

* Improved capability check when processing ajax requests

= 1.2.1 =

* Re-added Buried checkbox to the edit comment screen
* Added a file-modified-time version number to the JS to ensure file is not cached between updates
* Added a div.feature-burry-comments wrapper to the Feature | Bury links added to comments

= 1.2 =

* Development taken over by [Pippin Williamson](http://pippinsplugins.com)
* NOTE: no longer compatible with WordPress versions less than 3.5
* Replaced deprecated functions with up-to-date versions
* Added new Featured Comments widget
* Updated plugin class to a singleton

= 1.1.1 =
* Fixed bug, which showed feature/bury links to all users, instead of users with 'moderate_comments' capability.

= 1.1 =
* Major update
* Anyone with 'moderate_comments' capability is now able to feature/bury comments both from the frontend and backend
* Added support for featuring comments using ajax.
* The edit comments section now highlights featured comments, and reduces the opacity of buried comments.
* Fixed some E_NOTICE's

= 1.0.3 =
* Fixed a bug introduced in the last update

= 1.0.2 =
* Refactored source code

= 1.0.1 =
* Added missing screenshot files

= 1.0 =
* First version


== Upgrade Notice ==

= 1.2.1 =

* Re-added Buried checkbox to the edit comment screen
* Added a file-modified-time version number to the JS to ensure file is not cached between updates
* Added a div.feature-burry-comments wrapper to the Feature | Bury links added to comments

= 1.2 =

* Development taken over by [Pippin Williamson](http://pippinsplugins.com)
* NOTE: no longer compatible with WordPress versions less than 3.5
* Replaced deprecated functions with up-to-date versions
* Added new Featured Comments widget
* Updated plugin class to a singleton

= 1.1.1 =
* Fixed bug, which showed feature/bury links to all users, instead of users with 'moderate_comments' capability.

= 1.1 =
* Major update
* Anyone with 'moderate_comments' capability is now able to feature/bury comments both from the frontend and backend
* Added support for featuring comments using ajax.
* The edit comments section now highlights featured comments, and reduces the opacity of buried comments.
* Fixed some E_NOTICE's

= 1.0.3 =
* Fixed a bug introduced in the last update

= 1.0.2 =
* Refactored source code

= 1.0.1 =
* Added missing screenshot files

= 1.0 =
* First version