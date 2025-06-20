<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * @class TINVWL_Trigger_Wishlist_Item_Sale
 */
class TINVWL_Trigger_Wishlist_Item_Sale extends AutomateWoo\Trigger
{

	/**
	 * @var array
	 */
	public $supplied_data_items = array('customer', 'product', 'wishlist');

	/**
	 *
	 */
	function load_admin_details()
	{
		$this->title = sprintf(__('Wishlist Item On Sale (TI WooCommerce Wishlist)', 'ti-woocommerce-wishlist-premium'));
		$this->group = __('Wishlists', 'ti-woocommerce-wishlist-premium');
	}


	/**
	 *
	 */
	function register_hooks()
	{
		add_action('tinvwl_send_notification_price', array($this, 'catch_hooks'), 10, 2);
	}

	/**
	 * @param $_product
	 * @param $user_id
	 * @param $wishlists
	 */
	function catch_hooks($_product, $user_id)
	{
		if (!$this->has_workflows()) {
			return;
		}

		$wishlist = new TINVWL_AutomateWoo_Wishlist();
		$wishlist->id = $data['wishlist_id'];
		$wishlist->owner_id = $data['author'];

		$this->maybe_run(array(
			'customer' => AutomateWoo\Customer_Factory::get_by_user_id($user_id),
			'wishlist' => $wishlist,
			'product' => $_product,
		));
	}

	/**
	 * @param $workflow Workflow
	 *
	 * @return bool
	 */
	function validate_workflow($workflow)
	{
		if (!$this->validate_field_user_pause_period($workflow)) {
			return false;
		}

		return true;
	}


	/**
	 * @param Workflow $workflow
	 *
	 * @return bool
	 */
	function validate_before_queued_event($workflow)
	{
		$product = $workflow->data_layer()->get_product();

		if (!$product->is_on_sale()) {
			return false; // check product is still on sale
		}

		return true;
	}

}
