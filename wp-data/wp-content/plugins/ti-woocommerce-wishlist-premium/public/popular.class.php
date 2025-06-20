<?php
/**
 * Popular list wishlists shortcode
 *
 * @since             1.0.0
 * @package           TInvWishlist\Popular
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die;
}

/**
 * Popular list wishlists shortcode
 */
class TInvWL_Public_Popular
{

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $_name;

	/**
	 * This class
	 *
	 * @var \TInvWL_Public_Popular
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \TInvWL_Public_Popular
	 */
	public static function instance($plugin_name = TINVWL_PREFIX)
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self($plugin_name);
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 */
	function __construct($plugin_name)
	{
		$this->_name = $plugin_name;
		$this->define_hooks();
	}

	/**
	 * Defined shortcode and hooks
	 */
	function define_hooks()
	{
	}

	/**
	 * Output page
	 *
	 * @param array $atts Array parameter for shortcode.
	 */
	function htmloutput($atts)
	{
		$wl = new TInvWL_Wishlist($this->_name);
		$paged = absint(get_query_var('wl_paged', 1));
		$paged = 1 < $paged ? $paged : 1;

		$data = array(
			'status' => 'public',
			'order_by' => 'B`.`visite',
			'order' => 'DESC',
			'count' => absint($atts['lists_per_page']),
			'offset' => absint($atts['lists_per_page']) * ($paged - 1),
		);
		global $wpdb;
		$data['sql'] = 'SELECT `A`.*, `B`.`visite` FROM `{table}` AS `A` LEFT JOIN `' . $wpdb->prefix . TINVWL_PREFIX . '_analytics` AS `B` ON `A`.`ID` = `B`.`wishlist_id` AND `B`.`product_id` = 0 WHERE `A`.`status`="public" GROUP BY `A`.`ID`';
		$pages = ceil(count($wl->get($data)) / absint($atts['lists_per_page']));
		$data['sql'] .= ' ORDER BY `{order_by}` {order} LIMIT {offset},{count};';
		if ('yes' === $atts['show_navigation']) {
			if (1 < $paged) {
				add_action('tinvwl_pagenation_wishlistpublic_table', array($this, 'page_prev'));
			}
			if ($pages > $paged) {
				add_action('tinvwl_pagenation_wishlistpublic_table', array($this, 'page_next'));
			}
		}

		$wishlists = $wl->get($data);

		$data = array(
			'wishlists' => $wishlists,
		);
		tinv_wishlist_template('ti-wishlist-public.php', $data);
		remove_action('tinvwl_pagenation_wishlistpublic_table', array($this, 'page_prev'));
		remove_action('tinvwl_pagenation_wishlistpublic_table', array($this, 'page_next'));
	}

	/**
	 * Prev page button
	 */
	function page_prev()
	{
		$paged = absint(get_query_var('wl_paged', 1));
		$paged = 1 < $paged ? $paged - 1 : 0;
		$this->page($paged, sprintf('<i class="ftinvwl ftinvwl-chevron-left"></i>%s', __('Previous Page', 'ti-woocommerce-wishlist-premium')), array('class' => 'button tinv-prev'));
	}

	/**
	 * Next page button
	 */
	function page_next()
	{
		$paged = absint(get_query_var('wl_paged', 1));
		$paged = 1 < $paged ? $paged + 1 : 2;
		$this->page($paged, sprintf('%s<i class="ftinvwl ftinvwl-chevron-right"></i>', __('Next Page', 'ti-woocommerce-wishlist-premium')), array('class' => 'button tinv-next'));
	}

	/**
	 * Page button
	 *
	 * @param integer $paged Index page.
	 * @param string $text Text button.
	 * @param style $style Style attribute.
	 */
	function page($paged, $text, $style = array())
	{
		$paged = absint($paged);
		if (is_array($style)) {
			$style = TInvWL_Form::__atrtostr($style);
		}
		$link = get_permalink();
		if (get_option('permalink_structure')) {
			$link .= 'page/' . $paged;
		} else {
			$link .= '&paged=' . $paged;
		}
		printf('<a href="%s" %s>%s</a>', esc_url($link), $style, $text); // WPCS: xss ok.
	}

	/**
	 * Shortcode basic function
	 *
	 * @param array $atts Array parameter from shortcode.
	 *
	 * @return string
	 */
	function shortcode($atts = array())
	{
		$default = array(
			'lists_per_page' => 10,
			'show_navigation' => 'yes',
		);
		$atts = shortcode_atts($default, $atts);

		ob_start();
		$this->htmloutput($atts);

		return ob_get_clean();
	}
}
