<?php
/**
 * The core plugin class.
 *
 * This is used to define admin-specific hooks, internationalization, and
 * public-facing site hooks.
 *
 * 
 */

class Portfolio_WP {
    

    private function load_dependencies() {

        require_once IGP_WP_INCLUDES . 'libraries/class-portfolio-template-loader.php';
        require_once IGP_WP_INCLUDES . 'helper/class-portfolio-wp-helper.php';
        
        require_once IGP_WP_INCLUDES . 'admin/class-portfolio-image.php';
        require_once IGP_WP_INCLUDES . 'public/portfolio-helper-functions.php';

        require_once IGP_WP_INCLUDES . 'admin/class-portfolio-wp-cpt.php';
       
        require_once IGP_WP_INCLUDES . 'admin/class-portfolio-admin.php';

        require_once IGP_WP_INCLUDES . 'public/class-portfolio-shortcode.php';
        


    }
    private function define_admin_hooks() {
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 20 );
        new Portfolio_WP_CPT();
    }
    private function define_public_hooks() {}
    
       
	/* Enqueue Admin Scripts */
	public function admin_scripts( $hook ) {

		global $id, $post;

        // Get current screen.
        $screen = get_current_screen();

        // Check if is Portfolio custom post type
        if ( 'igp_wp' !== $screen->post_type ) {
            return;
        }

        // Set the post_id
        $post_id = isset( $post->ID ) ? $post->ID : (int) $id;

		if ( 'post-new.php' == $hook || 'post.php' == $hook ) {

			/* CPT Styles & Scripts */
			// Media Scripts
			wp_enqueue_media( array(
	            'post' => $post_id,
	        ) );

	        $igp_wp_helper = array(
	        	'items' => array(),
	        	'settings' => array(),
	        	'strings' => array(
	        		'limitExceeded' => sprintf( __( 'You excedeed the limit of 30 photos. You can remove an image or %supgrade to pro%s', igp_wp ), '<a href="#" target="_blank">', '</a>' ),
	        	),
	        	'id' => $post_id,
	        	'_wpnonce' => wp_create_nonce( 'portfolio-ajax-save' ),
	        	'ajax_url' => admin_url( 'admin-ajax.php' ),
	        );

	        // Get all items from current gallery.
	        $images = get_post_meta( $post_id, 'portfolio-images', true );
	        
	        if ( is_array( $images ) && ! empty( $images ) ) {
	        	foreach ( $images as $image ) {
	        		if ( ! is_numeric( $image['id'] ) ) {
	        			continue;
	        		}

	        		$attachment = wp_prepare_attachment_for_js( $image['id'] );
	        		$image_url  = wp_get_attachment_image_src( $image['id'], 'large' );
					$image_full = wp_get_attachment_image_src( $image['id'], 'full' );

					$image['full']        = $image_full[0];
					$image['thumbnail']   = $image_url[0];
					$image['orientation'] = $attachment['orientation'];

					$igp_wp_helper['items'][] = $image;

	        	}
	        } 
	        else 
	        {   
	        	/*default image*/
	        	$igp_wp_helper['items'] =  get_option('igp-portfolio-images-default');
	        }

	        // Get current gallery settings.
	        $settings = get_post_meta( $post_id, 'portfolio-wp-settings', true );
	        if ( is_array( $settings ) ) {
	        	$igp_wp_helper['settings'] = wp_parse_args( $settings, Portfolio_WP_CPT_Fields_Helper::get_defaults() );
	        }else{
	        	$igp_wp_helper['settings'] = Portfolio_WP_CPT_Fields_Helper::get_defaults();
	        }

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'jquery-ui',                  IGP_WP_ASSETS . 'css/jquery-ui.min.css', null, IGP_WP_CURRENT_VERSION );
			wp_enqueue_style( 'portfolio-wp-cpt-',           IGP_WP_ASSETS . 'css/portfolio-wp-cpt.css', null, IGP_WP_CURRENT_VERSION );
			wp_enqueue_style( 'bootstrap-css', IGP_WP_ASSETS . 'css/bootstrap.css', null, IGP_WP_CURRENT_VERSION );
			
			/*fontawesome*/
			wp_enqueue_style('igp-font-awesome-5.0.8', IGP_WP_ASSETS.'css/font-awesome-latest/css/fontawesome-all.min.css');

			wp_enqueue_script( 'portfolio-wp-resize-senzor', IGP_WP_ASSETS . 'js/resizesensor.js', array( 'jquery' ), IGP_WP_CURRENT_VERSION, true );
			wp_enqueue_script( 'portfolio-wp-packery',       IGP_WP_ASSETS . 'js/packery.min.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-droppable', 'jquery-ui-resizable', 'jquery-ui-draggable' ), IGP_WP_CURRENT_VERSION, true );
			wp_enqueue_script( 'portfolio-wp-settings',      IGP_WP_ASSETS . 'js/wp-portfolio-wp-settings.js', array( 'jquery', 'jquery-ui-slider', 'wp-color-picker', 'jquery-ui-sortable' ), IGP_WP_CURRENT_VERSION, true );
			wp_enqueue_script( 'portfolio-wp-save',          IGP_WP_ASSETS . 'js/wp-portfolio-wp-save.js', array(), IGP_WP_CURRENT_VERSION, true );
			wp_enqueue_script( 'portfolio-wp-items',         IGP_WP_ASSETS . 'js/wp-portfolio-wp-items.js', array(), IGP_WP_CURRENT_VERSION, true );
			wp_enqueue_script( 'portfolio-wp-modal',         IGP_WP_ASSETS . 'js/wp-portfolio-wp-modal.js', array(), IGP_WP_CURRENT_VERSION, true );
			wp_enqueue_script( 'portfolio-wp-upload-media',        IGP_WP_ASSETS . 'js/wp-portfolio-wp-upload.js', array(), IGP_WP_CURRENT_VERSION, true );
			wp_enqueue_script( 'portfolio-wp-gallery',       IGP_WP_ASSETS . 'js/wp-portfolio-wp-gallery.js', array(), IGP_WP_CURRENT_VERSION, true );
			wp_enqueue_script( 'portfolio-wp-conditions',    IGP_WP_ASSETS . 'js/wp-portfolio-wp-conditions.js', array(), IGP_WP_CURRENT_VERSION, true );

			do_action( 'igp_wp_scripts_before_wp_igp_wp' );

			wp_enqueue_script( 'portfolio-wp', IGP_WP_ASSETS . 'js/wp-portfolio-wp.js', array(), IGP_WP_CURRENT_VERSION, true );
			wp_localize_script( 'portfolio-wp', 'PortfolioWPHelper', $igp_wp_helper );

			do_action( 'igp_wp_scripts_after_wp_portfolio-wp' );

		}
	}


    // loading language files
    public function portfolio_load_plugin_textdomain() {
        $rs = load_plugin_textdomain('portfolio', FALSE, basename(dirname(__FILE__)) . '/languages/');
    }

    
    public function __construct() {
        
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

        
        //loading plugin translation files
        add_action('plugins_loaded', array($this, 'portfolio_load_plugin_textdomain'));

        if ( is_admin() ) {
            $plugin = plugin_basename(__FILE__);
            
        }
    }

}

function scrape_instagram($username , $access_mode ) {

	$username = trim(strtolower($username));

	switch ($access_mode) {
	  case 'hashtag':
		  $url              = 'https://instagram.com/explore/tags/' . str_replace('#', '', $username);
		  $transient_prefix = 'h';
		  break;
	  case 'username':
		  $url              = 'https://instagram.com/' . str_replace('@', '', $username);
		  $transient_prefix = 'u';
		  break;
	  default:
		  $url              = 'https://instagram.com/' . str_replace('@', '', $username);
		  $transient_prefix = 'u';
		  break;
	}

	
	if (false === ($instagram = get_transient('insta-a10-' . $transient_prefix . '-' . sanitize_title_with_dashes($username)))) {
		
		try {
			$remote = wp_remote_get($url);
		} catch (Exception $e) {

		}

		
		if (is_wp_error($remote)) {
			return new WP_Error('site_down', esc_html__('Unable to communicate with Instagram.', 'wp-instagram-widget'));
		}

		if (200 !== wp_remote_retrieve_response_code($remote)) {
			return new WP_Error('invalid_response', esc_html__('Instagram did not return a 200.', 'wp-instagram-widget'));
		}

		$shards      = explode('window._sharedData = ', $remote['body']);
		$insta_json  = explode(';</script>', $shards[1]);
		$insta_array = json_decode($insta_json[0], true);



		if (! $insta_array) {
			return new WP_Error('bad_json', esc_html__('Instagram has returned invalid data.', 'wp-instagram-widget'));
		}

		if (isset($insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'])) {
			$images = $insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];
		} elseif (isset($insta_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'])) {
			$images = $insta_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'];
		} else {
			return new WP_Error('bad_json_2', esc_html__('Instagram has returned invalid data.', 'wp-instagram-widget'));
		}

		if (! is_array($images)) {
			return new WP_Error('bad_array', esc_html__('Instagram has returned invalid data.', 'wp-instagram-widget'));
		}

		$instagram = array();


		foreach ($images as $image) {
			if (true === $image['node']['is_video']) {
				$type = 'video';
			} else {
				$type = 'image';
			}

			$caption = __('Instagram Image', 'wp-instagram-widget');
			if (! empty($image['node']['edge_media_to_caption']['edges'][0]['node']['text'])) {
				$caption = wp_kses($image['node']['edge_media_to_caption']['edges'][0]['node']['text'], array());
			}

			$instagram[] = array(
			  'description' => $caption,
			  'link'        => trailingslashit('//instagram.com/p/' . $image['node']['shortcode']),
			  'time'        => $image['node']['taken_at_timestamp'],
			  'comments'    => $image['node']['edge_media_to_comment']['count'],
			  'likes'       => $image['node']['edge_liked_by']['count'],
			  'thumbnail'   => preg_replace('/^https?\:/i', '', $image['node']['thumbnail_resources'][0]['src']),
			  'small'       => preg_replace('/^https?\:/i', '', $image['node']['thumbnail_resources'][2]['src']),
			  'large'       => preg_replace('/^https?\:/i', '', $image['node']['thumbnail_resources'][4]['src']),
			  'original'    => preg_replace('/^https?\:/i', '', $image['node']['display_url']),
			  'type'        => $type,
			  );
		} // End foreach().

		// do not set an empty transient - should help catch private or empty accounts.
		if (! empty($instagram)) {
			$instagram = base64_encode(serialize($instagram));
			set_transient('insta-a10-' . $transient_prefix . '-' . sanitize_title_with_dashes($username), $instagram, apply_filters('null_instagram_cache_time', HOUR_IN_SECONDS * 2));
		}
	}
			
	if (! empty($instagram)) {
		return unserialize(base64_decode($instagram));
	} else {
		return new WP_Error('no_images', esc_html__('Instagram did not return any images.', 'wp-instagram-widget'));
	}
}


function images_only($media_item) {
	if ('image' === $media_item['type']) {
		return true;
	}

	return false;
}
