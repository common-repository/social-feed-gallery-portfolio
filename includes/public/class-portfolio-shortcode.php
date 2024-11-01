<?php

/**
 *
 */
class Portfolio_Shortcode {


	private $loader;

	function __construct() {

		$this->loader  = new Portfolio_Template_Loader();

		add_shortcode( 'igp-wp', array( $this, 'gallery_shortcode_handler' ) );
		add_shortcode( 'Igp-wp', array( $this, 'gallery_shortcode_handler' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'igp_gallery_scripts' ) );

		// Add shortcode related hooks
		add_filter( 'portfolio_shortcode_item_data', 'portfolio_generate_image_links', 10, 3 );
		add_filter( 'portfolio_shortcode_item_data', 'portfolio_check_lightboxes_and_links', 15, 3 );
		add_filter( 'portfolio_shortcode_item_data', 'portfolio_check_hover_effect', 20, 3 );
		add_filter( 'portfolio_shortcode_item_data', 'portfolio_check_custom_grid', 25, 3 );
	}

	public function igp_gallery_scripts() {

		wp_enqueue_style( 'portfolio_lightbox2_stylesheet', IGP_WP_ASSETS . 'css/lightbox.min.css', null, IGP_WP_CURRENT_VERSION );

		wp_enqueue_style( 'portfolio-css', IGP_WP_ASSETS . 'css/portfolio.css', null, IGP_WP_CURRENT_VERSION );

		// Scripts necessary for some galleries
		wp_enqueue_script( 'portfolio_lightbox2_script', IGP_WP_ASSETS . 'js/lightbox.min.js', array( 'jquery' ), IGP_WP_CURRENT_VERSION, true );
		wp_enqueue_script( 'portfolio_packery', IGP_WP_ASSETS . 'js/packery.min.js', array( 'jquery' ), IGP_WP_CURRENT_VERSION, true );
		

		wp_enqueue_style( 'bootstrap-css', IGP_WP_ASSETS . 'css/bootstrap.css', null, IGP_WP_CURRENT_VERSION );
		wp_enqueue_style('igp-font-awesome-5.0.8', IGP_WP_ASSETS.'css/font-awesome-latest/css/fontawesome-all.min.css');

	}


	public function gallery_shortcode_handler( $atts ) {

		$default_atts = array(
			'id' => false,
			'align' => '',
		);
	   
		
		$atts = wp_parse_args( $atts, $default_atts );
		
		if ( ! $atts['id'] ) {
			return esc_html__( 'Gallery not found.', igp_wp );
		}
		
		/* Generate uniq id for this gallery */
		$gallery_id = 'igp-' . $atts['id'];
		
		// Check if is an old Portfolio post or new.
		$gallery = get_post( $atts['id'] );

		if ( 'igp_wp' != get_post_type( $gallery ) ) {
			$gallery_posts = get_posts( array(
				'post_type' => 'igp_wp',
				'post_status' => 'publish',
				'meta_query' => array(
					array(
						'key'     => 'portfolio-id',
						'value'   => $atts['id'],
						'compare' => '=',
					),
				),
			) );


			if ( empty( $gallery_posts ) ) {
				return esc_html__( 'Gallery not found.', igp_wp );
			}

			$atts['id'] = $gallery_posts[0]->ID;

		}

		/* Get gallery settings */
		$settings = get_post_meta( $atts['id'], 'portfolio-wp-settings', true );
		$default  = Portfolio_WP_CPT_Fields_Helper::get_defaults();
		$settings = wp_parse_args( $settings, $default );

		$type = 'creative-gallery';
		if ( isset( $settings['type'] ) ) {
			$type = $settings['type'];
		}else{
			$settings['type'] = 'creative-gallery';
		}
		
		$pre_gallery_html = apply_filters( 'portfolio_pre_output_filter_check', false, $settings, $gallery );

		if ( false !== $pre_gallery_html ) {

			// If there is HTML, then we stop trying to display the gallery and return THAT HTML.
			$pre_output =  apply_filters( 'portfolio_pre_output_filter','', $settings, $gallery );
			return $pre_output;

		}


		/* Get gallery images */
		$images = apply_filters( 'igp_gallery_before_shuffle_images', get_post_meta( $atts['id'], 'portfolio-images', true ), $settings );
		if ( isset( $settings['shuffle'] ) && '1' == $settings['shuffle'] && 'creative-gallery' == $type ) {
			shuffle( $images );
		}
		$images = apply_filters( 'igp_gallery_images', $images, $settings );

		if ( empty( $settings ) ) {
			return esc_html__( 'Gallery not found.', igp_wp );
		}

		if ( 'custom-grid' == $type ) {
			wp_enqueue_script( 'packery' );
		}


		/* Enqueue lightbox related scripts & styles */
		switch ( $settings['lightbox'] ) {
			case "lightbox2":
				wp_enqueue_style( 'lightbox2_stylesheet' );
				wp_enqueue_script( 'lightbox2_script' );
				wp_add_inline_script( 'lightbox2_script', 'jQuery(document).ready(function(){lightbox.option({albumLabel: "' . esc_html__( 'Image %1 of %2', igp_wp ) . '" });});' );
				break;
			default:
				do_action( 'portfolio_lighbox_shortcode', $settings['lightbox'] );
				break;
		}

		do_action('portfolio_extra_scripts',$settings);

		// Main CSS & JS
		$necessary_scripts = apply_filters( 'portfolio_necessary_scripts', array( 'portfolio' ),$settings );
		$necessary_styles  = apply_filters( 'portfolio_necessary_styles', array( 'portfolio' ), $settings );

		if ( ! empty( $necessary_scripts ) ) {
			foreach ( $necessary_scripts as $script ) {
				wp_enqueue_script( $script );
			}
		}

		if ( ! empty( $necessary_styles ) ) {
			foreach ( $necessary_styles as $style ) {
				wp_enqueue_style( $style );
			}
		}


		$settings['gallery_id'] = $gallery_id;
		$settings['align']      = $atts['align'];

		$template_data = array(
			'gallery_id' => $gallery_id,
			'settings'   => $settings,
			'images'     => $images,
			'loader'     => $this->loader,
		);

		ob_start();

		/* Config for gallery script */
		$js_config = array(
			"margin"          => absint( $settings['margin'] ),
			'type'            => $type,
			'columns'         => 12,
			'gutter'          => isset( $settings['gutter'] ) ? absint($settings['gutter']) : 10,
		);
		

		if($js_config['type']=='creative-gallery')
		{
			wp_enqueue_style( 'masonry-default-css', IGP_WP_MASONRY . '/css/default.css', null, IGP_WP_CURRENT_VERSION );
			wp_enqueue_style( 'masonry-component-css', IGP_WP_MASONRY . '/css/component.css', null, IGP_WP_CURRENT_VERSION );

			// Scripts necessary for some galleries
			wp_enqueue_script( 'masonry_modernizr_script', IGP_WP_MASONRY . '/js/modernizr.custom.js', array( ), IGP_WP_CURRENT_VERSION, true );
	  		wp_enqueue_script( 'portfolio_masonry_script', IGP_WP_MASONRY . '/js/masonry.pkgd.min.js', array( ) , IGP_WP_CURRENT_VERSION, true );
			wp_enqueue_script( 'masonry_imagesloaded_script', IGP_WP_MASONRY . '/js/imagesloaded.js', array( ), IGP_WP_CURRENT_VERSION, true );
			wp_enqueue_script( 'masonry_classie_script', IGP_WP_MASONRY . '/js/classie.js', array( ), IGP_WP_CURRENT_VERSION, true );
			wp_enqueue_script( 'masonry_anim_on_scroll_script', IGP_WP_MASONRY . '/js/AnimOnScroll.js', array( ), IGP_WP_CURRENT_VERSION, true );
			wp_enqueue_script( 'masonry_script', IGP_WP_MASONRY . '/js/masonry-script.js', array( ), IGP_WP_CURRENT_VERSION, true );
		}

		$template_data['js_config'] = apply_filters( 'igp_gallery_settings', $js_config, $settings );
		$template_data              = apply_filters( 'igp_gallery_template_data', $template_data );

	
		
		echo $this->generate_gallery_css( $gallery_id, $settings );
				
		
		$this->loader->set_template_data( $template_data );

    	$this->loader->get_template_part( 'portfolio', 'gallery' ); //load portfolio-gallery.php

    	$html = ob_get_clean();
    	return $html;
	}
	
	private function generate_gallery_css( $gallery_id, $settings ) {

		$css = "<style>";

		if ( $settings['borderSize'] ) {
			$css .= "#{$gallery_id} img{ border: " . absint($settings['borderSize']) . "px solid " . sanitize_hex_color($settings['borderColor']) . "; }";
		}

		if ( $settings['borderRadius'] ) {
			$css .= "#{$gallery_id} img { border-radius: " . absint($settings['borderRadius']) . "px; }";
		}

		if ( $settings['shadowSize'] ) {
			$css .= "#{$gallery_id} img { box-shadow: " . sanitize_hex_color($settings['shadowColor']) . " 0px 0px " . absint($settings['shadowSize']) . "px; }";
		}

		/*	if ( $settings['socialIconColor'] ) {
				$css .= "#{$gallery_id} .portfolio-item .igp-social a { color: " . sanitize_hex_color($settings['socialIconColor']) . " }";
			}*/

		$css .= "#{$gallery_id} .portfolio-item .caption { background-color: " . sanitize_hex_color($settings['captionColor']) . ";  }";
		if ( '' != $settings['captionColor'] || '' != $settings['captionFontSize'] ) {
			$css .= "#{$gallery_id} .portfolio-item .figc {";
			if ( '' != $settings['captionColor'] ) {
				$css .= 'color:' . sanitize_hex_color($settings['captionColor']) . ';';
			}
			$css .= '}';
		}

		if ( '' != $settings['titleFontSize'] && 0 != $settings['titleFontSize'] ) {
			$css .= "#{$gallery_id} .igp-title {  font-size: " . absint($settings['titleFontSize']) . "px; }";
		}

		$css .= "#{$gallery_id} .portfolio-item { transform: scale(" . absint( $settings['loadedScale'] ) / 100 . "); }";

		if ( 'custom-grid' != $settings['type'] ) {
			$css .= "#{$gallery_id} { width:" . esc_attr($settings['width']) . ";}";
			//$css .= "#{$gallery_id} .portfolio-item{height:" . absint( $settings['height'] ) . "px;}";
		}

		$css .= "#{$gallery_id}  p.description { color:" . sanitize_hex_color($settings['captionColor']) . ";font-size:" . absint($settings['captionFontSize']) . "px; }";

		if ( '' != $settings['titleColor'] ) {
			$css .= "#{$gallery_id}  .igp-title { color:" . sanitize_hex_color($settings['titleColor']) . "; }";
		}else{
			$css .= "#{$gallery_id}  .igp-title { color:" . sanitize_hex_color($settings['captionColor']) . "; }";
		}

		$css = apply_filters( 'portfolio_shortcode_css', $css, $gallery_id, $settings );


		if ( strlen( $settings['style'] ) ) {
			$css .= esc_html($settings['style']);
		}

		$css .= "</style>\n";

		return $css;

	}
}

new Portfolio_Shortcode();