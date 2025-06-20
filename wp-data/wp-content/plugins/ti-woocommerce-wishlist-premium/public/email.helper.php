<?php
/**
 * Basic email class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Public
 * @subpackage          Email
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Basic email class
 */
class TInvWL_Public_Email {

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
	 * This class
	 *
	 * @var \TInvWL_Public_TInvWL
	 */
	protected static $_instance = null;

	/**
	 * @var
	 */
	public $parent_settings;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 *
	 * @return \TInvWL_Public_TInvWL
	 */
	public static function instance( $plugin_name = TINVWL_PREFIX, $version = TINVWL_VERSION ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $plugin_name, $version );
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	function __construct( $plugin_name, $version ) {
		$this->_name    = $plugin_name;
		$this->_version = $version;
		$this->add_settings();

		add_filter( 'woocommerce_email_classes', array( $this, 'add_emails' ) );
	}

	/**
	 * Add email list object
	 */
	public function add_emails( $email_classes ) {
		$email_classes[ $this->_name . '_Public_Email_Estimate' ]             = new TInvWL_Public_Email_Estimate( $this->_name, $this->_version );
		$email_classes[ $this->_name . '_Public_Email_Promotional' ]          = new TInvWL_Public_Email_Promotional( $this->_name, $this->_version );
		$email_classes[ $this->_name . '_Public_Email_NotificationPrice' ]    = new TInvWL_Public_Email_NotificationPrice( $this->_name, $this->_version );
		$email_classes[ $this->_name . '_Public_Email_NotificationStock' ]    = new TInvWL_Public_Email_NotificationStock( $this->_name, $this->_version );
		$email_classes[ $this->_name . '_Public_Email_NotificationLowStock' ] = new TInvWL_Public_Email_NotificationLowStock( $this->_name, $this->_version );
		$email_classes[ $this->_name . '_Public_Email_Subscribe' ]            = new TInvWL_Public_Email_Subscribe( $this->_name, $this->_version );

		return $email_classes;
	}

	/**
	 *
	 */
	public function add_settings() {
		$this->parent_settings[ $this->_name . '_Public_Email_Estimate' ]             = new TInvWL_Public_Settings( 'TInvWL_Public_Email_Data_Estimate', $this->_version, $this->_name );
		$this->parent_settings[ $this->_name . '_Public_Email_Promotional' ]          = new TInvWL_Public_Settings( 'TInvWL_Public_Email_Data_Promotional', $this->_version, $this->_name );
		$this->parent_settings[ $this->_name . '_Public_Email_NotificationPrice' ]    = new TInvWL_Public_Settings( 'TInvWL_Public_Email_Data_NotificationPrice', $this->_version, $this->_name );
		$this->parent_settings[ $this->_name . '_Public_Email_NotificationStock' ]    = new TInvWL_Public_Settings( 'TInvWL_Public_Email_Data_NotificationStock', $this->_version, $this->_name );
		$this->parent_settings[ $this->_name . '_Public_Email_NotificationLowStock' ] = new TInvWL_Public_Settings( 'TInvWL_Public_Email_Data_NotificationLowStock', $this->_version, $this->_name );
	}

	/**
	 * Set template email
	 */
	function set_templates() {
		$emailtemplate = tinv_template_email( get_class( $this ) );
		$this->set_template( $emailtemplate );
	}

	/**
	 * Set template email
	 *
	 * @param string $emailtemplate Email template name.
	 */
	function set_template( $emailtemplate = '' ) {
		$this->template_html  = $this->loadtemplates( $this->template_name, false, $emailtemplate );
		$this->template_plain = $this->loadtemplates( $this->template_name, true, $emailtemplate );
	}

	/**
	 * Get list templates
	 *
	 * @param string $template Name template.
	 *
	 * @return array
	 */
	public static function templates( $template ) {
		$paths = array(
			TINVWL_PATH . implode( DIRECTORY_SEPARATOR, array( 'templates', 'emails', $template ) ),
		);

		$curtemplate = tinv_template();
		if ( ! empty( $curtemplate ) ) {
			array_unshift( $paths, TINVWL_PATH . implode( DIRECTORY_SEPARATOR, array(
					'templates',
					$curtemplate,
					'emails',
					$template,
				) ) );
		}
		$templates = array();
		foreach ( $paths as $path ) {
			$template_paths = glob( $path . '*.php' );
			foreach ( $template_paths as $value ) {
				$value = preg_replace( '/(^' . $template . '|\.php$)/i', '', basename( $value ) );

				$templates[ $value ] = empty( $value ) ? __( 'Default', 'ti-woocommerce-wishlist-premium' ) : ucfirst( $value );
			}
		}
		$templates = array_unique( $templates );
		asort( $templates );

		return apply_filters( 'tinvwl_get_list_email_templates', $templates, $template );
	}

	/**
	 * Get list templates
	 *
	 * @return array
	 */
	public static function gettemplates( $name ) {
		return self::templates( $name );
	}

}
