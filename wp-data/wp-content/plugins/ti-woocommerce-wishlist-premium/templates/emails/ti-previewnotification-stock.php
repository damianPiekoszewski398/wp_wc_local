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

?><!DOCTYPE html>
<html dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo get_bloginfo( 'name', 'display' ); ?></title></head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
<div id="wrapper" dir="ltr"
	 style="background-color: #f5f5f5; margin: 0; padding: 70px 0 70px 0; -webkit-text-size-adjust: none !important; width: 100%;">
	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
		<tr>
			<td align="center" valign="top">
				<div id="template_header_image"></div>
				<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container"
					   style="box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important; background-color: #fdfdfd; border: 1px solid #dcdcdc; border-radius: 3px !important;">
					<tr>
						<td align="center" valign="top"><!-- Header -->
							<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_header"
								   style='background-color: #557da1; border-radius: 3px 3px 0 0 !important; color: #ffffff; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;'>
								<tr>
									<td id="header_wrapper" style="padding: 36px 48px; display: block;"><h1
												style='color: #ffffff; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: left; text-shadow: 0 1px 0 #7797b4; -webkit-font-smoothing: antialiased;'><?php esc_html_e( 'The Product is now on sale!', 'ti-woocommerce-wishlist-premium' ); ?></h1>
									</td>
								</tr>
							</table><!-- End Header --></td>
					</tr>
					<tr>
						<td align="center" valign="top"><!-- Body -->
							<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
								<tr>
									<td valign="top" id="body_content" style="background-color: #fdfdfd;">
										<!-- Content -->
										<table border="0" cellpadding="20" cellspacing="0" width="100%">
											<tr>
												<td valign="top" style="padding: 48px;">
													<div id="body_content_inner"
														 style='color: #737373; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 14px; line-height: 150%; text-align: left;'>
														<p style="margin: 0 0 16px;"><?php esc_html_e( 'Hi', 'ti-woocommerce-wishlist-premium' ); ?>
															SuperUser</p>
														<p style="margin: 0 0 16px;"><?php esc_html_e( 'A product of your wishlist is in stock!', 'ti-woocommerce-wishlist-premium' ); ?></p>
														<p style="margin: 0 0 16px;"></p>
														<ul>
															<li><a href="#"
																   style="color: #557da1; font-weight: normal; text-decoration: underline;"><?php esc_html_e( 'Default wishlist', 'ti-woocommerce-wishlist-premium' ); ?></a>
															</li>
														</ul>
														<p style="margin: 0 0 16px;"></p>
														<table>
															<tr>
																<td style="padding: 12px;"><img width="180" height="180"
																								src="<?php echo TINVWL_URL; ?>assets/img/emails/T_7_front-180x180.jpg"
																								class="attachment-shop_thumbnail size-shop_thumbnail wp-post-image"
																								alt="T_7_front"
																								sizes="(max-width: 180px) 85vw, 180px"
																								style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;">
																</td>
																<td style="padding: 12px;">Happy Ninja</td>
																<td style="padding: 12px;">
																	<del>
																		<span class="woocommerce-Price-amount amount"><span
																					class="woocommerce-Price-currencySymbol">$</span>30.00</span>
																	</del>
																	<ins>
																		<span class="woocommerce-Price-amount amount"><span
																					class="woocommerce-Price-currencySymbol">$</span>18.00</span>
																	</ins>
																</td>
															</tr>
														</table>
													</div>
												</td>
											</tr>
										</table><!-- End Content --></td>
								</tr>
							</table><!-- End Body --></td>
					</tr>
					<tr>
						<td align="center" valign="top"><!-- Footer -->
							<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer">
								<tr>
									<td valign="top" style="padding: 0; -webkit-border-radius: 6px;">
										<table border="0" cellpadding="10" cellspacing="0" width="100%">
											<tr>
												<td colspan="2" valign="middle" id="credit"
													style="padding: 0 48px 48px 48px; -webkit-border-radius: 6px; border: 0; color: #99b1c7; font-family: Arial; font-size: 12px; line-height: 125%; text-align: center;">
													<p><?php echo get_bloginfo( 'name', 'display' ); ?> – Powered by
														WooCommerce</p></td>
											</tr>
										</table>
									</td>
								</tr>
							</table><!-- End Footer --></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
</body>
</html>
