<?php
/**
 * Template for PDF export of wishlist.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-pdf.php.
 *
 * @package TI_WooCommerce_Wishlist
 * @version 2.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template variables.
 *
 * @var array $wishlist Wishlist data.
 * @var array $wishlist_table_row Wishlist table row data.
 * @var string $total Total value.
 * @var array $products Products.
 * @var boolean $qty If quantity feature enabled.
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> >

<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<title><?php echo esc_html( apply_filters( 'tinvwl_wishlist_header_title', $wishlist['title'], $wishlist ) ); ?></title>
	<style>
		* {
			font-family: sans-serif;
			color: #333;
		}

		div.heading {
			text-align: center;
		}

		h1 {
			font-size: 60px;
			margin-bottom: 0;
		}

		#tagline {
			color: #444;
			font-size: 20px;
		}

		h2 {
			font-size: 40px;
			text-align: center;
			margin-bottom: 50px;
		}

		table.tinvwl-table-manage-list {
			width: 100%;
			border-spacing: 0;
			border-radius: 5px;
		}

		table.tinvwl-table-manage-list tbody tr:nth-child(even) {
			background-color: #f9f9f9;
		}

		table.tinvwl-table-manage-list tr th,
		table.tinvwl-table-manage-list tr td {
			padding: 15px;
		}

		table.tinvwl-table-manage-list tr th {
			border-bottom: 2px solid #ebebeb;
		}

		table.tinvwl-table-manage-list tr .product-price,
		table.tinvwl-table-manage-list tr .product-quantity,
		table.tinvwl-table-manage-list tr .product-name,
		table.tinvwl-table-manage-list tr .product-date,
		table.tinvwl-table-manage-list tr .product-stock,
		table.tinvwl-table-manage-list tr .product-subtotal,
		p.wishlist-empty {
			text-align: center;
		}

		div.wishlist-total {
			text-align: right;
			padding: 50px;
		}

		table.tinvwl-table-manage-list td.product-thumbnail a {
			display: block;
		}

		table.tinvwl-table-manage-list td.product-thumbnail img {
			max-width: 100px;
			height: auto;
		}
	</style>
</head>
<body>
<div class="heading">
	<div id="logo">
		<h1><?php echo esc_html( get_option( 'blogname' ) ); ?></h1>
	</div>
	<div id="tagline"><?php echo esc_html( get_option( 'blogdescription' ) ); ?></div>
</div>
<?php do_action( 'tinvwl_wishlist_pdf_before_title', $wishlist, $products ); ?>
<div class="tinv-header">
	<h2><?php echo wp_kses_post( apply_filters( 'tinvwl_wishlist_header_title', $wishlist['title'], $wishlist ) ); ?></h2>
</div>
<?php do_action( 'tinvwl_wishlist_pdf_before_products_table', $wishlist, $products ); ?>

<?php if ( $products ): ?>
	<table class="tinvwl-table-manage-list">
		<thead>
		<tr>
			<?php if ( isset( $wishlist_table_row['colm_image'] ) && $wishlist_table_row['colm_image'] ) : ?>
				<th class="product-thumbnail">&nbsp;</th>
			<?php endif; ?>
			<th class="product-name"><?php esc_html_e( 'Product Name', 'ti-woocommerce-wishlist-premium' ); ?></th>
			<?php if ( isset( $wishlist_table_row['colm_price'] ) && $wishlist_table_row['colm_price'] ) : ?>
				<th class="product-price"><?php esc_html_e( 'Unit Price', 'ti-woocommerce-wishlist-premium' ); ?></th>
			<?php endif; ?>
			<?php if ( isset( $wishlist_table_row['colm_date'] ) && $wishlist_table_row['colm_date'] ) : ?>
				<th class="product-date"><?php esc_html_e( 'Date Added', 'ti-woocommerce-wishlist-premium' ); ?></th>
			<?php endif; ?>
			<?php if ( isset( $wishlist_table_row['colm_stock'] ) && $wishlist_table_row['colm_stock'] ) : ?>
				<th class="product-stock"><?php esc_html_e( 'Stock Status', 'ti-woocommerce-wishlist-premium' ); ?></th>
			<?php endif; ?>
			<?php if ( $qty && isset( $wishlist_table_row['colm_quantity'] ) && $wishlist_table_row['colm_quantity'] ) : ?>
				<th class="product-quantity"><?php esc_html_e( 'Qty', 'ti-woocommerce-wishlist-premium' ); ?></th>
			<?php endif; ?>
			<?php if ( isset( $wishlist_table_row['subtotal'] ) && $wishlist_table_row['subtotal'] ) : ?>
				<th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'ti-woocommerce-wishlist-premium' ); ?></th>
			<?php endif; ?>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ( $products as $wl_product ) {
			if ( empty( $wl_product['data'] ) ) {
				continue;
			}

			$product = apply_filters( 'tinvwl_wishlist_item', $wl_product['data'] );

			unset( $wl_product['data'] );
			if ( ( $wl_product['quantity'] > 0 || apply_filters( 'tinvwl_allow_zero_quantity', false ) ) && apply_filters( 'tinvwl_wishlist_item_visible', true, $wl_product, $product ) ) {
				$product_url = apply_filters( 'tinvwl_wishlist_item_url', $product->get_permalink(), $wl_product, $product );
				do_action( 'tinvwl_wishlist_row_before', $wl_product, $product );
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'tinvwl_wishlist_item_class', 'wishlist_item', $wl_product, $product ) ); ?>"
					data-tinvwl-pid="<?php echo esc_attr( $wl_product['ID'] ); ?>">
					<?php if ( isset( $wishlist_table_row['colm_image'] ) && $wishlist_table_row['colm_image'] ) : ?>
						<td class="product-thumbnail">
							<?php
							$thumbnail = apply_filters( 'tinvwl_wishlist_item_thumbnail', $product->get_image(), $wl_product, $product );

							if ( ! $product->is_visible() ) {
								echo $thumbnail; // WPCS: XSS ok.
							} else {
								printf( '<a href="%s">%s</a>', esc_url( $product_url ), $thumbnail ); // WPCS: XSS ok.
							}
							?>
						</td>
					<?php endif; ?>
					<td class="product-name">
						<?php
						if ( ! $product->is_visible() ) {
							echo apply_filters( 'tinvwl_wishlist_item_name', is_callable( array(
									$product,
									'get_name'
								) ) ? $product->get_name() : $product->get_title(), $wl_product, $product ) . '&nbsp;'; // WPCS: XSS ok.
						} else {
							echo apply_filters( 'tinvwl_wishlist_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_url ), is_callable( array(
								$product,
								'get_name'
							) ) ? $product->get_name() : $product->get_title() ), $wl_product, $product ); // WPCS: XSS ok.
						}

						echo apply_filters( 'tinvwl_wishlist_item_meta_data', tinv_wishlist_get_item_data( $product, $wl_product ), $wl_product, $product ); // WPCS: XSS ok.
						?>
					</td>
					<?php if ( isset( $wishlist_table_row['colm_price'] ) && $wishlist_table_row['colm_price'] ) : ?>
						<td class="product-price">
							<?php
							echo apply_filters( 'tinvwl_wishlist_item_price', $product->get_price_html(), $wl_product, $product, false ); // WPCS: XSS ok.
							?>
						</td>
					<?php endif; ?>
					<?php if ( isset( $wishlist_table_row['colm_date'] ) && $wishlist_table_row['colm_date'] ) : ?>
						<td class="product-date">
							<?php
							echo apply_filters( 'tinvwl_wishlist_item_date', sprintf( // WPCS: XSS ok.
								'<time class="entry-date" datetime="%1$s">%2$s</time>', $wl_product['date'], mysql2date( get_option( 'date_format' ), $wl_product['date'] )
							), $wl_product, $product );
							?>
						</td>
					<?php endif; ?>
					<?php if ( isset( $wishlist_table_row['colm_stock'] ) && $wishlist_table_row['colm_stock'] ) : ?>
						<td class="product-stock">
							<?php
							$availability = (array) $product->get_availability();
							if ( ! array_key_exists( 'availability', $availability ) ) {
								$availability['availability'] = '';
							}
							if ( ! array_key_exists( 'class', $availability ) ) {
								$availability['class'] = '';
							}
							$availability_html = empty( $availability['availability'] ) ? '<p class="stock ' . esc_attr( $availability['class'] ) . '"><span><i class="ftinvwl ftinvwl-check"></i></span><span class="tinvwl-txt">' . esc_html__( 'In stock', 'ti-woocommerce-wishlist-premium' ) . '</span></p>' : '<p class="stock ' . esc_attr( $availability['class'] ) . '"><span><i class="ftinvwl ftinvwl-' . ( ( 'out-of-stock' === esc_attr( $availability['class'] ) ? 'times' : 'check' ) ) . '"></i></span><span>' . wp_kses_post( $availability['availability'] ) . '</span></p>';

							echo apply_filters( 'tinvwl_wishlist_item_status', $availability_html, $availability['availability'], $wl_product, $product ); // WPCS: XSS ok.
							?>
						</td>
					<?php endif; ?>
					<?php if ( $qty && isset( $wishlist_table_row['colm_quantity'] ) && $wishlist_table_row['colm_quantity'] ) : ?>
						<td class="product-quantity">
							<?php echo intval( $wl_product['quantity'] ); ?>
						</td>
					<?php endif; ?>
					<?php if ( isset( $wishlist_table_row['subtotal'] ) && $wishlist_table_row['subtotal'] ) : ?>
						<td class="product-subtotal">
							<?php echo wc_price( $wl_product['subtotal'] ); ?>
						</td>
					<?php endif; ?>
				</tr>
				<?php
			}
		}
		?>
		</tbody>
	</table>
<?php else: ?>
	<p class="wishlist-empty">
		<?php if ( get_current_user_id() === $wishlist['author'] ) {
			$msg = esc_html__( 'Your {wishlist_title} is currently empty.', 'ti-woocommerce-wishlist-premium' );
		} else {
			$msg = esc_html__( '{wishlist_title} is currently empty.', 'ti-woocommerce-wishlist-premium' );
		}

		echo tinvwl_message_placeholders( $msg, null, $wishlist );
		?>
	</p>
<?php endif; ?>
<?php do_action( 'tinvwl_wishlist_pdf_after_products_table', $wishlist, $products ); ?>
<?php if ( isset( $total ) && $total ) : ?>
	<div class="wishlist-total">
		<h4><?php echo esc_html( __( 'Total: ', 'ti-woocommerce-wishlist-premium' ) ) . wc_price( $total ); ?></h4>
	</div>
<?php endif; ?>
</body>
</html>
