<?php
/**
 * Template for the bottom of the config page.
 *
 * @package Przelewy24
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="p24-check-config-descriptions">
	<dialog data-error-code="<?php echo esc_html( P24_Config_Checker::ERROR_NONE_ID ); ?>">
		<p>
			<?php echo esc_html( __( 'Sprawdzenie konfiguracji powiodło się' ) ); ?>
		</p>
		<form method="dialog">
			<button>Ok</button>
		</form>
	</dialog>
	<dialog data-error-code="<?php echo esc_html( P24_Config_Checker::ERROR_X_ID ); ?>">
		<p>
			<?php echo esc_html( __( 'Test rejestracji transakcji zakończył się błędem - Sprawdź czy dane wpisane w konfiguracji wtyczki są prawidłowe. W przypadku dalszych błędów skontaktuj się z Działem Technicznym na adres email support@przelewy24.pl.' ) ); ?>
		</p>
		<p>
			<?php echo esc_html( __( 'Pamiętaj aby na początku korespondencji podać dane takie jak: ID sprzedawcy, zrzut ekranu konfiguracji.' ) ); ?>
		</p>
		<form method="dialog">
			<button>Ok</button>
		</form>
	</dialog>
	<dialog data-error-code="<?php echo esc_html( P24_Config_Checker::ERROR_AUTH_ID ); ?>">
		<p>
			<?php echo esc_html( __( 'Test rejestracji transakcji zakończył się błędem - Sprawdź czy dane wpisane w konfiguracji wtyczki są prawidłowe. W przypadku dalszych błędów skontaktuj się z Działem Technicznym na adres email support@przelewy24.pl.' ) ); ?>
		</p>
		<p>
			<?php echo esc_html( __( 'Pamiętaj aby na początku korespondencji podać dane takie jak: ID sprzedawcy, zrzut ekranu konfiguracji.' ) ); ?>
		</p>
		<form method="dialog">
			<button>Ok</button>
		</form>
	</dialog>
	<dialog data-error-code="<?php echo esc_html( P24_Config_Checker::ERROR_REGISTRATION_ID ); ?>">
		<p>
			<?php echo esc_html( __( 'W celu dokończenia aktywacji konta skontaktuj się z naszym' ) ); ?>
			<strong><?php echo esc_html( __( 'Biurem Obsługi Biznesu' ) ); ?></strong>
			<?php echo esc_html( __( 'na adres email biznes@przelewy24.pl' ) ); ?>
		</p>
		<p>
			<?php echo esc_html( __( 'Pamiętaj aby na początku korespondencji podać ID sprzedawcy.' ) ); ?>
		</p>
		<form method="dialog">
			<button>Ok</button>
		</form>
	</dialog>
</div>
