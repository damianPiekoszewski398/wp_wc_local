<?php
/**
 * Basic admin style helper class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Helper
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Basic admin style helper class
 */
abstract class TInvWL_Admin_BaseStyle extends TInvWL_Admin_BaseSection {

	/**
	 * Create AJAX method
	 */
	function load_function() {
		parent::load_function();
		add_action( 'wp_ajax_selecttemplate', array( $this, 'load_settings_template' ) );
	}

	/**
	 * Render Style form template
	 */
	function load_settings_template() {
		$templates = $this->get_templates();
		$template  = filter_input( INPUT_POST, 'selected', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( in_array( $template, $templates ) ) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
			$template = '';
		}
		$sections = $this->prepare_sections( $template );
		$view     = new TInvWL_ViewSection( TINVWL_PREFIX, $this->_version );
		$view->load_data( $sections );
		$view->load_value( $this->constructor_load( $sections ) );
		$content = $view->Run( false );
		$content = str_replace( array( $this->start_form( '' ), $this->end_form( '' ) ), '', $content );
		echo $content; // WPCS: xss ok.
		wp_die();
	}

	/**
	 * Prepare sections for template attributes
	 *
	 * @param string $template Template name.
	 *
	 * @return array
	 */
	function prepare_sections( $template = '' ) {
		$fields      = array();
		$fields_data = array();
		if ( empty( $template ) ) {
			$fields     = $this->default_style_settings();
			$theme_file = TINVWL_PATH . implode( DIRECTORY_SEPARATOR, array( 'assets', 'css', 'theme.css' ) );
			if ( file_exists( $theme_file ) ) {
				$fields_data = $this->break_css( file_get_contents( $theme_file ) ); // @codingStandardsIgnoreLine WordPress.VIP.RestrictedFunctions.file_get_contents
			}
		} else {
			$theme_file = TINVWL_PATH . implode( DIRECTORY_SEPARATOR, array( 'templates', $template, 'index-ti.php' ) );
			if ( file_exists( $theme_file ) ) {
				include $theme_file;
			}
			$theme_file = TINVWL_PATH . implode( DIRECTORY_SEPARATOR, array( 'templates', $template, 'theme.css' ) );
			if ( file_exists( $theme_file ) ) {
				$fields_data = $this->break_css( file_get_contents( $theme_file ) ); // @codingStandardsIgnoreLine WordPress.VIP.RestrictedFunctions.file_get_contents
			}
		}
		$_fields = $this->prepare_fields( $fields, $fields_data );
		foreach ( $_fields as &$_field ) {
			if ( ! array_key_exists( 'skin', $_field ) ) {
				switch ( $_field['type'] ) {
					case 'group':
					case 'groupHTML':
						$_field['skin'] = 'section-group-style';
						break;
					default:
						$_field['skin'] = 'section-field-style';
						break;
				}
			}
		}
		$sections = array( $this->template_options( $_fields, $template ) );

		return $sections;
	}

	/**
	 * Create Scetions for this settings
	 *
	 * @return array
	 */
	function constructor_data() {
		$templates = $this->get_templates();
		$templates = tinv_array_merge( array(
			'' => __( 'Default', 'ti-woocommerce-wishlist-premium' ),
		), $templates );
		asort( $templates );

		return array_merge( $this->before_style_settings( $templates ), array( $this->template_options( array() ) ), $this->after_style_settings() );
	}

	/**
	 * Basic function for default theme fields
	 *
	 * @return array
	 */
	function default_style_settings() {
		return array();
	}

	/**
	 * Basic function for before fields theme settings
	 *
	 * @param array $templates Array of theme list.
	 *
	 * @return array
	 */
	function before_style_settings( $templates = array() ) {
		return array(
			array(
				'id'         => 'style',
				'title'      => __( 'Templates', 'ti-woocommerce-wishlist-premium' ),
				'desc'       => '',
				'show_names' => false,
				'fields'     => array(
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'customstyle',
						'text'  => __( 'Use Theme style', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'extra' => array( 'tiwl-hide' => '.tinvwl-style-options, .tinvwl-style-template>tr' ),
						'class' => 'tinvwl-header-row',
					),
					array(
						'type'  => 'group',
						'id'    => 'template',
						'class' => 'tinvwl-style-template',
						'style' => ( 1 < count( $templates ) ? '' : 'display:none;' ),
					),
					array(
						'type'    => 'select',
						'name'    => 'template',
						'text'    => __( 'Templates', 'ti-woocommerce-wishlist-premium' ),
						'std'     => '',
						'options' => $templates,
					),
				),
			),
		);
	}

	/**
	 * Basic function for after fields theme settings
	 *
	 * @return array
	 */
	function after_style_settings() {
		return array(
			array(
				'id'         => 'style_plain',
				'title'      => __( 'Custom CSS Rules', 'ti-woocommerce-wishlist-premium' ),
				'desc'       => '',
				'show_names' => false,
				'fields'     => array(
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'allow',
						'text'  => __( 'Custom CSS Rules', 'ti-woocommerce-wishlist-premium' ),
						'std'   => false,
						'extra' => array( 'tiwl-show' => '.tiwl-style-custom-allow' ),
						'class' => 'tinvwl-header-row',
					),
					array(
						'type'  => 'group',
						'id'    => 'custom',
						'class' => 'tiwl-style-custom-allow',
					),
					array(
						'type' => 'textarea',
						'name' => 'css',
						'text' => '',
						'std'  => '',
					),
				),
			),
			array(
				'id'     => 'save_buttons',
				'class'  => 'only-button',
				'noform' => true,
				'fields' => array(
					array(
						'type'  => 'button_submit',
						'name'  => 'setting_save',
						'std'   => '<span><i class="ftinvwl ftinvwl-check"></i></span>' . __( 'Save Settings', 'ti-woocommerce-wishlist-premium' ),
						'extra' => array( 'class' => 'tinvwl-btn split status-btn-ok' ),
					),
					array(
						'type'  => 'button_submit',
						'name'  => 'setting_reset',
						'std'   => '<span><i class="ftinvwl ftinvwl-times"></i></span>' . __( 'Reset', 'ti-woocommerce-wishlist-premium' ),
						'extra' => array( 'class' => 'tinvwl-btn split status-btn-ok tinvwl-confirm-reset' ),
					),
					array(
						'type' => 'button_submit_quick',
						'name' => 'setting_save_quick',
						'std'  => '<span><i class="ftinvwl ftinvwl-floppy-o"></i></span>' . __( 'Save', 'ti-woocommerce-wishlist-premium' ),
					),
				),
			),
		);
	}

	/**
	 * Get Templates
	 *
	 * @return array
	 */
	function get_templates() {
		$paths = glob( TINVWL_PATH . 'templates' . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR );
		foreach ( $paths as $i => $path ) {
			if ( ! file_exists( $path . DIRECTORY_SEPARATOR . 'index-ti.php' ) ) {
				unset( $paths[ $i ] );
			}
		}
		$templates = array();
		foreach ( $paths as $path ) {
			$path      = basename( $path );
			$path_name = str_replace( '_', ' ', $path );
			$path_name = explode( ' ', $path_name );
			foreach ( $path_name as &$_path_name ) {
				$_path_name = ucfirst( $_path_name );
			}
			$path_name          = implode( ' ', $path_name );
			$templates[ $path ] = $path_name;
		}

		return $templates;
	}

	/**
	 * Create Template Options for themes
	 *
	 * @param array $fields Array of style fields.
	 *
	 * @return array
	 */
	function template_options( $fields = array(), $template = '' ) {
		return array(
			'id'         => 'style_options' . $template,
			'title'      => __( 'Template Options', 'ti-woocommerce-wishlist-premium' ),
			'show_names' => true,
			'class'      => 'tinvwl-style-options',
			'fields'     => $fields,
			'skin'       => 'section-general',
		);
	}

	/**
	 * Prepare style fields for sections fields
	 *
	 * @param array $fields Array of fields list.
	 * @param array $data Array of default values for fields.
	 *
	 * @return array
	 */
	function prepare_fields( $fields = array(), $data = array() ) {
		foreach ( $fields as &$field ) {
			if ( ! array_key_exists( 'selector', $field ) || ! array_key_exists( 'element', $field ) ) {
				continue;
			}
			$field['name'] = self::create_selectorkey( $field['selector'], $field['element'] );
			if ( ! array_key_exists( 'std', $field ) ) {
				$field['std'] = '';
			}
			if ( isset( $data[ $field['selector'] ][ $field['element'] ] ) ) {
				$value = $data[ $field['selector'] ][ $field['element'] ];
				if ( array_key_exists( 'format', (array) $field ) ) {
					$pregx = preg_replace( '/(\[|\]|\\|\/|\^|\$|\%|\.|\||\?|\*|\+|\(|\)|\{|\})/', '\\\${1}', $field['format'] );
					$pregx = str_replace( '\{0\}', '(.*?)', $pregx );
					$pregx = '/^' . $pregx . '$/i';
					if ( preg_match( $pregx, $value, $matches ) ) {
						if ( isset( $matches[1] ) ) {
							$field['std'] = trim( $matches[1] );
							$field['std'] = preg_replace( '/^\.\.\//', TINVWL_URL . 'assets/', $field['std'] );
						}
					}
				} else {
					$field['std'] = $value;
				}
			}
			if ( 'transparent' === $field['std'] ) {
				$field['std'] = 'rgba(0,0,0,0)';
			}
			unset( $field['selector'], $field['element'], $field['format'] );
		}

		return $fields;
	}

	/**
	 * Save value to database
	 *
	 * @param array $data Post section data.
	 *
	 * @return boolean
	 */
	function constructor_save( $data ) {
		if ( empty( $data ) || ! is_array( $data ) ) {
			return false;
		}
		if ( array_key_exists( 'style', (array) $data ) ) {
			if ( false === $data['style']['customstyle'] ) {
				$this->style_options_save( $data['style']['template'] );
			}
			delete_transient( $this->_name . '_dynamic_' . $data['style']['template'] );
			delete_transient( TINVWL_PREFIX . '_dynamicfont' );
		}
		if ( filter_input( INPUT_POST, 'save_buttons-setting_reset' ) ) {
			foreach ( array_keys( $data ) as $key ) {
				if ( in_array( $key, array( 'style_plain' ) ) ) {
					$data[ $key ] = array();
				}
			}
		}
		if ( array_key_exists( 'style_plain', (array) $data ) ) {
			if ( ! @$data['style_plain']['allow'] ) {
				$data['style_plain']['css'] = '';
			}
			if ( ! @$data['style_plain']['css'] ) {
				$data['style_plain']['allow'] = false;
			}
		}

		parent::constructor_save( $data );
		if ( ! tinv_get_option( 'style', 'customstyle' ) && filter_input( INPUT_POST, 'save_buttons-setting_reset' ) ) {
			if ( array_key_exists( 'style', $data ) ) {
				$template = isset( $data['style']['template'] ) ? $data['style']['template'] : tinv_template();
				tinv_update_option( 'style_options' . $template, '', array() );
				delete_transient( $this->_name . '_dynamic_' . $template );
				delete_transient( TINVWL_PREFIX . '_dynamicfont' );
			}
		}
	}

	/**
	 * Save style options
	 *
	 * @param string $template Template name.
	 */
	function style_options_save( $template = '' ) {
		$sections = $this->prepare_sections( $template );
		$view     = new TInvWL_ViewSection( $this->_name, $this->_version );
		$view->load_data( $sections );
		$data = $view->post_form();
		$this->constructor_save( $data );
		if ( array_key_exists( 'style_options' . $template, (array) $data ) ) {
			self::convert_styles( $template );
		}
	}

	/**
	 * Generate fields name for form
	 *
	 * @param string $selector Selector for fields.
	 * @param string $element Attribute name.
	 *
	 * @return string
	 */
	public static function create_selectorkey( $selector, $element ) {
		return md5( $selector . '||' . $element );
	}

	/**
	 * Create array of css attributes
	 *
	 * @param string $css CSS content.
	 *
	 * @return array
	 */
	function break_css( $css ) {
		$results = array();
		$css     = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		$css     = preg_replace( '/(\r|\n|\t| {2,})/', '', $css );
		$css     = str_replace( array( '{', '}' ), array( ' { ', ' } ' ), $css );
		preg_match_all( '/(.+?)\s*?\{\s*?(.+?)\s*?\}/', $css, $matches );
		foreach ( array_keys( $matches[0] ) as $i ) {
			foreach ( explode( ';', $matches[2][ $i ] ) as $attr ) {
				if ( strlen( trim( $attr ) ) > 0 ) {
					list( $name, $value ) = explode( ':', $attr );
					$results[ trim( $matches[1][ $i ] ) ][ trim( $name ) ] = trim( $value );
				}
			}
		}

		return $results;
	}

	/**
	 * Convert settings to css
	 *
	 * @param string $template Name template.
	 *
	 * @return string
	 */
	public static function convert_styles( $template = '' ) {
		$fields = array();
		if ( empty( $template ) ) {
			$c      = new TInvWL_Admin_Settings_Style( TINVWL_PREFIX, TINVWL_VERSION );
			$fields = $c->default_style_settings();
		} else {
			$theme_file = TINVWL_PATH . implode( DIRECTORY_SEPARATOR, array( 'templates', $template, 'index-ti.php' ) );
			if ( file_exists( $theme_file ) ) {
				include $theme_file;
			}
		}
		$style  = (array) tinv_get_option( 'style_options' . $template );
		$styles = array();
		foreach ( $fields as $field ) {
			if ( ! array_key_exists( 'selector', $field ) || ! array_key_exists( 'element', $field ) ) {
				continue;
			}
			$key = self::create_selectorkey( $field['selector'], $field['element'] );
			if ( array_key_exists( $key, (array) $style ) ) {
				$value = $style[ $key ];
				if ( array_key_exists( 'format', $field ) ) {
					$value = str_replace( '{0}', $value, $field['format'] );
				}
				if ( array_key_exists( 'important', $field ) && $field['important'] ) {
					$value .= ' !important';
				}
				$styles[ $field['selector'] ][ $field['element'] ] = $value;
				if ( array_key_exists( 'subelement', $field ) ) {
					if ( is_array( $field['subelement'] ) ) {
						foreach ( $field['subelement'] as $subelement ) {
							$styles[ $field['selector'] ][ $subelement ] = $value;
						}
					} else {
						$styles[ $field['selector'] ][ $field['subelement'] ] = $value;
					}
				}
			}
		}
		foreach ( $styles as &$elements ) {
			$elements = array_filter( $elements );
		}
		$styles = array_filter( $styles );
		foreach ( $styles as $selector => &$elements ) {
			foreach ( $elements as $key => &$element ) {
				$element = sprintf( '%s:%s;', $key, $element );
			}
			$elements = implode( '', $elements );
			$elements = sprintf( '%s {%s}', $selector, $elements );
		}
		$styles = implode( ' ', $styles );
		tinv_update_option( 'style_options' . $template, 'css', $styles );

		return $styles;
	}
}
