<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( !defined( 'YITH_WCGPF_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_WCGPF_Admin
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */

if ( !class_exists( 'YITH_WCGPF_Admin' ) ) {
    /**
     * Class YITH_WCGPF_Admin
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_WCGPF_Admin {

        /**
         * @var Panel object
         */
        protected $_panel = null;


        /**
         * @var Panel page
         */
        protected $_panel_page = 'yith_wcgpf_panel';

        /**
         * @var bool Show the premium landing page
         */
        public $show_premium_landing = true;

        /**
         * @var string Official plugin documentation
         */
        protected $_official_documentation = '//docs.yithemes.com/yith-google-product-feed-for-woocommerce/';

        /**
         * @var string Premium version landing link
         */
        protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-google-product-feed-for-woocommerce';

        /**
         * Main Instance
         *
         * @var YITH_WCGPF_Admin
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Main plugin Instance
         *
         * @return
         * @var YITH_WCGPF_Admin instance
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public static function get_instance()
        {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$_instance ) ) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }

        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        protected function __construct() {
            /* === Register Panel Settings === */
            add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

            // Enqueue Scripts
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 11);

            add_action( 'yith_wcgpf_premium_tab', array( $this, 'show_premium_landing' ) );

            add_action('yith_wcgpf_settings_tab', array( $this, 'show_make_product_feed' ));
            add_action('yith_wcgpf_manage_tab', array($this, 'show_product_feeds_table'));
        }

        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @author   Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @use     /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function register_panel() {

            if ( !empty( $this->_panel ) ) {
                return;
            }

            $admin_tabs = apply_filters( 'yith_wcgpf_admin_tabs', array(
                    'manage' => __( 'Manage Feeds', 'yith-google-product-feed-for-woocommerce'),
                    'google-product-information' => __( 'Google general feed fields', 'yith-google-product-feed-for-woocommerce'),
                )
            );

            if( $this->show_premium_landing ){
                $admin_tabs['premium'] = __( 'Premium Version', 'yith-google-product-feed-for-woocommerce' );
            }

            $args = array(
                'create_menu_page' => true,
                'parent_slug'      => '',
                'page_title'       => _x( 'Google Product Feed', 'plugin name in admin page title', 'yith-google-product-feed-for-woocommerce' ),
                'menu_title'       => _x( 'Google Product Feed', 'plugin name in admin WP menu', 'yith-google-product-feed-for-woocommerce' ),
                'capability'       => 'manage_options',
                'parent'           => 'yith-google-product-feed-for-woocommerce',
                'parent_page'      => 'yit_plugin_panel',
                'page'             => $this->_panel_page,
                'admin-tabs'       => $admin_tabs,
                'options-path'     => YITH_WCGPF_OPTIONS_PATH,
                'links'            => $this->get_sidebar_link()
            );


            /* === Fixed: not updated theme/old plugin framework  === */
            if ( !class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
                require_once('../../plugin-fw/lib/yit-plugin-panel-wc.php' );
            }


            $this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

            add_action( 'woocommerce_admin_field_yith_google_product_feed_upload', array( $this->_panel, 'yit_upload' ), 10, 1 );
        }


        /**
         * Sidebar links
         *
         * @return   array The links
         * @since    1.2.1
         * @author   Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function get_sidebar_link() {
            $links = array(
                /*array(
                    'title' => __( 'Plugin documentation', 'yith-google-product-feed-for-woocommerce' ),
                    'url'   => $this->_official_documentation,
                ),*/
                array(
                    'title' => __( 'Help Center', 'yith-google-product-feed-for-woocommerce' ),
                    'url'   => 'http://support.yithemes.com/hc/en-us/categories/202568518-Plugins',
                ),
                array(
                    'title' => __( 'Support platform', 'yith-google-product-feed-for-woocommerce' ),
                    'url'   => 'https://yithemes.com/my-account/support/dashboard/',
                ),
                /*array(
                    'title' => sprintf( '%s (%s %s)', __( 'Changelog', 'yith-google-product-feed-for-woocommerce' ), __( 'current version', 'yith-google-product-feed-for-woocommerce' ), YITH_WCGPF_VERSION ),
                    'url'   => 'https://yithemes.com/docs-plugins/yith-woocommerce-multi-step-checkout/07-changelog-premium.html',
                ),*/
            );

            return $links;
        }



        /**
         * Enqueue styles and scripts
         *
         * @access public
         * @return void
         * @since 1.0.0
         * @author   Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function enqueue_styles_scripts() {
            wp_register_style( 'yith_wcgpf_admincss', YITH_WCGPF_ASSETS_URL . 'css/yith-wcgpf-admin.css', YITH_WCGPF_VERSION );
            wp_register_script( 'yith_wcgpf_adminjs', YITH_WCGPF_ASSETS_URL . 'js/yith-wcgpf-admin.js', array('jquery', 'jquery-ui-sortable','wc-enhanced-select'), YITH_WCGPF_VERSION );
            wp_register_script( 'yith_wcgpf_template_admin', YITH_WCGPF_ASSETS_URL . 'js/yith-wcgpf-template-admin.js', array('jquery', 'jquery-ui-sortable'), YITH_WCGPF_VERSION  );
            wp_register_script( 'yith_wcgpf_admin_product', YITH_WCGPF_ASSETS_URL . 'js/yith-wcgpf-admin-product.js', array('jquery', 'jquery-ui-sortable'), YITH_WCGPF_VERSION  );

            wp_localize_script( 'yith_wcgpf_adminjs', 'yith_wcgpf_adminjs', apply_filters( 'yith_wcgpf_admin_localize',array(
                'ajaxurl'    => admin_url( 'admin-ajax.php' ),
                'before_3_0' => version_compare( WC()->version, '3.0', '<' ) ? true : false,
                'search_categories_nonce' => wp_create_nonce( 'search-categories' ),
                'search_tags_nonce'       => wp_create_nonce( 'search-tags' ),
            )));
            wp_localize_script( 'yith_wcgpf_admin_product', 'yith_wcgpf_admin_product', apply_filters( 'yith_wcgpf_admin_product_localize',array(
                'ajaxurl'                 => admin_url( 'admin-ajax.php' ),
                'before_3_0' => version_compare( WC()->version, '3.0', '<' ) ? true : false,

            )));

            if( 'yith-wcgpf-feed' == get_post_type() ) {
                wp_enqueue_script('yith_wcgpf_adminjs');
            }


            if(is_admin()) {
                wp_enqueue_style('yith_wcgpf_admincss');
            }

            if( 'yith-wcgpf-template' == get_post_type() ) {
                wp_enqueue_script('yith_wcgpf_template_admin');
            }

            wp_enqueue_script('yith_wcgpf_admin_product');
        }

        /**
         * Show the premium landing
         *
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return void
         */
        public function show_premium_landing(){
            if( file_exists( YITH_WCGPF_TEMPLATE_PATH . 'premium/premium.php' )&& $this->show_premium_landing ){
                require_once( YITH_WCGPF_TEMPLATE_PATH . 'premium/premium.php' );
            }
        }
        

        /**
         * Show product feed table
         *
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return void
         */

        public function show_product_feeds_table() {
            wc_get_template( 'admin/product-feed-table.php', array(), '', YITH_WCGPF_TEMPLATE_PATH );
        }


        /**
         * Get the premium landing uri
         *
         * @since   1.0.0
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @return  string The premium landing link
         */
        public function get_premium_landing_uri() {
            return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing.'?refer_id=1030585';
        }
    }


}

function YITH_WCGPF_Admin() {
    return YITH_WCGPF_Admin::get_instance();
}