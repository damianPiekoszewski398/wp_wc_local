<?php
/**
 * The Template for displaying admin table info block this plugin.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<span class="tinvwl-info-wrap tinvwl-in-table">
	<span class="tinvwl-info-sign">
		<a class="tinvwl-help" href="javascript:void(0)" data-container="body" data-toggle="popover"
		   data-trigger="manual" data-placement="bottom" data-html="true" rel="nofollow">
			<i class="ftinvwl ftinvwl-info"></i>
		</a>
	</span>
	<span class="tinvwl-info-desc">
<?php echo $desc; // WPCS: xss ok. ?>
	</span>
</span>
