=== Comment Free Zone ===
Contributors: progressplanner, joostdevalk, aristath, mariekerakt, irisguelen, samalderson
Tags: disable comments, comments, trackbacks, pingbacks, moderation
Requires at least: 6.7
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.2
License: GPL-3.0+
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Disable comments across your WordPress site, including pingbacks, trackbacks, comment feeds, and comment-related admin screens.

== Description ==

Comment Free Zone helps you disable comments in WordPress without adding settings or extra maintenance. Activate the plugin and it removes comment-related functionality from your site so you can run a cleaner, simpler WordPress setup.

This plugin is built for sites that do not need comments at all, such as brochure sites, company sites, documentation sites, landing pages, and client websites where comments only add clutter or moderation overhead.

== What Comment Free Zone removes ==

* The Comments menu in wp-admin.
* The Discussion settings screen.
* Comment links from the admin bar.
* The comments column from relevant list tables.
* Comment and trackback support on post types that support them.
* Comment blocks from block-based output.
* The comment meta box from the editor experience.
* Comment RSS feeds.
* Comment endpoints and comment-related fields in relevant REST API responses.
* Outgoing pings and incoming pingbacks.

== Why site owners use it ==

* Keep WordPress focused on publishing instead of moderation.
* Reduce comment spam and comment-related noise.
* Simplify the admin for clients and editors.
* Remove comment UI from both classic and block-based experiences.
* Use a no-settings plugin that works immediately after activation.

== Installation ==

1. In your WordPress admin, go to Plugins → Add New.
2. Search for "Comment Free Zone".
3. Install and activate the plugin.
4. Comment functionality is disabled immediately. No further configuration is required.

You can also install the plugin manually:

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate Comment Free Zone from the Plugins screen in WordPress.

== Frequently Asked Questions ==

= Does this plugin delete existing comments? =

No. Comment Free Zone disables comment functionality and removes comment-related UI, but it does not delete existing comment data from your database.

= Does it disable pingbacks and trackbacks too? =

Yes. The plugin disables pingbacks and trackbacks alongside standard WordPress comments.

= Does it work on custom post types? =

Yes. The plugin removes comment and trackback support from post types that support those features.

= Do I need to configure anything after activation? =

No. This is a no-settings plugin. Activate it and the plugin applies its changes immediately.

= Where do I report a bug or security issue? =

If you've found a bug, please search the open issues on [GitHub](https://github.com/ProgressPlanner/comment-free-zone/issues/) first and open a new issue if needed.

If it's a security issue, please report it through our [Patchstack Vulnerability Disclosure Program](https://patchstack.com/database/vdp/comment-free-zone).

== Screenshots ==

<!-- TODO: Add plugin screenshots and keyword-rich captions for the WordPress.org listing. -->

== Changelog ==

= 1.0.2 =
* Minor naming updates.

= 1.0.1 =
* Updated the "Tested up to" WordPress version.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.2 =
Minor naming updates and compatibility metadata improvements.
