<?php
/**
 * The Template for promotional email this plugin.
 *
 * @version             2.2.0
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
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

	<!-- Facebook sharing information tags -->
	<meta property="og:title" content="<?php echo get_bloginfo( 'name', 'display' ); // WPCS: xss ok. ?>"/>

	<title><?php echo get_bloginfo( 'name', 'display' ); // WPCS: xss ok. ?></title>
	<style type="text/css">
		/* Client-specific Styles */
		#outlook a {
			padding: 0;
		}

		/* Force Outlook to provide a "view in browser" button. */
		body {
			width: 100% !important;
		}

		/* Force Hotmail to display emails at full width */
		body {
			-webkit-text-size-adjust: none;
		}

		/* Prevent Webkit platforms from changing default text sizes. */

		/* Reset Styles */
		body {
			margin: 0;
			padding: 0;
		}

		img {
			border: 0;
			height: auto;
			line-height: 100%;
			outline: none;
			text-decoration: none;
		}

		table td {
			border-collapse: collapse;
		}

		#backgroundTable {
			height: 100% !important;
			margin: 0;
			padding: 0;
			width: 100% !important;
		}
	</style>
	<!--[if !mso]><!-->
	<style type="text/css">
		@import url(https://fonts.googleapis.com/css?family=Open+Sans:700);
	</style>
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:700" rel="stylesheet" type="text/css"/>
	<!--<![endif]-->
</head>
<body style="background-color: <?php echo tinv_get_option( 'notifications_style', 'background' ); // WPCS: xss ok. ?>;"
	  leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
<table bgcolor="<?php echo tinv_get_option( 'notifications_style', 'background' ); // WPCS: xss ok. ?>" border="0"
	   cellpadding="0" cellspacing="0" height="100%" width="100%" id="backgroundTable">
	<tbody>
	<tr>
		<td align="center" valign="top">
			<table border="0" cellpadding="0" cellspacing="0" width="550" id="templatePreheader" align="center">
				<tbody>
				<tr>
					<td height="58"></td>
				</tr>
				<tr>
					<td valign="middle" align="left">
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td valign="middle">
									<img src="<?php echo esc_attr( ( ! tinv_get_option( 'notifications_style', 'current_logo' ) && tinv_get_option( 'notifications_style', 'logo' ) ) ? tinv_get_option( 'notifications_style', 'logo' ) : TINVWL_URL . 'assets/img/logo_heart.png' ); ?>"
										 alt=""/>
								</td>
								<td width="27"></td>
								<td valign="middle">
									<span style="color:<?php echo tinv_get_option( 'notifications_style', 'content' ); // WPCS: xss ok. ?>; font-family:'Open Sans', sans-serif; font-size:14px; line-height:24px;"><?php echo tinv_get_option( 'notifications_style', 'logo_text' ); // WPCS: xss ok. ?></span>
								</td>
							</tr>
						</table>
					</td>
					<td width="6"></td>
				</tr>
				<tr>
					<td height="81"></td>
				</tr>
				</tbody>
			</table>
			<?php echo $content; // WPCS: xss ok. ?>
			<table border="0" cellpadding="0" cellspacing="0" width="550" align="center" id="templateFooter">
				<tbody>
				<tr>
					<td height="59"></td>
				</tr>
				<tr>
					<td valign="middle" id="social" align="center">
						<table border="0" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<?php if ( tinv_get_option( 'notifications_style', 'facebook' ) ) : ?>
									<td width="55" valign="middle" align="center">
										<a href="http://facebook.com/<?php echo tinv_get_option( 'notifications_style', 'facebook' ); // WPCS: xss ok. ?>"
										   target="_blank"><img
													src="<?php echo esc_attr( TINVWL_URL . 'assets/img/emails/social_facebook.png' ); ?>"
													width="9" height="18"
													alt="<?php echo tinv_get_option( 'notifications_style', 'facebook' ); // WPCS: xss ok. ?>"/></a>
									</td>
								<?php endif; ?>
								<?php if ( tinv_get_option( 'notifications_style', 'twitter' ) ) : ?>
									<td width="55" valign="middle" align="center">
										<a href="https://twitter.com/<?php echo tinv_get_option( 'notifications_style', 'twitter' ); // WPCS: xss ok. ?>"
										   target="_blank"><img
													src="<?php echo esc_attr( TINVWL_URL . 'assets/img/emails/social_twitter.png' ); ?>"
													width="18" height="14"
													alt="<?php echo tinv_get_option( 'notifications_style', 'twitter' ); // WPCS: xss ok. ?>"/></a>
									</td>
								<?php endif; ?>
								<?php if ( tinv_get_option( 'notifications_style', 'pinterest' ) ) : ?>
									<td width="55" valign="middle" align="center">
										<a href="http://pinterest.com/<?php echo tinv_get_option( 'notifications_style', 'pinterest' ); // WPCS: xss ok. ?>"
										   target="_blank"><img
													src="<?php echo esc_attr( TINVWL_URL . 'assets/img/emails/social_pinterest.png' ); ?>"
													width="14" height="17"
													alt="<?php echo tinv_get_option( 'notifications_style', 'pinterest' ); // WPCS: xss ok. ?>"/></a>
									</td>
								<?php endif; ?>
								<?php if ( tinv_get_option( 'notifications_style', 'google+' ) ) : ?>
									<td width="55" valign="middle" align="center">
										<a href="http://plus.google.com/u/0/<?php echo tinv_get_option( 'notifications_style', 'google+' ); // WPCS: xss ok. ?>"
										   target="_blank"><img
													src="<?php echo esc_attr( TINVWL_URL . 'assets/img/emails/social_google_plus.png' ); ?>"
													width="15" height="12"
													alt="<?php echo tinv_get_option( 'notifications_style', 'google+' ); // WPCS: xss ok. ?>"/></a>
									</td>
								<?php endif; ?>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="61"></td>
				</tr>
				<tr>
					<td valign="top" width="350" align="center">
						<span style="color:<?php echo tinv_get_option( 'notifications_style', 'content' ); // WPCS: xss ok. ?>;font-size:11px;font-family:'Arial',sans-serif;line-height: 28px;opacity:.59"><?php echo tinv_get_option( 'notifications_style', 'footer_text' ); // WPCS: xss ok. ?></span>
					</td>
				</tr>
				<tr>
					<td height="32"></td>
				</tr>
				</tbody>
			</table>
		</td>
	</tr>
	</tbody>
</table>
</body>
</html>
