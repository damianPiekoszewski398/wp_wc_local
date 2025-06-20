<?php
/**
 * Ask for Estimate create email
 *
 * @since             1.0.0
 * @package           TInvWishlist\Public
 * @subpackage          Email
 */

// If this file is called directly, abort.
if ( ! defined('ABSPATH')) {
	die;
}

/**
 * Ask for Estimate create email
 */
class TInvWL_Public_Email_Estimate extends WC_Email
{

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	public $_name;

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public $_version;

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	function __construct($plugin_name, $version)
	{
		$this->_name    = $plugin_name;
		$this->_version = $version;

		$this->settings_class = new TInvWL_Public_Email_Data_Estimate;

		$this->load_data();

		$this->set_templates();

		parent::__construct();
		add_filter('woocommerce_email_get_option', array($this, 'get_option_tinvwl'), 10, 4);
		add_filter('woocommerce_email_enabled_' . $this->id, array($this, 'enabled_tinvwl'), 10, 1);
	}


	/**
	 * Set template email
	 */
	function set_templates()
	{
		$emailtemplate = tinv_template_email(get_class($this));
		$this->set_template($emailtemplate);
	}

	/**
	 * Set template email
	 *
	 * @param string $emailtemplate Email template name.
	 */
	function set_template($emailtemplate = '')
	{
		$this->template_html  = $this->loadtemplates($this->template_name, $emailtemplate, false);
		$this->template_plain = $this->loadtemplates($this->template_name, $emailtemplate, true);
	}

	/**
	 * Get current template path
	 *
	 * @param string $template Name template.
	 * @param string $emailtemplate Name skin template.
	 * @param boolean $plain Plain or HTML template?.
	 *
	 * @return string
	 */
	function loadtemplates($template, $emailtemplate, $plain = false)
	{
		$curtemplate   = tinv_template();
		$template_name = 'emails' . DIRECTORY_SEPARATOR . ($plain ? 'plain' . DIRECTORY_SEPARATOR : '') . $template . $emailtemplate . '.php';
		if ( ! empty($curtemplate)) {
			if (file_exists(TINVWL_PATH . implode(DIRECTORY_SEPARATOR, array(
					'templates',
					$curtemplate,
					$template_name,
				)))) {
				return $template_name;
			}
		}
		if (file_exists(TINVWL_PATH . implode(DIRECTORY_SEPARATOR, array('templates', $template_name)))) {
			return $template_name;
		}

		return 'emails' . DIRECTORY_SEPARATOR . ($plain ? 'plain' . DIRECTORY_SEPARATOR : '') . $template . '.php';
	}

	/**
	 * Update status email
	 *
	 * @param boolean $value Woocommerce status.
	 *
	 * @return boolean
	 */
	function enabled_tinvwl($value)
	{
		$option_name = str_replace($this->_name . '_', '', $this->id);
		$_value      = tinv_get_option($option_name, 'enabled');
		if (is_null($_value)) {
			return $value;
		}

		return $_value;
	}

	/**
	 * It replaces the value to the value of the plugin
	 *
	 * @param mixed $value Set value.
	 * @param object $_this Object for validation id.
	 * @param mixed $_value New Value.
	 * @param string $key key field.
	 *
	 * @return mixed
	 */
	function get_option_tinvwl($value, $_this, $_value, $key)
	{
		if ($this->id === $_this->id) {
			$option_name = str_replace($this->_name . '_', '', $this->id);
			$_value      = tinv_get_option($option_name, $key);
			if (is_null($_value)) {
				return $value;
			}
			if (is_bool($_value)) {
				$_value = $_value ? 'yes' : 'no';
			}

			return $_value;
		}

		return $value;
	}

	/**
	 * Run method send mail
	 *
	 * @param array $wishlist Wishlist object.
	 * @param string $note Description for email.
	 *
	 * @return boolean
	 */
	public function trigger($wishlist, $note, $args)
	{
		if ( ! $this->is_enabled()) {
			return false;
		}
		if (empty($wishlist) || ! array_key_exists('ID', (array)$wishlist)) {
			return false;
		} else {
			$this->wishlist = $wishlist;
		}

		$this->heading = $this->get_option('heading');
		$this->subject = $this->get_option('subject');

		$this->notes = $note;

		// Get products fin this wishlist.
		$wlp = new TInvWL_Product($this->wishlist, $this->_name);

		$this->wishlist['products'] = $wlp->get_wishlist(array('count' => 9999999));

		// Get wishlist url.
		$this->wishlist['url'] = tinv_url_wishlist($wishlist['ID']);

		// Get user info.
		$user = get_user_by('id', $this->wishlist['author']);

		if ($user && $user->exists()) {
			$this->wishlist['author_display_name'] = $user->display_name;
			$this->wishlist['author_user_email']   = $user->user_email;
		} else {
			$this->wishlist['author_display_name'] = $args['name'];
			$this->wishlist['author_user_email']   = $args['email'];
		}

		// This sets the recipient to the settings defined below in init_form_fields().
		$this->recipient = $this->get_option('recipient');

		// If none was entered, just use the WP admin email as a fallback.
		if ( ! $this->recipient) {
			$this->recipient = get_option('admin_email');
		}

		// Dublicate copy for user.
		$this->copy = $this->get_option('copy');
		$this->copy = 'yes' === $this->copy;
		$this->args = $args;

		$result = $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
		if (is_wp_error($result)) {
			do_action('tinvwl_estimate_email_error', $this->get_recipient(), $user, $this->wishlist, $result->get_error_message());
		} else {
			/* Run a 3rd party code when estimate email sent.
			 *
			 * @param string $this->get_recipient() email recipient(s).
			 * @param WP_User $user WordPress user object of wishlist owner.
			 * @param array $this->wishlist current wishlist data.
			 *
			 * */
			do_action('tinvwl_estimate_email_successfully', $this->get_recipient(), $user, $this->wishlist);
		}
		return $result;
	}

	/**
	 * Update header for Send CC copy
	 *
	 * @return string
	 */
	function get_headers()
	{
		$headers = array('Reply-to: ' . $this->wishlist['author_user_email']);
		if ($this->copy) {
			$headers[] = 'Cc: ' . $this->wishlist['author_user_email'];
		}
		$headers[] = 'Content-Type: ' . $this->get_content_type();
		$headers   = implode("\r\n", $headers);

		return apply_filters('woocommerce_email_headers', $headers, $this->id, $this);
	}

	/**
	 * Get content html function
	 *
	 * @return string
	 */
	public function get_content_html()
	{
		ob_start();
		tinv_wishlist_template($this->template_html, apply_filters('tinvwl_estimate_email_data_template_html', array(
			'wishlist'             => $this->wishlist,
			'additional_note'      => $this->notes,
			'additional_arguments' => $this->args,
			'wishlist_table_row'   => tinv_get_option('product_table'),
			'email_heading'        => $this->get_heading(),
			'blogname'             => $this->get_blogname(),
			'sent_to_admin'        => true,
			'plain_text'           => false,
			'email'                => $this,
		)));

		return ob_get_clean();
	}

	/**
	 * Get content plain function
	 *
	 * @return string
	 */
	public function get_content_plain()
	{
		ob_start();
		tinv_wishlist_template($this->template_plain, apply_filters('tinvwl_estimate_email_data_template_plain', array(
			'wishlist'             => $this->wishlist,
			'additional_note'      => $this->notes,
			'additional_arguments' => $this->args,
			'wishlist_table_row'   => tinv_get_option('product_table'),
			'email_heading'        => $this->get_heading(),
			'blogname'             => $this->get_blogname(),
			'sent_to_admin'        => true,
			'plain_text'           => true,
			'email'                => $this,
		)));

		return ob_get_clean();
	}

	/**
	 * Set email defaults
	 */
	function load_data()
	{
		$this->id          = $this->settings_class->id;
		$this->title       = $this->settings_class->title;
		$this->description = $this->settings_class->description;

		$this->heading = $this->settings_class->heading;
		$this->subject = $this->settings_class->subject;

		$this->template_name = $this->settings_class->template_name;

		// Trigger on new paid orders.
		add_action('tinvwl_send_ask_for_estimate', array($this, 'trigger'), 10, 3);
	}

	/**
	 * Initialise Settings Form Fields
	 */
	public function init_form_fields()
	{
		$this->form_fields = $this->settings_class->form_fields;
	}

	/**
	 * Save value to plugin
	 */
	function process_admin_options()
	{
		parent::process_admin_options();

		$option_name = str_replace($this->_name . '_', '', $this->id);
		$post_data   = $this->get_post_data();

		foreach ($this->get_form_fields() as $key => $field) {
			try {
				$value = $this->get_field_value($key, $field, $post_data);
				if ('checkbox' === $this->get_field_type($field)) {
					$value = 'yes' === $value;
				}
				tinv_update_option($option_name, $key, $value);
			} catch (Exception $e) {
				$this->add_error($e->getMessage());
			}
		}

		$option_name = str_replace($this->_name . '_', '', $this->id);
		$enabled     = tinv_get_option($option_name, 'enabled');
		tinv_update_option('estimate_button', 'allow', $enabled);
	}

}
