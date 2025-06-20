<?php
/**
 * Template for active multi currency.
 *
 * @package Przelewy24
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $hpos_active ) ) {
	throw new LogicException( 'The variable $hpos_active is not set.' );
}

if ( ! isset( $is_active ) ) {
	throw new LogicException( 'The variable $is_active is not set.' );
}

?>

<h1><?php echo esc_html( __( 'Aktywacja statusów P24' ) ); ?></h1>

<?php if ( $hpos_active ) : ?>
	<p class="p24-warning">
		<?php
		echo esc_html(
			__(
				'
        Aktywna jest opcja Wydajne przechowywanie zamówień.
        Obsługa dodatkowych statusów Przelewy24 dla zamówień
        nie jest dla niej wpierana. Prosimy ograniczyć się
        do domyślnych statusów dla zamówień
        albo zmienić ustawienia WooCommerce.
    '
			)
		);
		?>
	</p>
<?php endif; ?>

<form method="post">
	<table>
		<tr>
			<?php $field_id = 'p24_statuses_active_' . wp_rand(); ?>
			<th>
				<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( __( 'Aktywuj statusy' ) ); ?></label>
			</th>
			<td>
				<input type="checkbox" name="p24_statuses_active" <?php echo $is_active ? esc_attr( 'checked' ) : ''; ?> id="<?php echo esc_attr( $field_id ); ?>" value="1" />
			</td>
		</tr>

		<tr>
			<td></td>
			<td colspan="2">
				<input type="hidden" name="p24_action_type_field" value="activate_statuses" />
				<?php wp_nonce_field( 'p24_action', 'p24_nonce' ); ?>
				<input type="submit" value="<?php echo esc_html( __( 'Zapisz' ) ); ?>" />
			</td>
		</tr>
	</table>
</form>
<?php
