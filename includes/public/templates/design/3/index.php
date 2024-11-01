<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php wp_enqueue_style( "igp_wp_index_style_3", IGP_WP_ASSETS. "css/layout-design.css",''); ?>


	<?php
	if($data->js_config['type']=='custom-grid')
	{ ?>
		<div class="row">
	<?php }	else { ?>
		<div class="row grid" id="grid">
	<?php } 

		foreach ( $media_array as $image ) {  
			if($data->js_config['type']=='custom-grid') { ?>
				<div class="col-md-<?php echo $data->settings['select_column']; ?>">
					<div class="layout-3" style="margin: <?php echo $data->settings['margin'] ?>px;" >
						<img src="<?php echo $image[$size]; ?>">
			<?php }	else { ?>
				<div class="col-md-<?php echo $data->settings['select_column']; ?> project">
					<div class="layout-3" style="margin: <?php echo $data->settings['margin'] ?>px;" >
						<img src="<?php echo $image['original']; ?>">
			<?php } ?>

					<div class="layout-content">
		                <?php if ( ! $data->settings['hide_description'] ): ?>
							<p class="description"><?php echo $image['description']; ?></p>
						<?php endif ?>

	           		</div>

	           		<div class="icon">
	           		 	<span>
	           		 		<?php if($image['link'] != '') { ?>
								<a href="<?php echo $image['link']; ?>" target="<?php echo $target; ?>">
		                        	<i class="fa fa-link"></i>
		                 	    </a>
		                 	<?php } else { ?>
		                 		<a>
		                        	<i class="fa fa-link"></i>
		                 	    </a>
		                 	<?php } ?>
	                 	</span>
	           		 	<span>
	           		 		<?php if($data->settings['lightbox'] == 'lightbox2') { ?>
		                        	<a class="popup-box" href="<?php echo $image['original']; ?>" data-lightbox="image">
		                           		 <i class="fa fa-plus"></i>
			                        </a>		
								<?php } elseif($data->settings['lightbox'] == 'direct') { ?>	
									<a class="popup-box" href="<?php echo $image['original']; ?>" target="<?php echo $target; ?>">
			                            <i class="fa fa-plus"></i>
			                        </a>				                
								<?php } else { ?>
									<a class="popup-box">
									 	<i class="fa fa-plus"></i>
									 </a>
								<?php } ?>
	           		 	</span>
	           		</div><!-- icon -->
	           		</div><!-- layout-3 -->
			</div><!-- col -->
		<?php } ?>
	</div><!-- row -->

<?php return; ?>