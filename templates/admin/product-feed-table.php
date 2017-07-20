
<?php
    $functions =  YITH_Google_Product_Feed()->functions;
    $feed_url = $functions->create_feed('google','xml');
?>

<div>
    <h2><?php _e('Google Product Feed','yith-google-product-feed-for-woocommerce');?></h2>
    <div><?php _e('Your feed is available here: ','yith-google-product-feed-for-woocommerce'); ?><a target="_blank" href="<?php echo $feed_url ?>"><?php echo $feed_url?></a></div>

</div>
