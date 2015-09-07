=== Post Hierarchy Menu ===
Contributors: ijmccallum
Donate link: http://iainjmccallum.com/
Tags: menu, custom post types, hierarchy
Requires at least: 3.0.1
Tested up to: 4.3
Stable tag: 1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a widget that will display a nested list of any post type based on it's hierarchy.

== Description ==

I use a WordPress site to keep notes on a variety of subjects, they are organised as parents / children.  I needed a simple way to show them in a nested list that reflected their hierarchy.  So I wrote this plugin to do just that.

Once installed it addes a widget that will allow you to select which post type to display.  It's generally handy if you have hierarchical custom post types or a lot of deeply nested pages.  On the front end, this adds a script to load in the post list asyncrously - this lets your website load first without having to worry about pulling together the list of all your posts.  Onec the site is loaded, then all the posts will load.  Another resource saver for you - the posts are organised into their hierarchical order client side.  This means the server just spits out an array of posts and sends them to the visitor's browser.  Once they arrive the visitors browser will do the work of orgaisaiton.

== Installation ==

The usual - FTP the files into `/wp-content/plugins/` then activate in the admin.
Or - search 'post hierarchy menu' in the add plugins secrion, install and activate.

Now it's intalled, go to apperance -> widgets and you should see a fancy new widget sitting there ready for you to use!

== Screenshots ==

1. admin.png: The hierarchical post types.
2. front-end.png: The result.

== Frequently Asked Questions ==

Is it simple? Yes it is.

== Changelog ==

= 1.0 =
* This is the first version - it doesn't do much in terms of customisation, yet.

== Upgrade Notice ==

None as yet.