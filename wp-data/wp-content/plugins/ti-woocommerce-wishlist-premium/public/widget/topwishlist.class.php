<?php
/**
 * Widget "Popular product"
 *
 * @since             1.0.0
 * @package           TInvWishlist\Widget
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die;
}

/**
 * Widget "Popular product"
 */
class TInvWL_Public_Widget_Topwishlist extends WC_Widget
{

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->widget_cssclass = 'tinvwl widget_wishlist_products_counter';
		$this->widget_description = __('Displays the number of products in the wishlist on your site.', 'ti-woocommerce-wishlist-premium');
		$this->widget_id = 'widget_top_wishlist';
		$this->widget_name = __('TI Wishlist Products Counter', 'ti-woocommerce-wishlist-premium');
		$this->settings = array(
			'show_icon' => array(
				'type' => 'checkbox',
				'std' => ((bool)tinv_get_option('topline', 'icon')) ? 1 : 0,
				'label' => __('Show counter icon', 'ti-woocommerce-wishlist-premium'),
			),
			'link' => array(
				'type' => 'checkbox',
				'std' => tinv_get_option('topline', 'link') ? 1 : 0,
				'label' => __('Add link for counter title', 'ti-woocommerce-wishlist-premium'),
			),
			'show_text' => array(
				'type' => 'checkbox',
				'std' => tinv_get_option('topline', 'show_text') ? 1 : 0,
				'label' => __('Show counter text', 'ti-woocommerce-wishlist-premium'),
			),
			'text' => array(
				'type' => 'text',
				'std' => apply_filters('tinvwl_wishlist_products_counter_text', tinv_get_option('topline', 'text')),
				'label' => __('Counter Text', 'ti-woocommerce-wishlist-premium'),
			),
			'show_counter' => array(
				'type' => 'checkbox',
				'std' => tinv_get_option('topline', 'counter') ? 1 : 0,
				'label' => __('Show counter', 'ti-woocommerce-wishlist-premium'),
			),
			'drop_down' => array(
				'type' => 'checkbox',
				'std' => tinv_get_option('topline', 'drop_down') ? 1 : 0,
				'label' => __('Show dropdown with mini wishlist', 'ti-woocommerce-wishlist-premium'),
			),
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @param array $args Artguments.
	 * @param array $instance Instance.
	 */
	public function widget($args, $instance)
	{
		if (!is_user_logged_in() && !tinv_get_option('general', 'guests')) {
			return;
		}

		if ($this->get_cached_widget($args)) {
			return;
		}

		foreach ($instance as $key => $value) {
			if ('on' === $value) {
				$instance[$key] = 1;
			}
		}

		$this->widget_start($args, $instance);
		$content = tinvwl_shortcode_products_counter(array(
			'show_icon' => isset($instance['show_icon']) ? absint($instance['show_icon']) : $this->settings['show_icon']['std'],
			'show_text' => isset($instance['show_text']) ? absint($instance['show_text']) : $this->settings['show_text']['std'],
			'text' => isset($instance['text']) ? $instance['text'] : $this->settings['text']['std'],
			'show_counter' => isset($instance['show_counter']) ? absint($instance['show_counter']) : $this->settings['show_counter']['std'],
			'drop_down' => isset($instance['drop_down']) ? absint($instance['drop_down']) : $this->settings['drop_down']['std'],
			'link' => isset($instance['link']) ? absint($instance['link']) : $this->settings['link']['std'],
		));
		echo $content; // WPCS: xss ok.
		$this->widget_end($args, $instance);
		$this->cache_widget($args, $content);
	}
}
