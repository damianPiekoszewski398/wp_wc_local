<?php
/**
 * The Template for promotional email content this plugin.
 *
 * @version             1.0.0
 * @package           TInvWishlist\Admin\Template
 * @codingStandardsIgnoreFile Generic.Files.LowercasedFilename.NotFound
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<table border="0" cellpadding="0" cellspacing="0" width="465" id="templateBody">
	<tbody>
	<tr>
		<td valign="top" align="center">
			<h1 style="margin:0;color:#NStitle;font-size: 36px;font-family:'Arial',sans-serif;font-weight:normal;text-align:center;"
				class="h1">{heading}</h1>
		</td>
	</tr>
	<tr>
		<td height="26"></td>
	</tr>
	<tr>
		<td valign="top" align="center">
			<p style="margin:0;color:#NScontent;font-size:14px;font-family:'Arial',sans-serif;line-height: 28px;"><?php esc_html_e( 'Hi', 'ti-woocommerce-wishlist-premium' ); ?>
				{user_name},<br/>
				<?php esc_html_e( 'A product from your wishlist is on sale!', 'ti-woocommerce-wishlist-premium' ); ?>
				<br/>
				<?php echo sprintf( __( 'Use this code %s to obtain a discount.', 'ti-woocommerce-wishlist-premium' ), '<span style="color:#NSmain;"><strong>{coupon_code}</strong></span>' ); // WPCS: xss ok. ?>
			</p>
		</td>
	</tr>
	<tr>
		<td height="35"></td>
	</tr>
	<tr>
		<td valign="top" align="center">
			<table bgcolor="#NSbackgrcont" style="border-radius:4px" border="0" cellpadding="0" cellspacing="0"
				   width="100%" align="center">
				<tbody>
				<tr>
					<td height="54"></td>
				</tr>
				<tr>
					<td valign="middle" align="center">
						{product_image}
					</td>
				</tr>
				<tr>
					<td height="36"></td>
				</tr>
				<tr>
					<td valign="middle" align="center">
						<p style="margin:0;color:#NScontent;font-size:14px;font-family:'Arial',sans-serif;line-height: 24px;">
							<a style="color:#NScontent;text-decoration:none;" href="{product_url}"><strong>{product_name}</strong></a><br/>
							<span style="padding-right:5px;">{product_price_sale}</span>
							{product_price_regular}
						</p>
					</td>
				</tr>
				<tr>
					<td height="48"></td>
				</tr>
				<tr>
					<td valign="middle" align="center">
						<!--[if mso]>
				<p style="line-height:0;margin:0;">&nbsp;</p>
				<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="#" style="height:62px;v-text-anchor:middle;width:429px;" arcsize="5%" fillcolor="#NSmain" stroke="f">
					<w:anchorlock/>
					<v:textbox style="mso-fit-shape-to-text:t" inset="0px,18px,0px,18px">
						<center style="color:#ffffff;font-family: Arial, sans-serif;font-size: 12px;line-height: 16px;font-weight: bold;mso-line-height-rule:exactly;"><?php esc_html_e( 'VIEW PRODUCT', 'ti-woocommerce-wishlist-premium' ); ?></center>
					</v:textbox>
				</v:roundrect>
				<![endif]-->

						<!--[if !mso]-->
						<a style="width:429px;display:block;padding:23px 18px;border-radius:0 0 4px 4px;text-align:center;text-decoration:none !important;color:#fff;background-color:#NSmain;font-family:Arial,sans-serif;font-size:12px;line-height:16px;font-weight:bold;"
						   href="{url_wishlist_with_product}"
						   target="_blank"><?php esc_html_e( 'VIEW PRODUCT', 'ti-woocommerce-wishlist-premium' ); ?></a>
						<![endif]-->
					</td>
				</tr>
				</tbody>
			</table>
		</td>
	</tr>
	</tbody>
</table>
