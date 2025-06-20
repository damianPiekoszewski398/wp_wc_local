<?php

// Ref: https://www.uyond.com/resource/use-external-url-on-woocommerce-product-images/

defined( 'ABSPATH' ) || exit;

class Gp_External_Image {

    protected static $instance;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_post' ) );

        // show the product image in shop list.
        add_filter( 'woocommerce_product_get_image', array( $this, 'get_image' ), 10, 6 );
//        add_filter( 'woocommerce_product_get_image', array( $this, 'get_image' ), 10, 6 );
//        add_filter('post_thumbnail_html', array( $this, 'get_image' ), 10, 5);
//        add_filter( 'woocommerce_get_product_thumbnail', array( $this, 'get_image' ), 10, 6 );

        // show the gallery images in single product page.
        add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'thumbnail_html' ), 10, 2 );
        add_filter( 'wc_get_template', array( $this, 'get_template' ), 10, 5 );
    }

    public function add_meta_boxes() {
        add_meta_box(
            'uyond_product_img_url',
            'Product Image URL',
            array( $this, 'echo_product_img_url_box' ),
            'product',
            'side',
            'default'
        );

        add_meta_box(
            'gp_product_gallery_urls',
            'Product Gallery URL',
            array( $this, 'echo_product_gallery_url_box' ),
            'product',
            'side',
            'default'
        );
    }

    public function echo_product_img_url_box( $post ) {
        wp_nonce_field( 'uyond_product_img_url_metabox_nonce', 'uyond_product_img_url_nonce' );

        $img_url = $this->get_product_img_url( $post->ID );

        if ( $img_url ) {
            ?>
            <img style="max-width: 100%;" src="<?php echo esc_url( $img_url ); ?>" />
            <?php
        }

        ?>
        <input id="uyond_product_img_url" type="text" name="uyond_product_img_url" placeholder="Image URL" value="<?php echo esc_url( $img_url ); ?>" style="width:100%;font-size:13px;">
        <?php
    }

    public function echo_product_gallery_url_box( $post ) {
        wp_nonce_field( 'gp_product_gallery_url_metabox_nonce', 'gp_product_gallery_url_nonce' );

        $gallery_urls = $this->get_product_gallery_url( $post->ID );

        for ( $i = 0; $i < 10; $i++ ) {
            $gallery_url = '';
            if ( $i < count( $gallery_urls ) ) {
                $gallery_url = $gallery_urls[ $i ];
            }

            if ( $gallery_url ) {
                ?>
                <img style="max-width: 50%;" src="<?php echo esc_url( $gallery_url ); ?>" />
                <?php
            }

            ?>
            <input type="text" id="gp_product_gallery_url_<?php echo esc_html( $i ); ?>"  name="gp_product_gallery_url[]" placeholder="Gallery URL <?php echo esc_html( $i ); ?>" value="<?php echo esc_url( $gallery_url ); ?>" style="width:100%; font-size:13px;">
            <?php
        }

    }

    public function save_post( $post_id ) {
        if ( get_post_type( $post_id ) !== 'product' ) {
            return;
        }

        $this->save_post_product_url( $post_id );
        $this->save_post_product_gallery_url( $post_id );
    }

    public function save_post_product_url( $post_id ) {
        if ( ! isset( $_POST['uyond_product_img_url'] ) ) {
            return;
        }

        if ( ! isset( $_POST['uyond_product_img_url_nonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( sanitize_key( $_POST['uyond_product_img_url_nonce'] ), 'uyond_product_img_url_metabox_nonce' ) ) {
            return;
        }

        $url = esc_url_raw( rtrim( $_POST['uyond_product_img_url'] ) );

        if ( $url ) {
            update_post_meta( $post_id, '_gp_product_img_url', $url );
        } else {
            delete_post_meta( $post_id, '_gp_product_img_url' );
        }
    }

    public function save_post_product_gallery_url( $post_id ) {
        if ( ! isset( $_POST['gp_product_gallery_url'] ) ) {
            return;
        }

        if ( ! isset( $_POST['gp_product_gallery_url_nonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( sanitize_key( $_POST['gp_product_gallery_url_nonce'] ), 'gp_product_gallery_url_metabox_nonce' ) ) {
            return;
        }

        $urls = $_POST['gp_product_gallery_url'];

        if ( $urls ) {
            update_post_meta( $post_id, '_gp_product_gallery_url', $urls );
        } else {
            delete_post_meta( $post_id, '_gp_product_gallery_url' );
        }
    }

    public function get_product_img_url( $id ) {
        $value = get_post_meta( $id, '_gp_product_img_url', true );

        if ( $value ) {
            return $value;
        }

        return '';
    }

    public function get_product_gallery_url( $id ) {
        $value = get_post_meta( $id, '_gp_product_gallery_url', true );

        if ( $value ) {
            return $value;
        }

        return array();
    }

    public function get_image( $html, $product, $woosize, $attr, $placeholder, $image ) {
        $img_url = $this->get_product_img_url( $product->get_id() );
        return '<img width="260" height="300" src="' . esc_url( $img_url ) . '" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" loading="lazy" />';
    }

    public function get_template( $template, $template_name, $args, $template_path, $default_path ) {
        if ( 'single-product/product-thumbnails.php' === $template_name ) {
            $template = path_join( get_stylesheet_directory(), 'templates/single-product/product-thumbnails.php' );
        }

        return $template;
    }

    public function get_gallery_single_image( $img_url ) {
        return sprintf(
            '<div data-thumb="%1$s" data-thumb-alt="" class="woocommerce-product-gallery__image"><a href="%1$s"><img width="600" height="642" src="%1$s" class="" alt="" loading="lazy" title="61S2qlMWh6L._AC_SX679_" data-caption="" data-src="%1$s" data-large_image="%1$s" data-large_image_width="679" data-large_image_height="727" /></a></div>',
            $img_url
        );
    }

    public function thumbnail_html( $html, $post_thumbnail_id ) {
        global $product;
        $img_url = $this->get_product_img_url( $product->get_id() );

        if ( '' === $img_url ) {
            return $html;
        }

        return $this->get_gallery_single_image( $img_url );
    }
}