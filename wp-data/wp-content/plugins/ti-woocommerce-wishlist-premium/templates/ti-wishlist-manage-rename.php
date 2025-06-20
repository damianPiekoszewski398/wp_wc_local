<?php
/**
 * The Template for displaying dialog for rename wishlist this plugin.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-manage-rename.php.
 *
 * @version             1.9.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<span class="tinvwl-name-to-rename"><?php echo $wishlist_name; // WPCS: xss ok. ?></span>
<span class="tinvwl-rename-input" style="display:none;">
	<?php echo TInvWL_Form::_text( sprintf( 'wishlist_name[%d]', $wishlist_id ), $wishlist_value, array( 'class' => '' ) ); // WPCS: xss ok. ?>
</span>
<a href="javascript:void(0);" onclick="jQuery( this ).hide().prevAll( 'span' ).each( function () {
		jQuery( this ).toggle( jQuery( this ).is( ':hidden' ) ).find('input').focus();
	} );" class="tinvwl-rename-button"><i
			class="ftinvwl ftinvwl-pencil"></i><span><?php esc_html_e( 'Rename', 'ti-woocommerce-wishlist-premium' ) ?></span></a>
