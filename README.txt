=== Review Content Type ===
Contributors: chetanchauhan
Tags: review, reviews, ratings, rich snippets, schema, hreview, star rating, stars, affiliate, product review, wp review
Requires at least: 3.8
Tested up to: 4.5
Stable tag: 1.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create and manage reviews easily with this feature-rich, extendable, powerful and free WordPress review plugin the right way.

== Description ==
Review Content Type is a feature-rich, extendable and powerful **WordPress review plugin** allowing you to create professional looking review websites with ease. Using this review plugin for WordPress, you can create in-depth reviews having pros, cons, summary, rating, featured image, pricing details, and affiliate links.

Whether you're an affiliate marketer or a blogger writing reviews of products, services or anything else, this plugin has you covered.

= How this WordPress review plugin is different? =
Unlike other WordPress review plugins that add review functionality to existing post types, this plugin adds new `review` custom post type along with related custom taxonomies instead. Thus, avoiding cluttering of existing post types with review related data. And, most importantly allowing better management and organization of reviews for an overall improved user experience.

= Features =
* Add unlimited pros and cons to reviews along with a brief summary.
* Call to action buttons ideal for affiliate links.
* Link featured image to the media file, review page, or any custom URL if you want.
* Choose from one of the inbuilt stars, point, or percentage rating types or create a new one.
* Customize rating scale of all available rating types as per your likings. For example, you can switch from the default 5-star half rating system to 10-star full rating system with few changes to the rating scale of stars rating type.
* Support Schema.org rich snippets.
* Custom capabilities for creating and managing reviews.
* Easily customize review permalinks.
* Compatible with any WordPress theme.
* Responsive and mobile friendly.
* Developer friendly with plenty of actions and filters and a flexible templating system.

= Documentation & Support =
Use WordPress.org support forum for getting any help on using this plugin. However, please read the documentation available [here](https://github.com/chetanchauhan/review-content-type/wiki/) thoroughly before posting on the [support forum](https://wordpress.org/support/plugin/review-content-type/).

= Contributing =
Developers can contribute by heading over to [Review Content Type GitHub repository](https://github.com/chetanchauhan/review-content-type/).

= Feedback =
If you like this plugin, then please don't forget to [leave a good rating and review](https://wordpress.org/support/view/plugin-reviews/review-content-type/). Any constructive feedback including feature requests that can make this WordPress review plugin better is always welcome!

== Installation ==
1. Upload `review-content-type` to the `/wp-content/plugins/` directory
1. Activate the plugin through the \'Plugins\' menu in WordPress
1. Configure the plugin from the 'Reviews > Settings' page

== Screenshots ==
1. A sample review displayed using TwentyFifteen theme.
2. Review data meta box for creating in-depth reviews.
3. Configure review permalink settings.
4. General settings screen.
5. Rating settings screen.
6. Display settings screen.

== Changelog ==
= 1.0.3 - 2016-04-13 =
* Fix: Span element not closed while displaying rating using `rct_rating_html()`.

= 1.0.2 - 2015-09-19 =
* Tweak: Added appropriate capabilities for managing reviews to the editor, author, and subscriber user roles too. Earlier only administrator user role was given the permissions to manage reviews.
* Tweak: Remove all custom capabilities and saved settings when the plugin is deactivated and deleted from WP dashboard respectively.
* Fix: Incorrect rating is saved if rating scale is customized while the review is being created/edited by the user.

= 1.0.1 - 2015-06-20 =
* New: Show published reviews count in the 'At a Glance' dashboard widget.
* Fix: Incorrect reviews updated messages when using bulk edit.

= 1.0.0 - 2015-04-18 =
* Initial release
