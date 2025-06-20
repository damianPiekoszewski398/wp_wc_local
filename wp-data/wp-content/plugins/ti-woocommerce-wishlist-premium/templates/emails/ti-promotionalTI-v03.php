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
	<tr>
		<td align="center" valign="top">
			<table border="0" cellpadding="0" cellspacing="0" width="550" id="templatePreheader" align="center">
				<tbody>
				<tr>
					<td height="51"></td>
				</tr>
				<tr>
					<td valign="top" align="center">
						<img src="<?php echo esc_attr( ( ! tinv_get_option( 'notifications_style', 'current_logo' ) && tinv_get_option( 'notifications_style', 'logo' ) ) ? tinv_get_option( 'notifications_style', 'logo' ) : TINVWL_URL . 'assets/img/logo_heart.png' ); ?>"
							  alt=""/>
					</td>
				</tr>
				<tr>
					<td height="10"></td>
				</tr>
				<tr>
					<td valign="top" align="center">
						<span style="color:<?php echo tinv_get_option( 'notifications_style', 'content' ); // WPCS: xss ok. ?>; font-family:'Open Sans', sans-serif;font-size:14px;line-height:24px;"><?php echo tinv_get_option( 'notifications_style', 'logo_text' ); // WPCS: xss ok. ?></span>
					</td>
				</tr>
				<tr>
					<td height="54"></td>
				</tr>
				</tbody>
			</table>
			<?php echo $content; // WPCS: xss ok. ?>
			<table border="0" cellpadding="0" cellspacing="0" width="550" id="templateFooter">
				<tbody>
				<tr>
					<td height="48"></td>
				</tr>
				<tr>
					<td valign="middle" id="social" align="center">
						<?php if ( tinv_get_option( 'notifications_style', 'facebook' ) ) : ?>
							<a style="margin: 0 8px;"
							   href="http://facebook.com/<?php echo tinv_get_option( 'notifications_style', 'facebook' ); // WPCS: xss ok. ?>"
							   target="_blank"><img
										src="<?php echo esc_attr( TINVWL_URL . 'assets/img/emails/social-circle/social_facebook.png' ); ?>"
										width="41" height="41"
										alt="<?php echo tinv_get_option( 'notifications_style', 'facebook' ); // WPCS: xss ok. ?>"/></a>
						<?php endif; ?>
						<?php if ( tinv_get_option( 'notifications_style', 'twitter' ) ) : ?>
							<a style="margin: 0 8px;"
							   href="https://twitter.com/<?php echo tinv_get_option( 'notifications_style', 'twitter' ); // WPCS: xss ok. ?>"
							   target="_blank"><img
										src="<?php echo esc_attr( TINVWL_URL . 'assets/img/emails/social-circle/social_twitter.png' ); ?>"
										width="41" height="41"
										alt="<?php echo tinv_get_option( 'notifications_style', 'twitter' ); // WPCS: xss ok. ?>"/></a>
						<?php endif; ?>
						<?php if ( tinv_get_option( 'notifications_style', 'pinterest' ) ) : ?>
							<a style="margin: 0 8px;"
							   href="http://pinterest.com/<?php echo tinv_get_option( 'notifications_style', 'pinterest' ); // WPCS: xss ok. ?>"
							   target="_blank"><img
										src="<?php echo esc_attr( TINVWL_URL . 'assets/img/emails/social-circle/social_pinterest.png' ); ?>"
										width="41" height="41"
										alt="<?php echo tinv_get_option( 'notifications_style', 'pinterest' ); // WPCS: xss ok. ?>"/></a>
						<?php endif; ?>
						<?php if ( tinv_get_option( 'notifications_style', 'google+' ) ) : ?>
							<a style="margin: 0 8px;"
							   href="http://plus.google.com/u/0/<?php echo tinv_get_option( 'notifications_style', 'google+' ); // WPCS: xss ok. ?>"
							   target="_blank"><img
										src="<?php echo esc_attr( TINVWL_URL . 'assets/img/emails/social-circle/social_google_plus.png' ); ?>"
										width="41" height="41"
										alt="<?php echo tinv_get_option( 'notifications_style', 'google+' ); // WPCS: xss ok. ?>"/></a>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td height="40"></td>
				</tr>
				<tr>
					<td valign="top" width="350" align="center">
						<span style="color:<?php echo tinv_get_option( 'notifications_style', 'content' ); // WPCS: xss ok. ?>;font-size:11px;font-family:'Arial',sans-serif;line-height: 28px;opacity:.59"><?php echo tinv_get_option( 'notifications_style', 'footer_text' ); // WPCS: xss ok. ?></span>
					</td>
				</tr>
				<tr>
					<td height="42"></td>
				</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
</body>
</html>
