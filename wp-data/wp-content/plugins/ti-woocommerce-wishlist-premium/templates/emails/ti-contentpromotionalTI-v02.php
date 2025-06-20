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
<table bgcolor="#NSbackgrcont" style="border-radius: 4px;" border="0" cellpadding="0" cellspacing="0" width="550"
	   id="templateContainer">
	<tbody>
	{header_image_pre}
	<tr>
		<td align="center" valign="top">
			<img style="display: block;border: 0;max-width: 550px;" src="{header_image}" width="550px" height="375px"
				 alt=""/>
		</td>
	</tr>
	{header_image_post}
	<tr>
		<td height="60"></td>
	</tr>
	<tr>
		<td align="center" valign="top">
			<h1 style="margin:0;color:#NStitle;font-size: 22px;font-family:'Arial',sans-serif;font-weight:normal;text-align:center;"
				class="h1">{heading}</h1>
		</td>
	</tr>
	<tr>
		<td height="40"></td>
	</tr>
	<tr>
		<td align="center" valign="top">
			<div class="divider"
				 style="display: block;height:1px;width: 98px;background-color: #e0e0e0;margin:0 auto 20px;"></div>
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td align="center" valign="top">
			<p style="margin:0;color:#NScontent;font-size:14px;font-family:'Arial',sans-serif;line-height: 28px;"><?php esc_html_e( 'Hi', 'ti-woocommerce-wishlist-premium' ); ?>
				{user_name},
				<br/>
				<?php esc_html_e( 'A product from your wishlist is on sale!', 'ti-woocommerce-wishlist-premium' ); ?>
			</p>
		</td>
	</tr>
	<tr>
		<td height="26"></td>
	</tr>
	<tr>
		<td align="center" valign="top">
			<div style="font-size:14px;font-family:'Arial',sans-serif;line-height: 24px;">
				<a style="color:#NSmain;padding-right:30px;" href="{product_url}"
				   target="_blank"><strong>{product_name}</strong></a>
				<span style="color:#NScontent;padding-right:20px;">{product_price_regular}</span>
				<span style="color:#NScontent;">{product_price_sale}</span>
			</div>
		</td>
	</tr>
	<tr>
		<td height="28"></td>
	</tr>
	<tr>
		<td align="center" valign="top">
			<p style="margin:0;color:#NScontent;font-size:14px;font-family:'Arial',sans-serif;line-height: 24px;"><?php echo sprintf( __( 'Use this code %s to obtain a discount.', 'ti-woocommerce-wishlist-premium' ), '<span style="color:#NSmain;"><strong>{coupon_code}</strong></span>' ); // WPCS: xss ok. ?></p>
		</td>
	</tr>
	<tr>
		<td height="44"></td>
	</tr>
	<tr>
		<td align="center" valign="top">
			<!--[if mso]>
				<p style="line-height:0;margin:0;">&nbsp;</p>
				<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="#" style="height:62px;v-text-anchor:middle;width:130px;" arcsize="5%" fillcolor="#NSmain" stroke="f">
					<w:anchorlock/>
					<v:textbox style="mso-fit-shape-to-text:t" inset="0px,18px,0px,18px">
						<center style="color:#ffffff;font-family: Arial, sans-serif;font-size: 12px;line-height: 16px;font-weight: bold;mso-line-height-rule:exactly;"><?php esc_html_e( 'VIEW PRODUCT', 'ti-woocommerce-wishlist-premium' ); ?></center>
					</v:textbox>
				</v:roundrect>
				<![endif]-->

			<!--[if !mso]-->
			<a style="padding: 15px 18px;border-radius: 4px;display: inline-block;text-align: center;text-decoration: none !important;color: #fff;background-color: #NSmain;font-family: Arial, sans-serif;font-size: 12px;line-height: 16px;font-weight: bold;"
			   href="{url_wishlist_with_product}"
			   target="_blank"><?php esc_html_e( 'VIEW PRODUCT', 'ti-woocommerce-wishlist-premium' ); ?></a>
			<![endif]-->
		</td>
	</tr>
	<tr>
		<td height="60"></td>
	</tr>
	</tbody>
</table>
