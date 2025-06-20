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

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><!-- Facebook sharing information tags -->
	<meta property="og:title" content="<?php echo get_bloginfo( 'name', 'display' ); ?>">
	<title><?php echo get_bloginfo( 'name', 'display' ); ?></title><!--[if !mso]><!-->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:700" rel="stylesheet" type="text/css"><!--<![endif]-->
</head>
<body style="background-color: #ffffff; width: 100% !important; -webkit-text-size-adjust: none; margin: 0; padding: 0;"
	  leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="backgroundTable"
	   style="height: 100% !important; margin: 0; padding: 0; width: 100% !important;">
	<tbody>
	<tr>
		<td align="center" valign="top" style="border-collapse: collapse;">
			<table border="0" cellpadding="0" cellspacing="0" width="550" id="templatePreheader" align="center">
				<tbody>
				<tr>
					<td height="58" style="border-collapse: collapse;"></td>
				</tr>
				<tr>
					<td valign="middle" align="left" style="border-collapse: collapse;">
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td valign="middle" style="border-collapse: collapse;"><img
											src="<?php echo TINVWL_URL; ?>assets/img/logo_heart.png" width="54px"
											height="54px" alt=""
											style="border: 0; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;">
								</td>
								<td width="27" style="border-collapse: collapse;"></td>
								<td valign="middle" style="border-collapse: collapse;"><span
											style="color: #4f4639; font-family: 'Open Sans',sans-serif; font-size: 14px; line-height: 24px;"><strong>TI.WISHLIST</strong></span>
								</td>
							</tr>
						</table>
					</td>
					<td width="6" style="border-collapse: collapse;"></td>
				</tr>
				<tr>
					<td height="81" style="border-collapse: collapse;"></td>
				</tr>
				</tbody>
			</table>
			<table border="0" cellpadding="0" cellspacing="0" width="465" id="templateBody">
				<tbody>
				<tr>
					<td valign="top" align="center">
						<h1 style="margin:0;color:#202020;font-size: 36px;font-family:'Arial',sans-serif;font-weight:normal;text-align:center;"
							class="h1"><?php esc_html_e( 'There is a deal for you!', 'ti-woocommerce-wishlist-premium' ) ?></h1>
					</td>
				</tr>
				<tr>
					<td height="26"></td>
				</tr>
				<tr>
					<td valign="top" align="center">
						<p style="margin:0;color:#4f4639;font-size:14px;font-family:'Arial',sans-serif;line-height: 28px;"><?php esc_html_e( 'Hi', 'ti-woocommerce-wishlist-premium' ); ?>
							SuperUser,<br/>
							<?php esc_html_e( 'A product from your wishlist is on sale!', 'ti-woocommerce-wishlist-premium' ); ?>
							<br/>
							<?php echo sprintf( __( 'Use this code %s to obtain a discount.', 'ti-woocommerce-wishlist-premium' ), '<span style="color:#ff5739;"><strong>[discount_code]</strong></span>' ); // WPCS: xss ok. ?>
						</p>
					</td>
				</tr>
				<tr>
					<td height="35"></td>
				</tr>
				<tr>
					<td valign="top" align="center">
						<table bgcolor="#f5f5f5" style="border-radius:4px" border="0" cellpadding="0" cellspacing="0"
							   width="100%" align="center">
							<tbody>
							<tr>
								<td height="54"></td>
							</tr>
							<tr>
								<td valign="middle" align="center">
									<img width="180" height="180"
										 src="<?php echo TINVWL_URL; ?>assets/img/emails/T_7_front-180x180.jpg"
										 class="attachment-shop_thumbnail size-shop_thumbnail wp-post-image"
										 alt="T_7_front" sizes="(max-width: 180px) 85vw, 180px"
										 style="border: 0; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;">
								</td>
							</tr>
							<tr>
								<td height="36"></td>
							</tr>
							<tr>
								<td valign="middle" align="center">
									<p style="margin:0;color:#4f4639;font-size:14px;font-family:'Arial',sans-serif;line-height: 24px;">
										<a style="color:#4f4639;text-decoration:none;" href="#"><strong>Happy
												Ninja</strong></a><br/>
										<span style="padding-right:5px;"><span
													class="woocommerce-Price-amount amount"><span
														class="woocommerce-Price-currencySymbol">$</span>18.00</span></span>
										<strike><span class="woocommerce-Price-amount amount"><span
														class="woocommerce-Price-currencySymbol">$</span>30.00</span></strike>
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
<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="#" style="height:62px;v-text-anchor:middle;width:429px;" arcsize="5%" fillcolor="#ff5739" stroke="f">
<w:anchorlock/>
<v:textbox style="mso-fit-shape-to-text:t" inset="0px,18px,0px,18px">
<center style="color:#ffffff;font-family: Arial, sans-serif;font-size: 12px;line-height: 16px;font-weight: bold;mso-line-height-rule:exactly;"><?php esc_html_e( 'VIEW PRODUCT', 'ti-woocommerce-wishlist-premium' ); ?></center>
</v:textbox>
</v:roundrect>
<![endif]-->

									<!--[if !mso]-->
									<a style="width:429px;display:block;padding:23px 18px;border-radius:0 0 4px 4px;text-align:center;text-decoration:none !important;color:#fff;background-color:#ff5739;font-family:Arial,sans-serif;font-size:12px;line-height:16px;font-weight:bold;"
									   href="#"
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
			<table border="0" cellpadding="0" cellspacing="0" width="550" align="center" id="templateFooter">
				<tbody>
				<tr>
					<td height="59" style="border-collapse: collapse;"></td>
				</tr>
				<tr>
					<td valign="middle" id="social" align="center" style="border-collapse: collapse;">
						<table border="0" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td width="55" valign="middle" align="center" style="border-collapse: collapse;"><a
											href="#" target="_blank"
											style="color: #557da1; font-weight: normal; text-decoration: underline;"><img
												src="<?php echo TINVWL_URL; ?>assets/img/emails/social_facebook.png"
												width="9" height="18" alt=""
												style="border: 0; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"></a>
								</td>
								<td width="55" valign="middle" align="center" style="border-collapse: collapse;"><a
											href="#" target="_blank"
											style="color: #557da1; font-weight: normal; text-decoration: underline;"><img
												src="<?php echo TINVWL_URL; ?>assets/img/emails/social_twitter.png"
												width="18" height="14" alt=""
												style="border: 0; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"></a>
								</td>
								<td width="55" valign="middle" align="center" style="border-collapse: collapse;"><a
											href="#" target="_blank"
											style="color: #557da1; font-weight: normal; text-decoration: underline;"><img
												src="<?php echo TINVWL_URL; ?>assets/img/emails/social_pinterest.png"
												width="14" height="17" alt=""
												style="border: 0; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"></a>
								</td>
								<td width="55" valign="middle" align="center" style="border-collapse: collapse;"><a
											href="#" target="_blank"
											style="color: #557da1; font-weight: normal; text-decoration: underline;"><img
												src="<?php echo TINVWL_URL; ?>assets/img/emails/social_google_plus.png"
												width="15" height="12" alt=""
												style="border: 0; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"></a>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="61" style="border-collapse: collapse;"></td>
				</tr>
				<tr>
					<td valign="top" width="350" align="center" style="border-collapse: collapse;"><span
								style="color: #4f4639; font-size: 11px; font-family: 'Arial',sans-serif; line-height: 28px; opacity: .59;">Copyright © 2016 Ti.Wishlist, All rightsreserved.</span>
					</td>
				</tr>
				<tr>
					<td height="32" style="border-collapse: collapse;"></td>
				</tr>
				</tbody>
			</table>
		</td>
	</tr>
	</tbody>
</table>
</body>
</html>
