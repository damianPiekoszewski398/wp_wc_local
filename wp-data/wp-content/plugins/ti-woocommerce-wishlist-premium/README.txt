=== TI WooCommerce Wishlist Premium ===
Contributors: templateinvaders
Donate link: https://templateinvaders.com/
Tags: wishlist, woocommerce, products, e-commerce, shop, ecommerce wishlist, woocommerce wishlist, woocommerce , shop wishlist, wishlist  for Woocommerce
Requires at least: 4.7
Tested up to: 6.2
Stable tag: 2.5.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Documentation: https://templateinvaders.com/documentation/ti-woocommerce-wishlist/

More than just a Wishlist for WooCommerce, a powerful marketing & analytics tool.

== Description ==

You have full control over the look and functionality of your wishlist, from buttons and columns to processing and login options.
But what is really important is that you can grow a community using our exclusive follow feature and social share options.
People can create multiple wishlists for different events like birthdays, Christmas and share it with their friends, so they can not worry about gifts anymore.
On the other hand, customers can use it as collections with sets of different trendy clothes, gain followers and become a superstar!
When you at this time can learn what your customers "wish".
With the help of integrated analytics, you can see sales comparing to product popularity and other important information, that can help you build your successful sales strategy.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the `TI WooCommerce Wishlist Premium` through the 'Plugins' screen in WordPress
3. For the plugin to work as it should, the WooCommerce plugin has to be installed and enabled.

== Changelog ==
= 2.5.2 - Released 2023/05/29 =

* Modified AJAX to fetch data only after user interaction on the page
* Refreshed wishlist table when a product is added or removed from the wishlist
* Added filter `tinvwl_allow_addtowishlist_cart_product`
* Added integration with [WooCommerce Fast Cart](https://barn2.com/wordpress-plugins/woocommerce-fast-cart/ref/1007/) plugin
* Updated integration with [WooCommerce Composite Products](https://woocommerce.com/products/composite-products/?aff=3955) plugin
* Updated integration with the Flatsome theme
* Fixed an issue with the wishlist notification settings popup for guests
* Fixed a JavaScript notice: "Cannot read properties of undefined (reading 'stats')"
* Fixed an issue related to adding/removing products to the wishlist when the option 'Remove product from Wishlist on second click' is enabled
* Fixed an issue with custom icon states for the 'Add to Wishlist' button
* Fixed wishlist AJAX event issue for guests

= 2.5.0 - Released 2023/05/17 =

* Added support for WooCommerce 7.7
* Download wishlist data as PDF file
* Enhanced product custom meta handling
* Update the cart content when a product is added to the cart from the wishlist
* Refactored placeholders replacement for messages
* Fixed an issue with the `variation_id` attribute not working correctly on product listings
* Framework update

= 2.4.3 - Released 2023/05/06 =

* Added `{wishlist_title}` placeholder
* Fixed issues with products in wishlists data
* Fixed issue where the required login popup doesn't appear
* Added filter `tinvwl_wishlist_type_exclusion`


= 2.4.2 - Released 2023/05/04 =

* Added support for WordPress 6.2
* Added support for WooCommerce 7.6
* Added filter 'tinvwl_allow_data_cookies'
* Added filters 'tinvwl_api_wishlist_data_response' and 'tinvwl_api_product_data_response' for REST API response
* Updated integration with the [myCred – Points, Rewards, Gamification, Ranks, Badges & Loyalty Plugin](https://wordpress.org/plugins/mycred/) plugin
* Updated integration with the WooCommerce TM Extra Product Options plugin
* Updated integration with the [YITH WooCommerce Product Add-Ons](https://wordpress.org/plugins/yith-woocommerce-product-add-ons/) plugin
* Updated integration with Advanced Product Fields Extended for WooCommerce plugin
* Updated integration with [Advanced Product Fields (Product Addons) for WooCommerce](https://wordpress.org/plugins/advanced-product-fields-for-woocommerce/) plugin
* Updated integration with Flatsome theme
* Updated order item reference with wishlist author
* Fixed hidden option issue in the plugin settings
* Fixed JavaScript warning when checking cookies
* Fixed issue with removing from wishlist when default product variation is used
* Fixed wishlists search pagination issue when filtered

= 2.3.5 - Released 2023/03/01 =

* Fixed wishlist table ajax events issue for guests

= 2.3.4 - Released 2023/02/28 =

* Fixed license issue
* Fixed PHP warning in ajax events

= 2.3.3 - Released 2023/02/24 =

* Blocking the wishlist table visually while processing AJAX events
* Added integration with [Extra product options For WooCommerce | Custom Product Addons and Fields](https://wordpress.org/plugins/woo-extra-product-options/) plugin
* Added integration with [ione360 Configurator](https://wordpress.org/plugins/ione360-configurator/) plugin
* Added "input-checkbox" class to checkbox

= 2.3.2 - Released 2023/02/20 =

* Added check for wishlists save settings changes on the manage wishlists page
* Fixed PHP error for wishlist product price calculations

= 2.3.1 - Released 2023/02/16 =

* WooCommerce 7.4 support
* Fixed PHP errors for old WordPress and WooCommerce versions

= 2.3.0 - Released 2023/02/07 =

* WooCommerce 7.3 support
* Added WooCommerce High-Performance order storage feature compatibility
* Added product analytics export 
* Updated WCAG compatibility
* Fixed issue with wishlist table pagination count
* Fixed cache issue when cookies were set for guests
* Fixed PHP warning related to wishlists products stats
* Fixed products counter issue with private products

= 2.2.0 - Released 2023/01/03 =

* Added wishlist data update on login and logout
* Added integration with Ultimate Addons for the Elementor plugin
* Added integration with the VAD Product Design plugin
* Updated integration with the Divi theme
* Fixed admin menu duplicated items issue
* Fixed database upgrade issue
* Fixed low on stock email settings save issue
* Fixed email hardcoded logo size
* Fixed promotional email placeholders issue
* Fixed mini-wishlist issue on touchscreen laptops
* Fixed email notifications for back-to-stock, low-on-stock, and sale events
* Fixed issue with updating wishlists user data across devices
* Fixed issue with URL rewrites when wishlist set up under the WooCommerce My Account area
* Code clean up

= 2.1.0 - Released 2022/12/22 =

* Fixed security vulnerability related to the plugin updater
* Compatibility with PHP 8.1
* Added supports decimal value for products quantity in wishlist
* Added wishlist reference for cart, checkout, and order items when added from the wishlist
* Added remove and add to cart buttons to mini wishlist
* Tweaked mini wishlist options

= 2.0.17 - Released 2022/12/19 =

* WooCommerce 7.2 support
* Fixed PHP error related to the add to wishlist event
* Fixed plugin update check request

= 2.0.16 - Released 2022/12/06 =

* Updated wishlist table templates
* Fixed required login redirect for guests

= 2.0.15 - Released 2022/11/25 =

* Added option "Days after which the guest wishlist will be deleted"
* Fixed the message about the failed event of the add to cart button on the wishlist page
* Fixed wishlist products order issue
* Fixed update wishlist issue

= 2.0.14 - Released 2022/11/21 =

* WordPress 6.1 support
* WooCommerce 7.1 support
* Added wishlist product subtotal price and total wishlist price
* Added wishlist product is low on stock email notification
* Added wishlist products sorting by drag & drop feature
* Added feature to show stats for each product on the frontend
* Added feature to setup wishlist page under the WooCommerce My Account section
* Added redirect to the wishlist page in case of product is already on the wishlist and the event popup is disabled in the settings
* Added option "Products per page" for wishlist table
* Added options for add to wishlist button custom icon states
* Added option to hide AJAX popup on the wishlist page
* Added integration with [All in One Product Quantity for WooCommerce](https://wordpress.org/plugins/product-quantity-for-woocommerce/) plugin
* Added integration with [WC Fields Factory](https://wordpress.org/plugins/wc-fields-factory/) plugin
* Added filter `tinvwl_popup_close_timer`
* Added filter `tinvwl_social_link_email_recepient`
* Added JavaScript event `tinvwl_wishlist_ajax_response`
* Wishlist table actions migrated to AJAX calls
* Improved frontend AJAX routine
* Fixed URL rewrites issue
* Fixed issue with WooCommerce Blocks
* Fixed issue with wishlist products counter
* Fixed SQL error when a user gets empty wishlists search results
* Fixed WPML conflict with the My Account menu
* Fixed issue with marks product already in the wishlist
* Fixed issue with WPML plugin
* Fixed issue with multiwishlist select
* Updated integration with [WooCommerce Custom Product Addons](https://wordpress.org/plugins/woo-custom-product-addons/) plugin
* Updated integration with ShopEngine plugin
* Updated integration with WooCommerce TM Extra Product Options plugin
* Updated integration with WooCommerce Variation Swatches - Pro plugin
* Code cleanup

= 1.47.0 - Released 2022/06/06 =

* Updated integration with WooCommerce Blocks
* Fixed custom AJAX endpoint issue for customized WordPress setups
* Fixed integration issue with XforWooCommerce plugin
* Added filter `tinvwl_wishlistmanage_query`

= 1.46.0 - Released 2022/06/02 =

* WordPress 6.0 support
* WooCommerce 6.5 support
* Added option to disable promotional emails
* Added integration with Nasa Core plugin
* Added integration with [ShopEngine](https://wordpress.org/plugins/shopengine/) plugin
* Added filter `tinvwl_wishlist_get_products`
* Fixed issue related to multiwishlists default list creation

= 1.44.1 - Released 2022/05/05 =

* Updated integration with [WPC Product Bundles for WooCommerce](https://wordpress.org/plugins/woo-product-bundle/) plugin

= 1.44.0 - Released 2022/05/04 =

* Added support of WooCommerce Blocks


= 1.43.0 - Released 2022/04/27 =

* WooCommerce 6.4 support
* Added filters for share links
* Updated integration with [WooCommerce Gravity Forms Product Add-Ons](https://woocommerce.com/products/gravity-forms-add-ons/?aff=3955) plugin
* Added tinvwl_get_wishlist_data() as jQuery public function

= 1.42.1 - Released 2022/04/05 =

* Added integration with [WooCommerce Waitlist](https://woocommerce.com/document/woocommerce-waitlist/?aff=3955) plugin
* Updated integration with [WooCommerce Product Bundles](https://woocommerce.com/products/product-bundles/?aff=3955) plugin

= 1.42.0 - Released 2022/03/11 =

* WooCommerce 6.3 support
* Added customer company as placeholder for emails
* Added notification status for users screen of product analytics dashboard
* Added integration with [Anti-Spam by CleanTalk](https://wordpress.org/plugins/cleantalk-spam-protect/) plugin
* Updated integration with [WooCommerce Composite Products](https://woocommerce.com/products/composite-products/?aff=3955) plugin
* Fixed stock status message for wishlist table

= 1.41.1 - Released 2022/02/20 =

* Added feature to automatically close add to wishlist popup after few seconds if redirect disabled
* Added filter `tinvwl_addtowishlist_redirect`
* Fixed WPML plugin issue
* Fixed add to wishlist issue for guests

= 1.41.0 - Released 2022/02/17 =

* WooCommerce 6.2 support
* Updated integration with [Quick Buy Now Button for WooCommerce](https://woocommerce.com/products/quick-buy-now-button-for-woocommerce/?aff=3955) plugin
* Updated integration with [YITH WooCommerce Product Add-Ons](https://wordpress.org/plugins/yith-woocommerce-product-add-ons/) plugin
* Updated integration with [Google Tag Manager for WordPress](https://wordpress.org/plugins/duracelltomi-google-tag-manager/) plugin
* Fixed WPML plugin issue
* Added filter 'tinvwl_product_analytics_per_page'
* Added filter 'tinvwl_wishlists_per_page'


= 1.40.1 - Released 2022/01/29 =

* Fixed security issue

= 1.40.0 - Released 2022/01/27 =

* WordPress 5.9 support
* Fixed PHP error when saving products to wishlist on the cart page

= 1.30.1 - Released 2022/01/17 =

* WooCommerce 6.1 support
* Fixed PHP warning related to the add to wishlist button on the cart page

= 1.30.0 - Released 2022/01/10 =

* WooCommerce 6.0 support
* Updated integration with WooCommerce Product Add-Ons Ultimate plugin
* Fixed custom AJAX endpoint issue for some customized WordPress setups

= 1.29.0 - Released 2021/11/25 =

* WooCommerce 5.9 support

= 1.28.4 - Released 2021/09/08 =

* Added integration with [Quick Buy Now Button for WooCommerce](https://woocommerce.com/products/quick-buy-now-button-for-woocommerce/?aff=3955) plugin
* Updated integration with [WooCommerce Composite Products](https://woocommerce.com/products/composite-products/?aff=3955) plugin
* Updated integration with [WooCommerce Gravity Forms Product Add-Ons](https://woocommerce.com/products/gravity-forms-add-ons/?aff=3955) plugin

= 1.28.3 - Released 2021/08/25 =

* Added option Show "Continue Shopping" Button for the wishlist table navigation
* Fixed a bug when the newly created wishlist doesn't assign to the current user
* Fixed permalink issue for multilingual setups
* Fixed rename feature issues

= 1.28.2 - Released 2021/08/23 =

* Fixed rare validation issue for notification emails
* Updated integration with [PW WooCommerce Gift Cards](https://wordpress.org/plugins/pw-woocommerce-gift-cards/) plugin
* Updated integration with [myCred – Points, Rewards, Gamification, Ranks, Badges & Loyalty Plugin](https://wordpress.org/plugins/mycred/) plugin

= 1.28.1 - Released 2021/08/20 =

* Fixed PHP error in the add to wishlist button shortcode
* Fixed plugin webfont icons issue on some devices

= 1.28.0 - Released 2021/08/19 =

* WooCommerce 5.6 support
* Added option to rename the *wishlist* word across the plugin
* Tweaked current product detection for the add to wishlist button shortcode
* Updated integration with [WooCommerce Composite Products](https://woocommerce.com/products/composite-products/?aff=3955) plugin
* Updated integration with WooCommerce TM Extra Product Options plugin
* REST API wishlist create method supports user_id = 0 for guests
* REST API wishlist create method supports type attribute (list, default)


= 1.27.4 - Released 2021/08/17 =

* Accessible Rich Internet Applications enhancements
* Fixed issue for Wishlist REST API

= 1.27.3 - Released 2021/08/14 =

* Fixed translations issue
* Fixed variable products issue with [Polylang](https://wordpress.org/plugins/polylang/) plugin
* Fixed product translation issue for [WPML](https://wpml.org/?aid=9393&affiliate_key=9xzbMQnIyxHE) plugin when product added to cart from wishlist

= 1.27.2 - Released 2021/08/11 =

* Updated integration with [YITH WooCommerce Product Add-Ons](https://wordpress.org/plugins/yith-woocommerce-product-add-ons/) plugin
* Fixed products in wishlist issue for WPML different languages
* Fixed PHP fatal error

= 1.27.0 - Released 2021/07/31 =

* WordPress 5.8 support
* WooCommerce 5.5 support
* Improved DB queries performance
* Remove user roles capabilities on the plugin uninstall
* Updated integration with [WooCommerce Gravity Forms Product Add-Ons](https://woocommerce.com/products/gravity-forms-add-ons/?aff=3955) plugin
* Fixed WP Menu dashboard error notification.

= 1.26.0 - Released 2021/06/15 =

* WooCommerce 5.4 support
* Changed HTML markup for the empty wishlist template
* Fixed wishlist page pagination issue
* Fixed text domain
* Fixed an issue for integration with [WooCommerce Composite Products](https://woocommerce.com/products/composite-products/?aff=3955) plugin
* Fixed W3C HTML Validate issue
* Fixed PHP fatal error related to WP CLI
* CSS minor fixes

= 1.25.2 - Released 2021/04/20 =

* WooCommerce 5.2 support
* Updated integration with [AutomateWoo](https://woocommerce.com/products/automatewoo/?aff=3955) plugin
* Updated integration with WooCommerce TM Extra Product Options plugin
* Fixed extra JavaScript alert on wishlist table bulk events

= 1.24.5 - Released 2021/03/30 =

* Fixed FOIT for the custom icon webfont
* Security tweak
* Forced uppercase removed from a wishlist unique share key
* Hide wishlist products counter when it disabled for guests
* Updated integration with WooCommerce TM Extra Product Options plugin
* Updated integration with [Product Options and Price Calculation Formulas for WooCommerce – Uni CPO](https://wordpress.org/plugins/uni-woo-custom-product-options/) plugin

= 1.24.4 - Released 2021/03/25 =

* Security update

= 1.24.3 - Released 2021/03/23 =

* Added plugin settings to allow disabling built-in integrations with 3rd party plugins and themes
* Added preload for the plugin custom webfont
* Added integration with [WooCommerce Square](https://woocommerce.com/products/square/?aff=3955) plugin
* Updated integration with WooCommerce TM Extra Product Options plugin
* Updated integration for [Advanced Product Fields (Product Options) for WooCommerce](https://wordpress.org/plugins/advanced-product-fields-for-woocommerce/) plugin
* Updated integration with "Advanced Product Fields for WooCommerce Pro" plugin
* Fixed WPML issue for variations name
* Fixed wishlist privacy filter

= 1.23.10 - Released 2021/03/10 =

* WordPress 5.7 support
* WooCommerce 5.1 support

= 1.23.9 - Released 2021/03/03 =

* Updated integration with [AutomateWoo](https://woocommerce.com/products/automatewoo/?aff=3955) plugin
* Fixed 'Ask For An Estimate' button settings clash

= 1.23.8 - Released 2021/03/01 =

* Updated integration with Improved Product Options for WooCommerce plugin
* Fixed translation issue for manage wishlist table buttons
* Minor CSS fixes

= 1.23.7 - Released 2021/02/24 =

* Added filter `tinvwl_load_webfont` to allow disable to load webfont from 3rd party code
* Updated integration with [PW WooCommerce Gift Cards](https://wordpress.org/plugins/pw-woocommerce-gift-cards/) plugin
* Updated integration with the Flatsome theme
* Fixed issue when products don't add to wishlist while [WPML](https://wpml.org/?aid=9393&affiliate_key=9xzbMQnIyxHE) configured to show the default language as a fallback
* Fixed PHP error in the Dashboard->Appearance->Menus page
* Fixed the issue with WPML string translations for some admin text
* Minor CSS fixes

= 1.23.4 - Released 2021/02/19 =

* Added support of WooCommerce 5.0
* Added quantity attribute to add to wishlist button shortcode
* Added integration with [PW WooCommerce Gift Cards](https://wordpress.org/plugins/pw-woocommerce-gift-cards/) plugin
* Updated integration with [WooCommerce Product Add-ons](https://woocommerce.com/products/product-add-ons/?aff=3955) plugin
* Updated integration with [WooCommerce TM Extra Product Options](https://codecanyon.net/item/woocommerce-extra-product-options/7908619) plugin
* Updated integration with Flatsome theme
* Fixed PHP fatal error for WordPress less than 5.6.0 versions
* Remove deleted products OR products with invalid product type from a wishlist

= 1.23.3 - Released 2021/02/06 =

* Updated integration with "Advanced Product Fields for WooCommerce Pro" plugin

= 1.23.2 - Released 2021/02/04 =

* Fixed translation issue for wishlist table buttons
* Fixed PHP warning in rare cases of custom AJAX endpoint response
* Minor CSS fixes

= 1.23.1 - Released 2021/02/02 =

* Added option to show/hide product image on wishlist table
* Fixed PHP warning in customizer for Flatsome theme integration
* Fixed potential PHP error for custom AJAX endpoint

= 1.23.0 - Released 2021/02/01 =

* Added support of WooCommerce 4.9.x
* Added 'Ask For An Estimate' feature for guests
* Added add to wishlist button position state "After Thumbnails" for product page
* Added add to wishlist button position state "After Summary" for product page
* Updated add to wishlist button position state "Above Thumbnail" for catalog pages to display the button on top of the image
* Updated integration with [PPOM for WooCommerce](https://wordpress.org/plugins/woocommerce-product-addon/) plugin
* Updated integration with Flatsome theme
* Updated integration with [WooCommerce Product Add-ons](https://woocommerce.com/products/product-add-ons/?aff=3955) plugin
* Added Swedish translation
* Added Georgian translation
* Added Czech translation
* Added Norwegian (Bokmål) translation
* Added Romanian translation
* Added French translation
* Added Greek translation
* Added Spanish (Ecuador) translation
* Added Spanish (Mexico) translation
* Added Spanish (Venezuela) translation
* Added Spanish (Colombia) translation
* Updated Estonian translation
* Updated French (Canada) translation
* Updated Persian translation
* Updated Portuguese (Brazil) translation
* Updated Dutch translation
* Updated Polish translation
* Updated Spanish translation
* Updated Ukrainian translation
* Updated Finnish translation
* Updated Russian translation

= 1.22.1 - Released 2020/12/07 =

* Added compatibility with [LiteSpeed Cache](https://wordpress.org/plugins/litespeed-cache/) plugin
* Added compatibility with [Advanced Product Fields for WooCommerce Pro](https://www.studiowombat.com/plugin/advanced-product-fields-for-woocommerce/) plugin

= 1.22.0 - Released 2020/11/26 =

* WordPress 5.6 support
* Discontinued support for WooCommerce 2.x, minimum requirements are WooCommerce 3.0.0
* PHP 8 support
* Improved add to wishlist button behavior when it inserted outside add to cart form
* Improved WP SHORTINIT loading for the custom AJAX endpoint
* Fixed PHP notice in case of wrong pagination value for wishlist page
* Fixed PHP notices in case wishlist ID page is not valid WP_Post object

= 1.21.6 - Released 2020/11/11 =

* WooCommerce 4.7.x compatibility

= 1.21.5 - Released 2020/10/28 =

* WooCommerce 4.6.x compatibility
* Added `woocommerce_return_to_shop_text` filter introduced in WooCommerce 4.6.0
* Fixed issue with add to wishlist button incorrect response for WPML additional languages
* Fixed security issue related to import/export plugin settings.
* Fix PHP error related to search wishlists by guests

= 1.21.4 - Released 2020/10/3 =

* Updated integration for [Advanced Product Fields (Product Options) for WooCommerce](https://wordpress.org/plugins/advanced-product-fields-for-woocommerce/) plugin
* Fixed WPML additional languages issue with wishlist products
* Fixed the issue with wishlists list in the dialog dropdown

= 1.21.3 - Released 2020/09/28 =

* WooCommerce 4.5.x compatibility
* Added integration with [Check Pincode/Zipcode for Shipping Woocommerce](https://wordpress.org/plugins/check-pincodezipcode-for-shipping-woocommerce/) plugin
* Added integration for [WPC Variations Radio Buttons for WooCommerce](https://wordpress.org/plugins/wpc-variations-radio-buttons/) plugin
* Updated integration with [WooCommerce Multilingual](https://wordpress.org/plugins/woocommerce-multilingual/) plugin
* Use wp_kses_post instead of esc_html for sanitizing template strings to allow minimal HTML
* Fixed an issue with WPML products data
* Fixed an issue with incorrect JSON product data in some cases
* Fixed attributes display for variation on wishlist table
* Fixed HTML markup validation for integration with [WPC Product Bundles for WooCommerce](https://wordpress.org/plugins/woo-product-bundle/) plugin
* Fixed empty wishlist issue after adding a product to cart from paginated page
* Fixed wishlist products counter widget issue with Avada builder
* Fixed wishlist products counter dropdown issue
* Fixed wishlist products counter issue with quantity
* Fixed wishlist analytics for guests
* Fixed promotional email preview feature

= 1.21.2 - Released 2020/09/02 =

* Fixed issue with custom AJAX endpoint loading
* Updated integration with [WPC Product Bundles for WooCommerce](https://wordpress.org/plugins/woo-product-bundle/) plugin
* Updated integration with [WP Fastest Cache](https://wordpress.org/plugins/wp-fastest-cache/) plugin

= 1.21.1 - Released 2020/08/26 =

* Added compatibility with WooCommerce 4.4.x
* Added notification for users about emails opt-in and add global option to enable notifications by default
* Added empty option for products counter menu multi-select form element
* Added ability for admins to view private wishlists type
* Fixed critical issue when WPML deleted all products from other languages for current wishlist
* Fixed issue for Flywheel Cloud sites
* Fixed product name in messages for variable products
* Fixed issue with [Enable jQuery Migrate Helper](https://wordpress.org/plugins/enable-jquery-migrate-helper/) plugin
* Fixed integration with [WooCommerce Composite Products](https://woocommerce.com/products/composite-products/?aff=3955) plugin
* Fixed integration with [Advanced Product Fields (Product Options) for WooCommerce](https://wordpress.org/plugins/advanced-product-fields-for-woocommerce/) plugin
* Fixed issue for variable products in 'change price' email notification

= 1.21.0 - Released 2020/08/13 =

* WordPress 5.5.x support
* Added compatibility with WooCommerce 4.3.x
* Wishlist user-side data completely refactored
* Great performance improvements
* Fixed integration with [WooCommerce Bookings](https://woocommerce.com/products/woocommerce-bookings/?aff=3955) plugin

= 1.20.0 - Released 2020/06/25 =

* Added compatibility with WooCommerce 4.2.x
* Added RTL support
* Added email content edit options for change price and back to stock notifications
* The href attribute removed from add to wishlist button tag
* Wishlist REST API frontend request changed to POST method
* Improved security for Wishlist REST API methods
* Added integration with ELEX WooCommerce Catalog Mode plugin
* Added integration with WooCommerce Product Retailers plugin
* Added integration with Braintree For WooCommerce plugin
* Added integration with WPC Product Bundles for WooCommerce plugin
* Added integration with WooCommerce Product Table plugin as a custom table column ‘wishlist’
* Improved integration with Product Options and Price Calculation Formulas for WooCommerce – Uni CPO plugin
* Improved integration with WPML plugin
* Improved integration with WooCommerce Multilingual plugin
* Improved integration with WP Fastest Cache plugin
* Improved integration with WooCommerce Custom Product Addons plugin
* Fixed Wishlist REST API get_by_user method issue when extra wishlist always created on each request
* Fixed wishlist status issue for variations
* Fixed accessibility issue for add to wishlist button markup
* Fixed an issue with wishlist table quantity field on the press enter key
* Fixed an issue with the wrong wishlist status for add to wishlist shortcode
* Fixed an issue when wishlist status wrong on specific variation product page load
* Fixed PHP Fatal error in case wishlist page doesn’t set and forced permalinks option is active
* Fixed ‘add to wishlist’ button shortcode issue when product variation ID does not count for saved products
* Fixed an issue when adding product variation that has an additional attribute(s) set as ‘Any …’ to wishlist and from wishlist to cart
* Fixed an issue with duplicated buttons for out of stock products and Divi theme
* Fixed an issue with WP Rocket cache and WP REST API authorization for non-logged users
* Fixed an issue with file upload for the integration of WooCommerce TM Extra Product Options plugin

= 1.19.0 - Released 2020/04/27 =

* Added integration with [Product Options and Price Calculation Formulas for WooCommerce – Uni CPO](https://wordpress.org/plugins/uni-woo-custom-product-options/) plugin
* Added integration with [Hide Price and Add to Cart Button](https://woocommerce.com/products/hide-price-add-to-cart-button/?aff=3955) plugin
* Added integration with [Advanced Product Fields (Product Options) for WooCommerce](https://wordpress.org/plugins/advanced-product-fields-for-woocommerce/) plugin
* Added integration with WooCommerce Product Add-Ons Ultimate plugin
* Added integration with WooCommerce Fees and Discounts plugin
* Added compatibility for wishlist products counter in Woostify theme
* Fixed integration with WooCommerce Rental & Bookings System plugin
* Fixed integration with Improved Product Options for WooCommerce plugin
* Improved integration with [WooCommerce Composite Products](https://woocommerce.com/products/composite-products/?aff=3955) plugin
* Fixed multiple REST API queries for dynamic buttons
* Fixed wishlist products counter "hide zero value" issue
* Fixed wishlist products counter widget link issue on touch devices

= 1.18.0 - Released 2020/04/21 =

* Improved cache compatibility for ‘add to wishlist’ button states and wishlist products counter
* Wishlist products counter updating moved from WooCommerce AJAX fragments to a custom solution
* Added option to add wishlist products counter to multiple WordPress menus from plugin settings
* Added {product_sku} placeholder for add to wishlist message text
* Added filter `tinvwl_addtowishlist_message_placeholders` to override add to wishlist message placeholders from  a 3rd party code
* Added filter ‘tinvwl_add_to_menu’ to allow disabling wishlist products counter from 3rd party code
* Added filter ‘tinvwl_addtowishlist_not_allowed’ for validation of product addition to wishlist from a 3rd party code
* Fixed an issue when the newly created list doesn't show on the same page without reloading
* Fixed wishlists search results issues
* Minor CSS fixes

= 1.17.1 - Released 2020/04/10 =

* Fixed PHP fatal error for WP Rocket integration

= 1.17.0 - Released 2020/04/01 =

* WordPress 5.4 support
* WooCommerce 4.0.x support
* Added tool for export and import plugin settings
* Added to share buttons translated title
* REST API tweak: empty wishlist returns 200 response code and empty array now
* REST API tweak: create wishlist method now returns error description if required user_id not specified

= 1.16.2 - Released 2020/02/19 =

* Fixed PHP Warning related to cart clearing in WooCommerce 3.9+
* WP Rocket integration updated to use dynamic cookies for cache and fixed issue with cached wishlist products counter value

= 1.16.0 - Released 2019/12/30 =

* WooCommerce 3.9.0 support
* PHP 7.4 support
* Added {product_name} placeholder support for options of add to wishlist notice text
* Added filter `tinvwl_api_wishlist_get_products_response` to allow modify REST API response via 3rd party code
* Added compatibility with WooCommerce Tiered Price Table plugin
* Fixed fatal error throws by add to wishlist shortcode
* Fixed wishlist frontend issue when WooCommerce session doesn’t exists

= 1.15.4 - Released 2019/12/16 =

* Added compatibility with Kallyas theme and guest issue when cart is hidden
* Fixed product meta display on wishlist table
* Improved compatibility with Divi builder

= 1.15.3 - Released 2019/12/12 =

* Added compatibility for Divi builder to fix 'out of stock' issue
* Enhanced compatibility with Improved Product Options for WooCommerce plugin

= 1.15.2 - Released 2019/12/06 =

* Added compatibility with WooCommerce Variation Swatches - Pro plugin
* Enhanced integration with WP Grid Builder plugin
* Updated integration with WooCommerce TM Extra Product Options plugin
* Updated integration with [WooCommerce Bookings](https://woocommerce.com/products/woocommerce-bookings/?aff=3955) plugin

= 1.15.1 - Released 2019/12/04 =

* Added WP GridBuilder plugin custom block to show wishlist button
* Fixed bulk-adding products from wishlist to cart for WooCommerce versions below 3.8
* Fixed pricing issue for TM Extra Product Options plugin integration
* Trigger stock notification email on backorder status also

= 1.15.0 - Released 2019/11/27 =

* Added support for WordPress 5.3
* Added support for WooCommerce 3.8
* Added compatibility with Flatsome theme
* Added AutomateWoo plugin integration with following triggers:
* product added to wishlist;
* product on wishlist goes on sale;
* wishlist reminder;
* product from wishlist added to cart;
* product from wishlist purchased;
* product removed from wishlist;
* Added compatibility with WooCommerce Min Max Quantity & Step Control Single plugin
* Added compatibility with WooCommerce Variations Table - Grid plugin
* Added compatibility with YITH WooCommerce Quick View plugin
* Enhanced integration with WooCommerce Composite Products plugin
* Enhanced integration with WooCommerce Product Bundles plugin
* Enhanced integration with Improved Product Options for WooCommerce plugin
* Updated title method to the name method for WooCommerce 3+
* Fixed potential warning for conditional function is_page()
* Fixed JavaScript issue with URL get parameters
* Fixed issue related to 'Add All to cart' action
* Fixed issue with removing products after adding to cart
* Fixed issue with uppercase while wishlist share via copy to clipboard action

= 1.14.4 - Released 2019/09/04 =

* Fixed an issue with bundle products inside the composite product
* Fixed Setup Wizard errors
* Fixed WooCommerce 'My account' endpoint rewrites issue
* Added Polish translation

= 1.14.3 - Released 2019/09/02 =

* Fixed plugin updates check the issue
* Added hooks to product analytics table

= 1.14.2 - Released 2019/08/31 =

* Added compatibility for hook 'woocommerce_before_cart_item_quantity_zero' that deprecated from WooCommerce 3.7.0
* Added 'tinvwl_wishlist_privacy_types' filter to wishlist privacy types array for the create wishlist page
* Fixed 'Add All to Cart' button issue
* Fixed PHP Error when 'wc_clear_notices' method is not available

= 1.14.1 - Released 2019/08/29 =

* Added hooks to bulk actions feature on the wishlist page
* Added 'tinvwl_wishlist_privacy_types' filter to wishlist privacy types array for the manage wishlist page
* Added integration with WP Grid Builder plugin
* Added integration with WooCommerce Show Single Variations by Iconic plugin
* Fixed the option name 'Already In Wishlist'
* Fixed PHP Error when 'wc_clear_notices' method is not available
* Fixed integration with WooCommerce TM Extra Product Options plugin
* Fixed WooCommerce.com affiliate code issue
* Improved integration with WooCommerce Custom Product Addons (Pro) plugin
* Improved stylesheets loading for plugin settings

= 1.14.0 - Released 2019/08/15 =

* Added feature to show 'Already on Wishlist' text on buttons
* Added JavaScript event 'tinvwl_wishlist_added_status'
* Added compatibility with [Variations Swatches and Photos](https://woocommerce.com/products/variation-swatches-and-photos/?aff=3955) plugin
* Added compatibility with Clever Swatches plugin
* Added compatibility with WooCommerce Custom Product Addons (Pro) plugin
* Added compatibility with WooCommerce Rental & Bookings System plugin
* Added compatibility with Booking & Appointment Plugin for WooCommerce
* Refactored all 3rd party integrations code
* Fixed an issue with properly load custom translation files
* REST API fixes

= 1.13.0 - Released 2019/08/10 =

* Added support for WooCommerce 3.7
* Added wishlist [REST API](https://templateinvaders.com/api/wishlist/?utm_source=wordpressorg&utm_content=changelog)
* Added search wishlist by sharekey on backend wishlists table
* Added cleanup of deleted products from wishlist
* Added parameters to the 'tinvwl_get_wishlist_products' to get a custom products query results
* Refactored filters and actions
* Fixed unclosed 'strong' tag in admin notification
* Fixed empty URL issue for sharing buttons
* Fixed performance for meta tags addition on the wishlist page
* Fixed a compatibility issue with WooCommerce Show Single Variations by Iconic plugin
* Fixed a search results issue for products analytic
* Fixed the issue with Dashboard menu position
* Removed Google Plus (G+) sharing option since the service is closed

= 1.12.2 - Released 2019/07/03 =

* Fixed an issue with deleted product bug for frontend templates
* Fixed an issue with wishlist products query on multilingual setup
* Fixed "Move" button bug for wishlist products
* Prevent plugin activation in network-wide mode on a multisite setup
* Improved compatibility with TM Extra Product Options plugin

= 1.12.1 - Released 2019/06/20 =

* Fixed an issue with saving to wishlist from the cart page
* Fixed an issue with deleted product bug on the Analytics page
* Fixed a bug with forced Dashboard translation to default WordPress language instead of user profile settings
* Fixed Wishlist counter dropdown CSS issues for OceanWP theme

= 1.12.0 - Released 2019/05/17 =

* Greatly improved frontend performance
* Added method tinvwl_get_wishlist_products() to get wishlist products by wishlist ID or SHAREKEY
* Added filter <i>tinvwl_wc_cart_fragments_refresh</i> to disable WC cart fragments refreshing
* Fixed integration issues with Improved Product Options for WooCommerce plugin
* Fixed wishlist analytics issues
* Fixed manage wishlist pagination issue
* Fixed update wishlist issue with the limit to 10 products
* Fixed issue with auto removing grouped products from wishlist
* Fixed 'Add all to cart' issue for miniwishlist dropdown
* Code cleanup

= 1.11.0 - Released 2019/04/21 =

* WooCommerce 3.6.x support
* Added custom hook for [myCRED](https://wordpress.org/plugins/mycred/) plugin
* Added integration with Improved Product Options for WooCommerce plugin
* Minor PHP fixes and improvements

= 1.10.1 - Released 2019/04/14 =

* Email notifications opt-in functionality enhanced
* Added filter 'tinvwl_notifications_option' to hide/show 'notification button' on navigation
* WooCommerce PPOM plugin integration updated
* Fixed analytics purchase count issue

= 1.10.0 - Released 2019/04/09 =

* Added Force permalinks rewrite feature
* Added Opt-In functionality for email notifications
* Added numeric pagination on a Wishlist page
* Added filter 'tinvwl_allow_zero_quantity' to allow to save products into a Wishlist with a zero quantity
* Added filter 'tinvwl-allow_parent_variable' to allow to add parent variable product without variation selection from a 3rd party code.
* Fixed HTML W3C validation for add to wishlist button
* Fixed the limit for "Add all to cart" function
* Fixed an issue with removing products from wishlist when added to cart for WooCommerce prior 3.x versions.
* Fixed an issue with the search for a wishlist form
* Refactored email classes
* Removed hook checker functionality
* Improved the Setup Wizard to prepend shortcode to existing page content
* Improved compatibility with TM Extra Product Options plugin

= 1.9.1 - Released 2019/03/05 =

* Fixed templates path issue
* Fixed MySQL errors for admin search
* Fixed email settings
* Added compatibility with [Woocommerce Product Addons](https://wordpress.org/plugins/woo-custom-product-addons/) plugin by acowebs
* Improved compatibility with WooCommerce Advanced Quantity plugin
* Minor CSS fixes

= 1.9.0 - Released 2019/02/26 =

* Fixed WooCommerce templates location filter
* Fixed email settings sync issue
* Fixed font icon class name
* Fixed some typos and texts
* Fixed text domains
* Fixed pluggable function load order
* Fixed product in wishlist button class for variable type
* Fixed fatal errors when some 3rd party filters use global product object outside loop
* Fixed extra notice for trashed products
* Fixed the issue with trashed and deleted products
* Fixed infinite redirect loop if wishlist page is the same as WooCommerce "My account" page
* Fixed an issue with the product name in WooCommerce notice if add to cart validation failed
* Fixed an issue with cart and checkout redirect during bulk adding to cart process from a wishlist
* Fixed wishlist not found an issue when a guest is browsing his own empty wishlist
* Fixed a wishlist button issue for out of stock variable products
* Fixed mobile menu wishlist counter issue for OceanWP theme
* Fixed WC fragments refresh issue on products removing from a wishlist event
* Fixed WC_Cache method compatibility for WooCommerce prior to 3.2.0
* Fixed no-cache issue for WooCommerce below 3.2.4
* Fixed wishlist pagination rewrite conflicts
* Fixed login redirect with sharekey argument
* Fixed some PHP notices
* Fixed some issues related to PHP 7.3
* Fixed DB issue with INT type for IDs
* Fixed wishlist table markup for Composite products
* Fixed wizard output for WordPress 5.0+

* Improved accessibility for wishlist table elements
* Improved capabilities for admin pages
* Improved button shortcode output to support loop attribute
* Improved short variables names to avoid conflict with translate method
* Improved "Add to Wishlist" button behaviour for variable products
* Improved compatibility with WooCommerce plugin prior to 3.2 versions
* Improved compatibility with WooCommerce Multilingual plugin
* Improved compatibility with WooCommerce Product Bundles plugin
* Improved compatibility with WooCommerce Subscriptions plugin
* Improved compatibility with YITH WooCommerce Product Add-Ons plugin
* Improved add to cart error description
* Refactored <i>tinv_url_wishlist_by_key()</i> function
* Refactored promotional email class

* Added the ability to apply the wishlist page on a home page or a shop page
* Added <i>tinvwl-wc-cart-fragments</i> filter to disable wc-cart-fragments from 3rd party code
* Added filter <i>tinvwl_wishlist_products_counter</i> to allow change the value of wishlist products counter from a 3rd party code
* Added hook <i>tinvwl_product_added</i> to trigger when product added to a wishlist
* Added an option to hide zero-value in wishlist product counter
* Added parameters for button markup filter
* Added wishlist pages status
* Added icon animation on wishlist events loading
* Added unique IDs for Wishlist menu options to avoid any incompatibility issues with another plugin option
* Added "WhatsApp" and "Copy to clipboard" share options
* Added an option to show the wishlist products counter in WP menu
* Added redirect to current page after successful login
* Added restriction for template overrides filter to plugin templates only
* Added an option to redirect to the checkout page from wishlist if added to cart
* Added custom capabilities for dashboard pages
* Added Google Tag Manager for WordPress compatibility
* Added WooCommerce Custom Fields plugin integration
* Added Finnish (Suomi) translation
* Added Persian translation

* Removed deprecated admin template ajax call checker
* Icon png sprites replaced with custom font icons

= 1.8.0 - Released: 2018/06/13 =

* WooCommerce 3.4.x support
* Fixed compatibility issue with Font-Awesome 5+ library used in some themes
* Fixed JS issue with WooCommerce plugin versions less than 3.0.0
* Fixed an issue when "Remove Product" button disappears on mobile devices and tablets
* Fixed "input-group" class compatibility issue with Bootstrap 4
* Fixed conflict between WPML integration and latest Polylang plugin
* Date of products addition changed to WP local time instead of server time
* Disabled templates hook checker
* Improved compatibility with OceanWP WordPress theme
* Improved compatibility with WP Super Cache plugin
* Improved compatibility with WooCommerce Composite Product plugin:
* Fixed an issue with save cart functionality
* Added better integration
* Improved compatibility with Personalized Product Option Manager plugin:
* Fixed "Add to Wishlist" button position issue
* Fixed PHP notices
* Added support chat in plugin settings
* Added an alert when items or action are not selected before applying bulk actions on a Wishlist page
* Added Portuguese (Brazil) translation
* Added support for WooCommerce Catalog Visibility Options plugin
* Added support for WooCommerce Product Add-ons plugin
* Added support for YITH WooCommerce Product Add-Ons plugin

= 1.7.1 - Released: 2018/03/13 =

* Fixed PHP fatal error in a public wishlist view
* Fixed wishlists limit in a dropdown of add to wishlist dialogue when multi-wishlists support enabled

= 1.7.0 - Released: 2018/03/01 =

* Fixed Fatal error: if $product is not WooCommerce product
* Fixed Fatal error: if get_user_by is an object
* Fixed jQuery notice related to 3.0+
* Fixed an issue with deprecated function create_function(); on PHP 7.2+
* Fixed an issue with duplicated navigation buttons on a Wishlist page
* Fixed an issue with duplicated products in a Wishlist
* Fixed an issue with saving products into Wishlist on a cart page
* Fixed an issue with empty Wishlist name
* Fixed an issue with custom meta disappearing after moving products between wishlists
* Fixed an issue when "Add to Cart" action was available after disabling all buttons on a Wishlist page
* Fixed an issue when "Add to Wishlist" button didn't appear on a product details page for products without a price
* Fixed an issue when empty wishlist is created once a guest visits the shop page
* Fixed an issue with displaying SKU attribute after adding products to Wishlist from a catalogue
* Fixed text domains for some strings
* Added new option "Require Login" that disallows guests to add products to a Wishlist until they sign-in
* Added new option "Show button text" that allows displaying the only add to wishlist icon
* Added "nofollow" attribute for button links
* Added wishlists sorting by date of creation in the admin panel
* Added filter "tinvwl_addtowishlist_dialog_box" that will be helpful to override data in pop-up windows
* Added filters "tinvwl_addtowishlist_login_page" and "tinvwl_addtowishlist_return_ajax" that will be helpful to override "Require Login" popup.
* Added support for WooCommerce TM Extra Product Options plugin
* Added support for WP Multilang plugin
* Added French (Canada) translation
* Added Dutch (Netherlands) translation
* Improved compatibility with Personalized Product Option Manager plugin
* Improved Wishlist Products Counter functionality
* Improved variable products processing when adding to Wishlist

= 1.6.1 - Released: 2017/12/05 =

* Fixed an issue when guests could not add products into a Wishlist
* Improved Wishlist Products Counter functionality

= 1.6.0 - Released: 2017/12/01 =

* Fixed an issue with the wrong metadata after sharing Wishlist on Facebook
* Fixed Wishlist Products Counter issue when the wrong number of products was displaying if a cache is enabled
* Fixed an issue with W3 Total Cache plugin
* Fixed an issue with extra scheduled cleanup events
* Fixed JavaScript frontend compatibility issue
* Fixed SQL query to avoid an issue when Wishlist title has an apostrophe
* Fixed an issue with a duplicated call to WC AJAX fragments
* Fixed an issue with displaying product thumbnails in emails

* Added "Reset to Defaults" option in the admin panel
* Added an option to show the “Add to Wishlist” button above product thumbnail on a catalogue page
* Added an option to show/hide mini wishlist for products counter
* Added support for Comet Cache plugin
* Added support for WP Fastest Cache plugin
* Added new "Soft Skin" for wishlists
* Added filter "tinvwl_allow_addtowishlist_single_product" that helps to show/hide the "Add to Wishlist" button for specific products on a single products page
* Added hook "tinvwl_send_ask_for_estimate_args" that helps to override the estimate email form
* Lots of CSS fixes and improvements

* Improved Wishlists storage functionality:
* empty wishlists that do not have a user will be automatically removed after 7 days
* wishlists that contain at least 1 product but do not have a user will be automatically removed after 31 days
* Improved performance for custom styles
* Improved Setup Wizard
* Improved W3 Validation
* Improved Translation
* Corrected some texts
* Corrected some typos

= 1.5.7 - Released: 2017/10/21 =

* Fixed an issue with fonts not applying in Wishlist if "Use Theme Style" option is enabled
* Fixed an issue with transferring products between guest and user wishlists used on the same device/machine in the same browser.
* Fixed an issue with empty pop up window after clicking Share Wishlist by Email button
* Internal improvements:
* Remove the product from Wishlist on second click functionality does not depend whether products quantity is enabled or not any more.
* Variable product (without predefined variations applied by default) added from products listing page will be always substituted with the product added from details page (with selected variations).
* Reorganized "General Settings" section in admin panel to make it more user friendly.
* Added options descriptions in the admin panel.
* Improved "Wishlist Products Counter" widget
* If product added to multiple wishlists at the same time, all wishlists will be displayed below the product thumbnail in a dropdown.
* "View Wishlist" and "Add All to Cart" buttons are no longer visible if wishlist is empty.
* Improved WooCommerce Composite Products plugin support:
* Fixed individual price calculation with components
* Improved Polylang plugin support

= 1.5.3 - Released: 2017/09/20 =

* Fixed an issue with transferring products between guest and customer wishlists after signing in or logout.
* Fixed an issue when it's not possible to remove products from wishlist as a guest
* Fixed an issue with adding product variations to wishlist
* Improved WooCommerce Product Bundles plugin support:
* Fixed an issue with displaying product custom meta attributes
* Improved WPML plugin compatibility:
* Fixed an issue with "Remove/Add" button text when switching languages

= 1.5.2 - Released: 2017/09/11 =

* Improved WooCommerce product bundles support:
* Fixed an issue when product variations were not applied in bundled products
* Fixed an issue with products visibility on a Wishlist page
* Fixed an issue with inactive "Add to Cart" button for product variations when Wishlist functionality for unauthenticated users is disabled
* Added arguments for a filter that make possible overriding popup notices

= 1.5.1 - Released: 2017/09/03 =

* Fixed an issue when a product does not remove automatically from Wishlist after adding to cart
* Fixed an issue with duplicated products in Wishlist when WooCommerce Multilingual plugin activated
* Fixed a JavaScript error that prevents adding products to a wishlist

= 1.5.0 - Released: 2017/08/30 =

* Added "Wishlist Products counter" shortcode & widget
* Added "Save for Later" functionality for a Cart page
* Added the ability to remove a product from a Wishlist on the second click
* Added an option to show/hide a popup with successful or error notices after adding or removing products from a Wishlist
* Added support for plugins/WooCommerce add-ons that use custom meta:
* WooCommerce Gift Cards
* WooCommerce Bookings
* WooCommerce Subscriptions
* WooCommerce Composite Products
* WooCommerce Product Bundles
* WooCommerce Mix and Match
* WooCommerce Quantity Increment
* WooCommerce Personalized Product Option
* WooCommerce Gravity Forms Product Add*Ons
* YITH WooCommerce Product Bundles
* Added missing descriptions to some of the settings in the admin panel
* Added the ability to load custom translation files
* Added minified version of FontAwesome library
* Improved WPML compatibility
* Fixed an issue with a redirect to 404 error page after new wishlist is created
* Fixed fatal error in Dashboard menu
* Fixed a few PHP notices
* Fixed an issue when variation has an additional attribute(s) with any value
* Lots of CSS fixes
* Overall frontend performance optimization
* Code cleanup

= 1.3.3 - Released: 2017/05/07 =

* Improved WPML compatibility (fixed an issue with URL formats)
* Fixed issues with deprecated hooks related to WooCommerce 3.0.5
* Added Polylang plugin support
* Added new option that allows product automatic removal when it's added to the cart by anyone

= 1.3.2 - Released: 2017/04/27 =

* Minor CSS fixes
* Improved compatibility for WooCommerce 2 & 3
* Improved theme compatibility tests performance

= 1.3.1 - Released: 2017/04/26 =

* Fixed issues with promotional and estimate emails
* Improved  theme compatibility tests for shared hosts
* Improved compatibility for WooCommerce 2 & 3

= 1.3.0 - Released: 2017/04/24 =

* Fixed WPML string translations issue
* Added theme compatibility notices
* Wishlist custom item meta hidden from order
* Added compatibility with WooCommerce – Gift Cards

= 1.2.0 - Released: 2017/04/12 =

* WooCommerce 3.0+ support
* Added template overrides check for WooCommerce system report

= 1.1.3 - Released: 2017/04/04 =

* Fixed multiple issues with WPML support

= 1.1.2.11 - Released: 2017/03/16 =

* Fixed an issue when the Wishlist was not refreshed after the product is removed or added to the cart by the unauthenticated user.

= 1.1.2.10 - Released: 2017/03/03 =

* Fixed an issue with external products link

= 1.1.2.9 - Released: 2017/03/02 =

* The Setup Wizard enhancements
* Added new hooks for wishlist create|update|delete  and wishlist product add|update|remove events

= 1.1.2.8 - Released: 2017/02/26 =

* Fixed an issue with W3 Total Cache compatibility
* Added public functions

= 1.1.2.7 - Released: 2017/02/03 =

* Fixed an issue with "Add to Wishlist" function in a quick view popup (Compatibility with plugins that provide QuickView functionality)
* Added JavaScript alert for the "Add to Wishlist" button on a single product page when no variations are selected

= 1.1.2.6 - Released: 2017/01/27 =

* Fixed an issue when maximum 10 products can be added to cart from a Wishlist page using the "Add all to cart" button

= 1.1.2.5 - Released: 2017/01/27 =

* Fixed class loading critical error after plugin activation
* Added an option to show "Add to Wishlist" button on a catalogue page for variable products (if all default variations are set)

= 1.1.2.4 - Released: 2017/01/10 =

* Fixed issue with empty wishlist page
* Fixed issue with wrong product quantity on add to cart event from wishlist

= 1.1.2.3 - Released: 2016/12/09 =

* Fixed issues with pagination
* Added support for WordPress 4.7
* Removed Genericicons fonts

= 1.1.2.2 - Released: 2016/11/08 =

* Fixed issue with upgrade from free to the premium version
* Fixed issue with "Remove Wishlist" button

= 1.1.2.1 - Released: 2016/10/13 =

* Added reload a page when creating a new wishlist
* Fixed database error

= 1.1.2 - Released: 2016/10/12 =

* Added support for W3 Total Cache plugin
* Added support for WooCommerce - Gravity Forms Product Add-Ons
* Added minimized versions of JS

= 1.1.1 - Released: 2016/10/11 =

* Added support of WpP Super Cache plugin
* Fixed Text in Follow emails
* Fixed possible issue with the updater
* Fixed Period for sending notifications followers option
* Fixed field validator in email settings
* Fixed promotional emails preview
* Fixed disable of Follow functionality when disabling email in WooCommerce > Settings > Emails
* Fixed default colours for predefined promotional emails
* Follow wishlist button now visible for guests and suggests to login

= 1.1.0 - Released: 2016/09/22 =

* Added WP Rocket support
* Added promo email default skin values.
* Added filters for plugin options.
* Added social share and add to cart buttons to someone else wishlist page.
* Added theme font option.
* Added interception and output of ajax errors.
* Email heading and subject are now correctly applied to email.
* Moved modal windows to the footer.
* Fixed issue when the quantity in wishlist exceeds the number of products in stock.
* Fixed link in login modal after an error message.
* Fixed second predefined wishlist icon in a black colour not appear.
* Fixed subscribe issue.
* Fixed send estimate issue.
* Fixed plugin updater.
* Wishlist table footer is now hidden if all elements are disabled.
* A lot of minor fixes

= 1.0.0 - Released: 2016/09/09 =

* Initial release

== Upgrade Notice ==

= 1.40.1 =
Fixed security issue
