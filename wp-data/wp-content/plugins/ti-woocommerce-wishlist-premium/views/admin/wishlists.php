<?php
/**
 * The Template for displaying wishlist table.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$table->prepare_items();
?>

<section class="tinvwl-panel only-button">
	<?php $table->views(); ?>
</section>

<section class="tinwl-table-wrap tinvwl-panel w-bg w-shadow">
	<form method="POST">
		<?php $table->display(); ?>
	</form>
</section>
