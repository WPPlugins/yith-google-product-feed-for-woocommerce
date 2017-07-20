<?php
/**
 * Class Generate Google Product Feed
 *
 * @author  Yithemes
 * @package YITH Google Product Feed for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCGPF_VERSION' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCGPF_Generate_Feed_google' ) ) {
    /**
     * YITH_WCGPF_Generate_Feed_google
     *
     * @since 1.0.0
     */
    class YITH_WCGPF_Generate_Feed_google extends YITH_WCGPF_Generate_Feed
    {
        public function __construct($feed_id = '' ,$feed_type = 'xml')
        {
            parent::__construct($feed_id,$feed_type);
        }

        /**
         * Create the feed
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return array
         */

        function create_feed( $feed_id,$feed_type ) {
            
            if (!$feed_id) {
                $google_merchant =  YITH_Google_Product_Feed()->merchant_google;
                $values = $google_merchant->google_rows();
            } else {
                $values = get_post_meta($feed_id,'yith_wcgpf_save_feed',true);
            }
            $products = YITH_Google_Product_Feed()->products;
            $product_ids = $products->get_products();;
            $this->create_feed_xml( $values,$product_ids );
        }
        
        /**
         * Create feed xml
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $values
         */
        
        function create_feed_xml( $values, $product_ids ){
            $products = $this->get_products_mapping($product_ids);
            $head = $this->get_header_xml();
            $content ='';
            foreach ($products as $product) {
              $content .= $this->get_content_xml( $product, $values );
            }
            $footer = $this->get_footer_xml();

            $feed = $head.$content.$footer;

            echo $feed;

            die();
        }

        /**
         * Get content xml for each product
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $content
         */
        function get_content_xml( $product, $values ) {
            $content = '';
            $product_field_array = array();
            foreach( $values as $fields ) {
                $value = false;
                if( isset($fields['value']) ) {
                    
                    if  ( !empty($product[$fields['value']] )  && isset( $product[$fields['value']] ) ) {

                            $value = $product[$fields['value']];

                    } elseif( substr($fields['value'],0,strlen('yith_wcgpf_pfd_')) == 'yith_wcgpf_pfd_' ){
                        $product_field = substr($fields['value'],strlen('yith_wcgpf_pfd_'));

                            $variable = 'yith_wcgpf_tab_google_' . $product_field;
                            $value = get_option($variable,false);

                        if( $value && !empty($value ) ) {

                            $product_field_array[] = $product_field;
                        }
                   }
                }
                if ($value || !empty($value)) {
                    $content.= $this->print_content( $fields['attributes'], $value);
                }
            }
            if ( $content ) {
                    $content = $this->add_identifier_exists($content, $product_field_array,1);
            }
            return '<item>'.apply_filters('yith_wcgpf_get_content_xml',$content).'</item>';
        }

        /**
         * Mapping Product
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return $values
         */

        function get_products_mapping($posts){

            $products = array();
            $i = 0;
            foreach ( $posts as $post ) {
                $product = wc_get_product($post);
                 if(!$product || 'variable' == $product->get_type() ) {
                    continue;
                }

                $products[$i]['id'] =  $product->get_id();
                $products[$i]['title'] =  $product->get_title();
                $products[$i]['description'] = yit_get_prop($product,'description',true);
                $products[$i]['link'] =  get_permalink( $product->get_id() );
                $products[$i]['availability'] = ($availability = $product->is_in_stock()) ? 'in stock' : 'out of stock';
                $products[$i]['price'] = ($product->get_regular_price()) ? $product->get_regular_price().' '.get_woocommerce_currency() : '';
                $products[$i]['image_link'] = $this->get_image_link($product->get_image_id());
                $products[$i]['mpn'] = ($product->get_sku()) ? $product->get_sku() : '';

                $i++;

            }
            return apply_filters('yith_wcgpf_get_products_mapping',$products,$posts);
        }

        /**
         * Return image link
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        function get_image_link($product_image_id) {

            $image_link = wp_get_attachment_url($product_image_id);
            if( !$image_link ) {
                $image_link = wc_placeholder_img_src();
            }
            return $image_link;
        }


        /**
         * Print the content for each field
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        function print_content($attributes,$value,$prefix ='',$suffix=''){

            return '<g:'.$attributes.'>'.$this->CDATA( $prefix.$value.$suffix ).'</g:'.$attributes.'>';
        }

        /**
         * Add identifier_exists_attribute
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        function add_identifier_exists( $content,$array,$flag='' ) {

            $attribute = 'identifier_exists';
            if ( ( in_array('brand',$array ) ) && ( ( in_array('mpn',$array ) || in_array('gtin',$array ) ) ) ) {
                $value = 'yes';
            } else {
                $value = 'no';
            }

            if ($flag) {
                $content.= $this->print_content($attribute,$value);
            }else {
                $content[] = $value;
            }
            
            return $content;
        }
    }
}