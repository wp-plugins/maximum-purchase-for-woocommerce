=== Maximum Purchase for WooCommerce ===
Contributors: vark
Donate link: http://www.varktech.com/woocommerce/maximum-purchase-for-woocommerce/
Tags: e-commerce, WooCommerce, shop, store, admin, price, pricing, maximum, purchase, limits, checkout
Requires at least: 3.3
Tested up to: 4.3
Stable tag: 1.07.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin allows you to set up maximum purchase rules for products in your store.  Purchases must meet these rules to proceed to checkout payment.



== Description ==

The Maximum Purchase plugin for WooCommerce gives you the ability to set up maximum purchase rules for products in your WooCommerce 1.0+ store.  Customer purchases must then meet these rules, to proceed to checkout payment.

If a purchase in your store fails a maximum purchase rule, an error message appears at the top of the checkout page, identifying the error situation and rule requirements.  The customer must resolve the error, before the purchase can be completed.   

Fully tested with WooCommerce 2.4+ .


= Now with Custom Messaging! =
You can enter your own Custom Minimum Purchase Messages by Rule 

= Unparalleled Customer Service =

*   Customer Service is as important as the plugin functionality itself
*   [Support](http://www.varktech.com/support/) is open 7 days for questions and 1-on-1 assistance.


= Introductory Video =
[youtube http://www.youtube.com/watch?v=_2fyD57c9Zc]


[Tutorials](http://www.varktech.com/woocommerce/maximum-purchase-for-woocommerce/?active_tab=tutorial) | 
[Documentation](http://www.varktech.com/woocommerce/maximum-purchase-for-woocommerce/?active_tab=documentation) | 
[Videos](http://www.varktech.com/woocommerce/maximum-purchase-for-woocommerce/?active_tab=video) | 
[Shameless E-Commerce](http://www.varktech.com/woocommerce/maximum-purchase-pro-for-woocommerce/)


= How does the rule choose to examine the cart? [Search Criteria]  =

*   By Role/Membership for cart contents  (allows different pricing rules for customer types / logged-in role, such as wholesalers or preferred customers). 


= Role/Membership Info for Search Criteria =

*Role/Membership is used within Wordpress to control access and capabilities, when a role is given to a user.  Wordpress assigns certain roles by default such as Subscriber for new users or Administrator for the site's owner. Roles can also be used to associate a user with a pricing level.  Use a role management plugin like [User Role Editor](http://wordpress.org/extend/plugins/user-role-editor/) to establish custom roles, which you can give to a user or class of users.  Then you can associate that role with a Maximum Purchase Rule.  So when the user logs into your site, their Role interacts with the appropriate Rule.*


= How does the rule choose to examine the cart? [Search Criteria - Pro Plugin]  =

*   By cart contents
*   For a single product
*   For a single product's variations
*   By Product Category or Maximum Purchase Category, and/or By Role/Membership

=> [Maximum Purchase Pro Plugin](http://www.varktech.com/woocommerce/maximum-purchase-pro-for-woocommerce/) 


= How is the rule applied to the cart search results? [Rule applied to] =
*   All : work with the total of the units/prices
*   Each : apply the rule to each product in the Rule Population
*   Any : Same as each, but limits the rule testing to the first X number of products.


= Rule Applies To Either: =
*   Units Quantity Amount
*   Price Amount


= A sample of a maximum purchase rule: =
*   If the purchaser is a Subscriber - [search criteria:Subscriber]
*   The maximum total for all purchases - [rule applied to:  all]
*   Must be greater than $20. - [price amount: $20]


= Checkout Error Messaging =
At checkout, the rules are tested against the cart contents.  If products are found in error, an error message (in two possible locations) will be displayed.  The error situation must be resolved, before the customer is allowed to leave the checkout and proceed to payment. 

Error messaging css can be customized using the custom css option on the Rule Options Settings screen.  There are also currency sign options, and a comprehensive debugging mode option.


= Checkout Error Message Formats =
*   Text-based descriptive format
*   Table-based format


= More Info =
[Tutorials](http://www.varktech.com/woocommerce/maximum-purchase-for-woocommerce/?active_tab=tutorial) | 
[Documentation](http://www.varktech.com/woocommerce/maximum-purchase-for-woocommerce/?active_tab=documentation) | 
[Videos](http://www.varktech.com/woocommerce/maximum-purchase-for-woocommerce/?active_tab=video) | 
[Shameless E-Commerce](http://www.varktech.com/woocommerce/maximum-purchase-pro-for-woocommerce/)


= Additional Plugins by VarkTech.com =
1. [Maximum Purchase for WooCommerce](http://wordpress.org/extend/plugins/maximum-purchase-for-woocommerce)
1. [Min or Max Purchase for WooCommerce](http://wordpress.org/extend/plugins/min-or-max-purchase-for-woocommerce) 
1. [Pricing Deals Pro for WooCommerce](http://www.varktech.com/woocommerce/pricing-deals-pro-for-woocommerce/) 


= Pricing Deals Pro offers you complete flexibility creating pricing deals =
1. Buy two of these, get 10% off of both
1. Buy two of these, get 10% off another purchase
1. Buy two of these, get one of those free
1. Pricing Deals of any sort, by Role/Membership
1. etc....

=> [Pretty much any deal you can think of, you"ll be able to do!](http://www.varktech.com/woocommerce/pricing-deals-pro-for-woocommerce/) 


== Installation ==

= Maximum Requirements =

*   WooCommerce 1.0
*   WordPress 3.3+
*   PHP 5+

= Install Instructions =

1. Upload the folder `maximum-purchase-for-woocommerce` to the `/wp-content/plugins/` directory of your site
1. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

Please review the following printed and video documentation.

[Tutorials](http://www.varktech.com/woocommerce/maximum-purchase-for-woocommerce/?active_tab=tutorial) | 
[Documentation](http://www.varktech.com/woocommerce/maximum-purchase-for-woocommerce/?active_tab=documentation) | 
[Videos](http://www.varktech.com/woocommerce/maximum-purchase-for-woocommerce/?active_tab=video) | 
[Shameless E-Commerce](http://www.varktech.com/woocommerce/maximum-purchase-pro-for-woocommerce/)

 Please post questions at the [Support](http://www.varktech.com/support/) page at varktech.com.


== Screenshots ==

1. Maximum Purchase Rule Screen
2. Group Search Criteria
3. Rule application method - Any
4. Rule application method - Each
5. Rule application method - All
6. Quantity or Price Maximum Amount
7. Error Message at Checkout




== Changelog ==

= 1.07.4 - 2015-05-09 =
* Fix - Lifetime history tracking

= 1.07.3 - 2015-05-05 =
* Enhancement - Refactor all messaging to now be all Woo-based
* Fix - added 'doing ajax' to lifetime
* Fix - pull displayed unit cost from cart price rather than db

= 1.07.2 - 2014-06-04 =
* Fix - post-purchase processing

= 1.07.1 - 2014-05-23 =
* Enhancement - Update Nag additional message.

= 1.07 - 2014-05-16 =
* Fix - change add_error to add_notice.
* Enhancement - Custom Messaging! Overrides default messaging for rule.
* Fix - New rule add - stutter removed
* Enhancement - change currency sign and currency formatting to follow parent plugin, removed setting from this plugin
* Fix - Https issue resolved
* Fix - Warnings damped 
* Enhancement - Pro version check from Free version 

= 1.06 - 2013-02-23 =
* Bug Fix - "unexpected T_CLASS/T_PUBLIC" - File admin/vtmXX-rules-ui.php was corrupted, but the corruption only showed up on some hosts (?!).  Huge thanks to Don for allowing full access to his installation to debug.   

= 1.05 - 2013-02-13 =
* Bug Fix - Rule Add screen was being overwritten by some other plugins' global metaboxes - thanks to Dagofee for debug help
* Bug Fix - PHP version check not being executed correctly on activation hook (minimum PHP version 5 required)
* Bug Fix - Nuke and Repair buttons on Options screen were also affecting main Options settings, now fixed

= 1.0 - 2013-01-15 =
* Initial Public Release

== Upgrade Notice ==

= 1.07.4 - 2015-05-09 =
* Fix - Lifetime history tracking

= 1.07.3 - 2015-05-05 =
* Enhancement - Refactor all messaging to now be all Woo-based
* Fix - added 'doing ajax' to lifetime
* Fix - pull displayed unit cost from cart price rather than db

= 1.07.2 - 2014-06-04 =
* Fix - post-purchase processing

= 1.07.1 - 2014-05-23 =
* Enhancement - Update Nag additional message.

= 1.07 - 2014-05-16 =
* Fix - change add_error to add_notice.
* Enhancement - Custom Messaging! Overrides default messaging for rule.
* Fix - New rule add - stutter removed
* Enhancement - change currency sign and currency formatting to follow parent plugin, removed setting from this plugin
* Fix - Https issue resolved
* Fix - Warnings damped 
* Enhancement - Pro version check from Free version 

= 1.0 - 2013-01-15 =
* Initial Public Release