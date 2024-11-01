<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php 
wp_enqueue_style( "igp_wp_index_style_4", IGP_WP_ASSETS. "css/layout-design.css",'');
?>

<div class="layout-4">
	<?php
	if($data->js_config['type']=='custom-grid')
	{ ?>
		<div class="row">
	<?php }	else { ?>
		<div class="row grid" id="grid">
	<?php } 

			foreach ( $media_array as $image ) { ?>

			<div class="col-md-<?php echo $data->settings['select_column']; ?> project">
				<?php
				if($data->js_config['type']=='custom-grid')
				{ ?>
					<div style="margin: <?php echo $data->settings['margin'] ?>px;">
						<img src="<?php echo $image[$size]; ?>" >
				<?php }	else { ?>
					<div style="margin: <?php echo $data->settings['margin'] ?>px;" >
						<img src="<?php echo $image[$size]; ?>">
				<?php } ?>

					<div class="dimmer">
						<div class="overlay" style="">

							<?php if($image['link'] != '') { ?>

								<a href="<?php echo $image['link']; ?>"  target="<?php echo $target; ?>" class="link" title="">
									<i class="fa fa-link"></i>
								</a>

	                 		<?php } else { ?>
	                 			<a class="link" title=""><i class="fa fa-link"></i></a>
	                 		<?php } ?>
							<?php if ( ! $data->settings['hide_description'] ): ?>
								<p class="description"><?php echo $image['description']; ?></p>
							<?php endif ?>
	                        <?php if($data->settings['lightbox'] == 'lightbox2') { ?>
		                        	<a class="popup-box" href="<?php echo $image['original']; ?>" data-lightbox="image">
		                           		 <i class="fa fa-plus"></i>
			                        </a>		
								<?php } elseif($data->settings['lightbox'] == 'direct') { ?>	
									<a class="popup-box" href="<?php echo $image['original']; ?>"  target="_blank" >
			                            <i class="fa fa-plus"></i>
			                        </a>				                
								<?php } else { ?>
									<a class="popup-box">
									 	<i class="fa fa-plus"></i>
									 </a>
								<?php } ?>
						</div><!-- overlay -->
					</div><!-- dimmer -->

				</div><!-- margin or gutter -->
			</div><!-- col -->
			<?php } ?>
	</div><!-- row -->
</div>