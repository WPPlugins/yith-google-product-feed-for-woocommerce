<?php
/**
 * Class Helper Generate Product Feed
 *
 * @author  Yithemes
 * @package YITH Google Product Feed for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCGPF_VERSION' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCGPF_Helper' ) ) {
    /**
     * YITH_WCGPF_Helper
     *
     * @since 1.0.0
     */
    class YITH_WCGPF_Helper
    {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCGPF_Helper
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCGPF_Helper
         * @since 1.0.0
         */
        public static function get_instance()
        {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$_instance ) ) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }

        public $feed_type;
        public $merchant;
        public $feed_id;

        /**
         * @type array
         */
        public $allowed_merchant = array();

        public function __construct( )
        {
            $this->allowed_merchant = apply_filters('yith_wcgpf_allowed_merchant',array('google'));

            if( $this->is_url_for_generate_feed() ) {
                add_action('init',array($this,'generate_feed'));
            }
        }

        function is_url_for_generate_feed() {
            $is = isset( $_GET[ 'yith_wcgpf_feed' ] ) && isset( $_GET['merchant'] ) && in_array( $_GET[ 'merchant' ], $this->allowed_merchant );

            if ( $is ) {
                $this->feed_type = $_GET[ 'yith_wcgpf_feed' ];
                $this->merchant = $_GET[ 'merchant' ];
                $this->feed_id = isset($this->feed_id) ? $_GET[ 'merchant' ] :'';
            }
            return $is;
        }

        public function generate_feed() {
            $premium_suffix = defined( 'YITH_WCGPF_PREMIUM' ) && YITH_WCGPF_PREMIUM ? '_Premium' : '';
            $provider = 'YITH_WCGPF_Generate_Feed_'.$this->merchant.$premium_suffix;
            $generate_feed = new $provider($this->feed_id,$this->feed_type);
        }

    }
}