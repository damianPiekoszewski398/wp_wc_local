<?php
/**
 * The Template for displaying dropdown wishlist products in topline.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-product-counter.php.
 *
 * @version             2.3.0
 * @package           TInvWishlist\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( $icon_class && 'custom' === $icon && ! empty( $icon_upload ) ) {
	$text = sprintf( '<img src="%s" /> %s', esc_url( $icon_upload ), $text );
}
$class = TInvWL_Public_WishlistCounter::instance();
?>

<div
	class="wishlist_products_counter wishlist_products_counter_dropdown<?php echo ' ' . $icon_class . ' ' . $icon_style . ( empty( $text ) ? ' no-txt' : '' ); // WPCS: xss ok.     ?>">
	<?php if ( $use_link ) :
	$counter = $class->get_counter();
	?>
	<a href="<?php echo esc_url( tinv_url_wishlist_default() ); ?>"
	   name="<?php echo esc_attr( sanitize_title( $text ) ); ?>" aria-label="<?php echo esc_attr( $text ); ?>"
	   class="wishlist_products_counter<?php echo ' ' . $icon_class . ' ' . $icon_style . ( empty( $text ) ? ' no-txt' : '' ) . ( 0 < $counter ? ' wishlist-counter-with-products' : '' ); // WPCS: xss ok.
	   ?>">
		<?php endif; ?>
		<i class="wishlist-icon"></i>
		<?php if ( $text ) : ?>
			<span class="wishlist_products_counter_text"><?php echo $text; // WPCS: xss ok. ?></span>
		<?php endif; ?>
		<?php if ( $show_counter ) : ?>
			<span class="wishlist_products_counter_number"></span>
		<?php endif; ?>
		<?php if ( $use_link ) : ?>
	</a>
<?php endif; ?>
	<?php if ( $drop_down ) : ?>
		<div class="wishlist_products_counter_wishlist widget_wishlist"
			 style="display:none; opacity: 0;">
			<div class="widget_wishlist_content">
				<div class="tinv_mini_wishlist_list"><?php echo $class->mini_wishlist(); ?></div>
			</div>
		</div>
	<?php endif; ?>
</div>
