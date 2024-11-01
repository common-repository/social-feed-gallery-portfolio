<?php 
	if (! defined('ABSPATH')) {
		exit;
	} 
	wp_enqueue_style("igp_wp_index_style_2", IGP_WP_ASSETS. "css/layout-design.css", '');
?>


<?php
    if ($data->js_config['type']=='custom-grid') { ?>
	<div class="row">
		<?php } else { ?>
			<div class="row grid" id="grid">
				<?php }
			foreach ($media_array as $image) {
				// Create array with data in order to send it to image template
				/*--------------------------*/
				// $data->loader->set_template_data($item_data); ?>

				<div class="col-md-<?php echo $data->settings['select_column']; ?> project">
					<div class="layout-2"
						style="margin: <?php echo $data->settings['margin'] ?>px;">
						<img src="<?php echo $image[$size]; ?>">
						<div class="layout-content">
							<?php if ( ! $data->settings['hide_description'] ): ?>
								<p class="description"><?php echo $image['description']; ?></p>
							<?php endif ?>
							
						</div>
						<div class="icon">
							<span>
								<?php if ($image['link'] != '') { ?>
									<a href="<?php echo $image['link']; ?>" target="<?php echo $target; ?>">
										<i class="fab fa-instagram"></i>
									</a>
								<?php } else { ?>
									<a>
										<i class="fab fa-instagram"></i>
									</a>
								<?php } ?>
							</span>
							<span>
								<a class="popup-box"
									href="<?php echo $image['original']; ?>"
									data-lightbox="image">
									<i class="fa fa-plus"></i>
								</a>
							</span>
						</div><!-- icon -->
					</div><!-- layout-2 -->
				</div><!-- col --><?php
			} ?>
	</div> <!-- row -->
	
	<?php return ;?>