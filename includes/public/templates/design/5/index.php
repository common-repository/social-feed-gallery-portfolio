<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php wp_enqueue_style( "igp_wp_index_style_5", IGP_WP_ASSETS. "css/layout-design.css",''); ?>

	<?php
	if($data->js_config['type']=='custom-grid')
	{ ?>
		<div class="row">
	<?php }	else { ?>
		<div class="row grid" id="grid">
	<?php }
		    foreach ( $media_array as $image ): ?>
		
				<?php
			if($data->js_config['type']=='custom-grid')
			{ ?>
				<div class="col-md-<?php echo $data->settings['select_column']; ?>">
					<div class="layout-5" style="margin: <?php echo $data->settings['margin'] ?>px;">
						<div class="layout-inner">
							<div class="layout-image">

							<?php if($image['link'] != '') { ?>
	           		 			<a href="<?php echo $image['link']; ?>" target="<?php echo $target; ?>">
	                        		<img src="<?php echo $image[$size]; ?>">
	                 	    	</a>
	                 		<?php } else { ?>
	                 			<a>
	                        		<img src="<?php echo $image[$size]; ?>">
	                 	   		</a>
	                 		<?php } ?>

								
			<?php }	else { ?>
				<div class="col-md-<?php echo $data->settings['select_column']; ?> project">
					<div class="layout-5" style="margin: <?php echo $data->settings['margin'] ?>px;" >
						<div class="layout-inner">
							<div class="layout-image">
								<?php if($image['link'] != '') { ?>
	           		 			<a href="<?php echo $image['link']; ?>" target="<?php echo $target; ?>">
	                        		<img src="<?php echo $image['original']; ?>">
	                 	    	</a>
		                 		<?php } else { ?>
		                 			<a>
		                        		<img src="<?php echo $image['original']; ?>">
		                 	   		</a>
		                 		<?php } ?>
									
			<?php } ?>


							
							</div><!-- ld-pf-image -->
							<div class="layout-content" <?php if( $data->settings['hide_title'] &&  $data->settings['hide_description']){ ?>style="padding: 0px !important;" <?php  } ?> >
								<?php if ( ! $data->settings['hide_title'] ): ?>
									
										<?php if($data->settings['lightbox'] == 'lightbox2') { ?>
				                        	<a class="popup-box" href="<?php echo $image['original']; ?>" data-lightbox="image">
					                        </a>		
										<?php } elseif($data->settings['lightbox'] == 'direct') { ?>	
											<a class="popup-box" href="<?php echo $image['original']; ?>" 
												<?php if($image['target'] == '1') { ?> target="_blank" <?php } ?> >
					                        </a>				                
										<?php } ?>
									

								<?php endif ?>
								<?php if ( ! $data->settings['hide_description'] ): ?>
									<p class="description"><?php echo $image['description']; ?></p>
								<?php endif ?>
							</div>
							

						</div><!-- ld-pf-inner -->
					</div><!-- layout -->
				</div><!-- col -->	
				
				

		<?php endforeach; ?>
		

	</div><!-- row -->
