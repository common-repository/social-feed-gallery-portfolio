<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php 
wp_enqueue_style( "igp_wp_index_style_1", IGP_WP_ASSETS. "css/layout-design.css",'');
?>

    <?php
    if($data->js_config['type']=='custom-grid')
    { ?>
        <div class="row">
    <?php } else { ?>
        <div class="row grid" id="grid">
    <?php }
        
          foreach ( $media_array as $image ) { ?>

            <?php
            if($data->js_config['type']=='custom-grid')
            { ?> 
                <div class="col-md-<?php echo $data->settings['select_column']; ?>">
                    <div  style="margin: <?php echo $data->settings['margin'] ?>px;" >
                    <img src="<?php echo $image[$size]; ?>">
             <?php } else 
             { ?>
                <div class="col-md-<?php echo $data->settings['select_column']; ?> project">
                    <div style="margin: <?php echo $data->settings['margin'] ?>px;" >
                        <img src="<?php echo $image['original']; ?>">
             <?php } ?> 


         </div><!-- margin -->
     </div><!-- col -->
	<?php } ?>
    </div><!-- row -->

<?php return; ?>