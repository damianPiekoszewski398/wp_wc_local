<?php
/**
 * Template for the bottom of the config page.
 *
 * @package Przelewy24
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $url ) ) {
	throw new LogicException( 'The variable $url is not set.' );
}

?>


<tr valign="top" id="p24-btn-additional">
	<th scope="row" class="titledesc">
		<label>Dodatkowe akcje</label>
	</th>
	<td class="forminp">
		<span
				class="button button-secondary js-check-config"
				data-url="<?php echo esc_attr( $url ); ?>"
		>
			<?php echo esc_html( __( 'Sprawdź konfigurację' ) ); ?>
		</span>
	</td>
</tr>
