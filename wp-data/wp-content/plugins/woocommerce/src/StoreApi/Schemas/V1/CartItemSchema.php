<?php
namespace Automattic\WooCommerce\StoreApi\Schemas\V1;

use Automattic\WooCommerce\StoreApi\Utilities\ProductItemTrait;
use Automattic\WooCommerce\StoreApi\Utilities\QuantityLimits;

/**
 * CartItemSchema class.
 */
class CartItemSchema extends ItemSchema {
	use ProductItemTrait;

	/**
	 * The schema item name.
	 *
	 * @var string
	 */
	protected $title = 'cart_item';

	/**
	 * The schema item identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'cart-item';

    protected $_transliteration = [
        'ä' => 'ae',
        'æ' => 'ae',
        'ǽ' => 'ae',
        'ö' => 'oe',
        'œ' => 'oe',
        'ü' => 'ue',
        'Ä' => 'Ae',
        'Ü' => 'Ue',
        'Ö' => 'Oe',
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Å' => 'A',
        'Ǻ' => 'A',
        'Ā' => 'A',
        'Ă' => 'A',
        'Ą' => 'A',
        'Ǎ' => 'A',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'å' => 'a',
        'ǻ' => 'a',
        'ā' => 'a',
        'ă' => 'a',
        'ą' => 'a',
        'ǎ' => 'a',
        'ª' => 'a',
        'Ç' => 'C',
        'Ć' => 'C',
        'Ĉ' => 'C',
        'Ċ' => 'C',
        'Č' => 'C',
        'ç' => 'c',
        'ć' => 'c',
        'ĉ' => 'c',
        'ċ' => 'c',
        'č' => 'c',
        'Ð' => 'D',
        'Ď' => 'D',
        'Đ' => 'D',
        'ð' => 'd',
        'ď' => 'd',
        'đ' => 'd',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ē' => 'E',
        'Ĕ' => 'E',
        'Ė' => 'E',
        'Ę' => 'E',
        'Ě' => 'E',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ē' => 'e',
        'ĕ' => 'e',
        'ė' => 'e',
        'ę' => 'e',
        'ě' => 'e',
        'Ĝ' => 'G',
        'Ğ' => 'G',
        'Ġ' => 'G',
        'Ģ' => 'G',
        'Ґ' => 'G',
        'ĝ' => 'g',
        'ğ' => 'g',
        'ġ' => 'g',
        'ģ' => 'g',
        'ґ' => 'g',
        'Ĥ' => 'H',
        'Ħ' => 'H',
        'ĥ' => 'h',
        'ħ' => 'h',
        'І' => 'I',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ї' => 'Yi',
        'Ï' => 'I',
        'Ĩ' => 'I',
        'Ī' => 'I',
        'Ĭ' => 'I',
        'Ǐ' => 'I',
        'Į' => 'I',
        'İ' => 'I',
        'і' => 'i',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ї' => 'yi',
        'ĩ' => 'i',
        'ī' => 'i',
        'ĭ' => 'i',
        'ǐ' => 'i',
        'į' => 'i',
        'ı' => 'i',
        'Ĵ' => 'J',
        'ĵ' => 'j',
        'Ķ' => 'K',
        'ķ' => 'k',
        'Ĺ' => 'L',
        'Ļ' => 'L',
        'Ľ' => 'L',
        'Ŀ' => 'L',
        'Ł' => 'L',
        'ĺ' => 'l',
        'ļ' => 'l',
        'ľ' => 'l',
        'ŀ' => 'l',
        'ł' => 'l',
        'Ñ' => 'N',
        'Ń' => 'N',
        'Ņ' => 'N',
        'Ň' => 'N',
        'ñ' => 'n',
        'ń' => 'n',
        'ņ' => 'n',
        'ň' => 'n',
        'ŉ' => 'n',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ō' => 'O',
        'Ŏ' => 'O',
        'Ǒ' => 'O',
        'Ő' => 'O',
        'Ơ' => 'O',
        'Ø' => 'O',
        'Ǿ' => 'O',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ō' => 'o',
        'ŏ' => 'o',
        'ǒ' => 'o',
        'ő' => 'o',
        'ơ' => 'o',
        'ø' => 'o',
        'ǿ' => 'o',
        'º' => 'o',
        'Ŕ' => 'R',
        'Ŗ' => 'R',
        'Ř' => 'R',
        'ŕ' => 'r',
        'ŗ' => 'r',
        'ř' => 'r',
        'Ś' => 'S',
        'Ŝ' => 'S',
        'Ş' => 'S',
        'Ș' => 'S',
        'Š' => 'S',
        'ẞ' => 'SS',
        'ś' => 's',
        'ŝ' => 's',
        'ş' => 's',
        'ș' => 's',
        'š' => 's',
        'ſ' => 's',
        'Ţ' => 'T',
        'Ț' => 'T',
        'Ť' => 'T',
        'Ŧ' => 'T',
        'ţ' => 't',
        'ț' => 't',
        'ť' => 't',
        'ŧ' => 't',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ũ' => 'U',
        'Ū' => 'U',
        'Ŭ' => 'U',
        'Ů' => 'U',
        'Ű' => 'U',
        'Ų' => 'U',
        'Ư' => 'U',
        'Ǔ' => 'U',
        'Ǖ' => 'U',
        'Ǘ' => 'U',
        'Ǚ' => 'U',
        'Ǜ' => 'U',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ũ' => 'u',
        'ū' => 'u',
        'ŭ' => 'u',
        'ů' => 'u',
        'ű' => 'u',
        'ų' => 'u',
        'ư' => 'u',
        'ǔ' => 'u',
        'ǖ' => 'u',
        'ǘ' => 'u',
        'ǚ' => 'u',
        'ǜ' => 'u',
        'Ý' => 'Y',
        'Ÿ' => 'Y',
        'Ŷ' => 'Y',
        'ý' => 'y',
        'ÿ' => 'y',
        'ŷ' => 'y',
        'Ŵ' => 'W',
        'ŵ' => 'w',
        'Ź' => 'Z',
        'Ż' => 'Z',
        'Ž' => 'Z',
        'ź' => 'z',
        'ż' => 'z',
        'ž' => 'z',
        'Æ' => 'AE',
        'Ǽ' => 'AE',
        'ß' => 'ss',
        'Ĳ' => 'IJ',
        'ĳ' => 'ij',
        'Œ' => 'OE',
        'ƒ' => 'f',
        'Þ' => 'TH',
        'þ' => 'th',
        'Є' => 'Ye',
        'є' => 'ye',
    ];

	public function slug($string, $replacement = '-') {
        $quotedReplacement = preg_quote($replacement, '/');

        $map = [
            '/[^\s\p{Zs}\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ',
            '/[\s\p{Zs}]+/mu' => $replacement,
            sprintf('/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement) => '',
        ];

        $string = str_replace(
            array_keys($this->_transliteration),
            $this->_transliteration,
            $string
        );

        return preg_replace(array_keys($map), array_values($map), $string);
    }

	/**
	 * Convert a WooCommerce cart item to an object suitable for the response.
	 *
	 * @param array $cart_item Cart item array.
	 * @return array
	 */
	public function get_item_response( $cart_item ) {
		$product = $cart_item['data'];

		/**
		 * Filter the product permalink.
		 *
		 * This is a hook taken from the legacy cart/mini-cart templates that allows the permalink to be changed for a
		 * product. This is specific to the cart endpoint.
		 *
		 * @since 9.9.0
		 *
		 * @param string $product_permalink Product permalink.
		 * @param array  $cart_item         Cart item array.
		 * @param string $cart_item_key     Cart item key.
		 */
		$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $product->get_permalink(), $cart_item, $cart_item['key'] );

		$category_slug = null;
        $category_ids = $product->get_category_ids();
        if( count($category_ids) > 0 ) {
            $category_term = get_term_by( 'id', $category_ids[0], 'product_cat' );

            if( $category_term ) {
                $category_slug = $category_term->slug;
            }
        }

        $product_slug = $this->slug( strtolower( $product->get_title() ) );

		return [
			'key'                  => $cart_item['key'],
			'id'                   => $product->get_id(),
            'slug'                 => $product_slug,
            'category_slug'        => $category_slug,
			'type'                 => $product->get_type(),
			'quantity'             => wc_stock_amount( $cart_item['quantity'] ),
			'quantity_limits'      => (object) ( new QuantityLimits() )->get_cart_item_quantity_limits( $cart_item ),
			'name'                 => $this->prepare_html_response( $product->get_title() ),
			'short_description'    => $this->prepare_html_response( wc_format_content( wp_kses_post( $product->get_short_description() ) ) ),
			'description'          => $this->prepare_html_response( wc_format_content( wp_kses_post( $product->get_description() ) ) ),
			'sku'                  => $this->prepare_html_response( $product->get_sku() ),
			'low_stock_remaining'  => $this->get_low_stock_remaining( $product ),
			'backorders_allowed'   => (bool) $product->backorders_allowed(),
			'show_backorder_badge' => (bool) $product->backorders_require_notification() && $product->is_on_backorder( $cart_item['quantity'] ),
			'sold_individually'    => $product->is_sold_individually(),
			'permalink'            => $product_permalink,
			'images'               => $this->get_images( $product ),
			'variation'            => $this->format_variation_data( $cart_item['variation'], $product ),
			'item_data'            => $this->get_item_data( $cart_item ),
			'prices'               => (object) $this->prepare_product_price_response( $product, get_option( 'woocommerce_tax_display_cart' ) ),
			'totals'               => (object) $this->prepare_currency_response(
				[
					'line_subtotal'     => $this->prepare_money_response( $cart_item['line_subtotal'], wc_get_price_decimals() ),
					'line_subtotal_tax' => $this->prepare_money_response( $cart_item['line_subtotal_tax'], wc_get_price_decimals() ),
					'line_total'        => $this->prepare_money_response( $cart_item['line_total'], wc_get_price_decimals() ),
					'line_total_tax'    => $this->prepare_money_response( $cart_item['line_tax'], wc_get_price_decimals() ),
				]
			),
			'catalog_visibility'   => $product->get_catalog_visibility(),
			self::EXTENDING_KEY    => $this->get_extended_data( self::IDENTIFIER, $cart_item ),
		];
	}

	/**
	 * Format cart item data removing any HTML tag.
	 *
	 * @param array $cart_item Cart item array.
	 * @return array
	 */
	protected function get_item_data( $cart_item ) {
		/**
		 * Filters cart item data.
		 *
		 * Filters the variation option name for custom option slugs.
		 *
		 * @since 4.3.0
		 *
		 * @internal Matches filter name in WooCommerce core.
		 *
		 * @param array $item_data Cart item data. Empty by default.
		 * @param array $cart_item Cart item array.
		 * @return array
		 */
		$item_data       = apply_filters( 'woocommerce_get_item_data', array(), $cart_item );
		$clean_item_data = [];
		foreach ( $item_data as $data ) {
			// We will check each piece of data in the item data element to ensure it is scalar. Extensions could add arrays
			// to this, which would cause a fatal in wp_strip_all_tags. If it is not scalar, we will return an empty array,
			// which will be filtered out in get_item_data (after this function has run).
			foreach ( $data as $data_value ) {
				if ( ! is_scalar( $data_value ) ) {
					continue 2;
				}
			}
			$clean_item_data[] = $this->format_item_data_element( $data );
		}
		return $clean_item_data;
	}

	/**
	 * Remove HTML tags from cart item data and set the `hidden` property to `__experimental_woocommerce_blocks_hidden`.
	 *
	 * @param array $item_data_element Individual element of a cart item data.
	 * @return array
	 */
	protected function format_item_data_element( $item_data_element ) {
		if ( array_key_exists( '__experimental_woocommerce_blocks_hidden', $item_data_element ) ) {
			$item_data_element['hidden'] = $item_data_element['__experimental_woocommerce_blocks_hidden'];
		}
		return array_map( 'wp_strip_all_tags', $item_data_element );
	}
}
