<?php

$username = $data->settings['access_value'];
$size = ($data->js_config['type'] == 'custom-grid') ? 'large' : 'original';
$limit = !empty( $data->settings['img_count'] ) ? $data->settings['img_count'] : 9 ;
$target = $data->settings['link_target'];
$link ='Follow Me!';
$data->settings['margin'] = $data->settings['item_margin'];
$data->js_config['margin'] = $data->settings['item_margin'];
$access_mode = $data->settings['access_mode'];
$media_array = scrape_instagram($username, $access_mode); 

if( is_object( $media_array ) ) {
    echo "Error To Load Data";
    return;
}

if ( is_array( $media_array ) && count( $media_array ) >  $limit  ) { 
    $media_array = array_slice($media_array, 0, $limit); 
} 
$classes = apply_filters('igp_gallery_extra_classes', 'portfolio-wp portfolio-gallery ' . $data->settings['extra_classes'], $data->settings);
$items_attributes = apply_filters('igp_gallery_items_attributes', array(), $data->settings); ?>




<div id="<?php echo esc_attr($data->gallery_id) ?>"
    class="<?php echo esc_attr($classes); ?> <?php echo ($data->settings['align'] != '') ? esc_attr('align' . $data->settings['align']) : ''; ?>"
    data-config="<?php echo esc_attr(json_encode($data->js_config)) ?>">

    <?php $layout = $data->settings['layout']; 
    require "design/{$layout}/index.php";  ?>
</div>
<?php return;