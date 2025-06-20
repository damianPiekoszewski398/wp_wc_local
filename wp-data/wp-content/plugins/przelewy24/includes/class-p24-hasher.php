<?php
/**
 * File that define P24_Hasher class.
 *
 * @package Przelewy24
 */

defined( 'ABSPATH' ) || exit;

/* We have to require this one by hand. */
require_once ABSPATH . WPINC . '/class-phpass.php';

/**
 * Class to hash sensitive data.
 */
class P24_Hasher {

	/**
	 * The hasher from WordPress.
	 *
	 * @var PasswordHash
	 */
	private $hasher;

	/**
	 * String with salt.
	 *
	 * @var string
	 */
	private $salt;

	/**
	 * Constructor.
	 *
	 * @param P24_Config_Accessor $config Config for Przelewy24.
	 */
	public function __construct( P24_Config_Accessor $config ) {
		$this->hasher = new PasswordHash( 8, false );
		$this->salt   = $config->get_salt();
	}

	/**
	 * Compute hash.
	 *
	 * @param string $input Input to compute hash.
	 * @return string
	 */
	public function hash( $input ) {
		return $this->hasher->HashPassword( $input . $this->salt );
	}

	/**
	 * Check hash.
	 *
	 * @param string $input Intput that has hash computed.
	 * @param string $hash Computed hash.
	 * @return bool
	 */
	public function check( $input, $hash ) {
		return $this->hasher->CheckPassword( $input . $this->salt, $hash );
	}

	/**
	 * Check hash and return original value if valid. Null otherwise.
	 *
	 * @param string $input Intput that has hash computed.
	 * @param string $hash Computed hash.
	 * @return string|null
	 */
	public function return_if_valid( $input, $hash ) {
		if ( $this->check( $input, $hash ) ) {
			return $input;
		} else {
			return null;
		}
	}
}
