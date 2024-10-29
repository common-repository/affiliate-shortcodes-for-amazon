=== Shortcodes for Amazon Affiliate ===
Contributors: genweb
Donate link: https://netgrows.com/product/amazon-affiliate-shortcodes-pro/
Tags: amazon affiliate, amazon affiliate shortcode, amazon product advertising 5, amazon store, amazon advertising, amazon api 5, amazon afiliados
Requires at least: 5.2.5 
Tested up to: 5.4
Stable tag: trunk
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Amazon Affiliate Shortcodes, easy and fast Amazon store integration in your WordPress site. Insert the Amazon shortcodes in post, pages, widgets or anywhere.


== Description ==

* 100% compatible with the latest Amazon Product Advertising API 5.0 (API 4 deprecated on March 9, 2020).

* The products will appear as normal content in your site, the adblockers will not hide them.

* Auto-update old products.

* Search Amazon products by keyword.

* Choose between two available layouts: row or grid.

* Show or hide product title, description & price. 

* Custom post type and taxonomy: store and manage all Amazon products like WordPress posts or pages.

* Available country/locale options: Australia, Brazil, Canada, France, Germany, India, Italy, Japan, Mexico, Singapore, Spain, Turkey, United Arab Emirates, United Kingdom, and the United States. 
More info at https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region

* Spanish and English support.


== How to use ==

1 - Install & activate the plugin through the 'Plugins' menu in WordPress.

2 - Go to "AMZ Shortcodes -> Settings" and fill in your Amazon Affiliate Access Key, Amazon Affiliate Secret Key, Default Associate Tag, and Advertising API locale.

3 - Go to "AMZ Shortcodes -> Shortcodes" and create a new shortcode linked to your Amazon keyword. You can use any shortcode name.

4 - Copy the previous shortcode and insert it in any post, page or widget. Remember that if you are using WordPress Gutenberg editor, you can insert a custom block called "shortcode".
After this step, your post, page or widget should contain a shortcode similar to [amzcode "xx"].

5 - Publish or visit your content in the frontend. The first time that the shortcode is called, the products are retrieved from Amazon API. The 2nd time and up, the products are shown using the WordPress database.


== Manage products ==

You can manage all stored products under "AMZ Shortcodes -> All Products".

I do not recommend manually editing products content if you are auto-updating them from Amazon API, since your edits may be deleted when updated products arrive.


== Delete all products from category when updating ==

If you want to manually modify the products, you may find this setting useful.

When "Yes", it will delete ALL products linked to a shortcode after updating them using Amazon API. 

When "No", it will delete only the outdated products (an old product with exactly the same name as a recently retrieved product).


== Frequently Asked Questions ==

= I receive AMAZON SETTINGS ERROR, what can I do? =

1 - Please, check again your Amazon Affiliate Access Key, Amazon Affiliate Secret Key, Default Associate Tag and Advertising API locale under plugin settings.

2 - Please, check them again using a "SearchItems" request in https://webservices.amazon.com/paapi5/scratchpad/

== Screenshots ==

1. Amazon products manager.
2. Plugin main settings.
3. Manage shortcodes as categories.
4. Plugin frontend, choose between grid or row layout.

== Changelog ==

= 1.0 =
* First stable version.
* Auto update products.

= 0.5 =
* BETA version.

== Upgrade Notice ==

= 1.0 =
This version will allow you to create a completely working Amazon shop. Products will be updated automatically.