<?php
/**
 * The cpt plugin class.
 *
 * This is used to define the custom post type that will be used for galleries
 *
 * @since      1.0.1
 */
class Portfolio_WP_CPT {
    
	private $labels    = array();
	private $args      = array();
	private $metaboxes = array();
	private $cpt_name;
    private $builder;    
    
	public function __construct() {
        $this->labels = apply_filters('igp_wp_cpt_labels', array(
            'singular_name'         => esc_html__( 'Instagram Gallery Portfolio', igp_wp ),
			'menu_name'             => esc_html__( 'Instagram Gallery Portfolio', igp_wp ),
			'name_admin_bar'        => esc_html__( 'Instagram Gallery Portfolio', igp_wp ),
			'archives'              => esc_html__( 'Item Archives', igp_wp ),
			'attributes'            => esc_html__( 'Item Attributes', igp_wp ),
			'parent_item_colon'     => esc_html__( 'Parent Item:', igp_wp ),
			'all_items'             => esc_html__( 'Galleries', igp_wp ),
			'add_new_item'          => esc_html__( 'Add New Item', igp_wp ),
			'add_new'               => esc_html__( 'Add New', igp_wp ),
			'new_item'              => esc_html__( 'New Item', igp_wp ),
			'edit_item'             => esc_html__( 'Edit Item', igp_wp ),
			'update_item'           => esc_html__( 'Update Item', igp_wp ),
			'view_item'             => esc_html__( 'View Item', igp_wp ),
			'view_items'            => esc_html__( 'View Items', igp_wp ),
			'search_items'          => esc_html__( 'Search Item', igp_wp ),
			'not_found'             => esc_html__( 'Not found', igp_wp ),
			'not_found_in_trash'    => esc_html__( 'Not found in Trash', igp_wp ),
			'featured_image'        => esc_html__( 'Featured Image', igp_wp ),
			'set_featured_image'    => esc_html__( 'Set featured image', igp_wp ),
			'remove_featured_image' => esc_html__( 'Remove featured image', igp_wp ),
			'use_featured_image'    => esc_html__( 'Use as featured image', igp_wp ),
			'insert_into_item'      => esc_html__( 'Insert into item', igp_wp ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this item', igp_wp ),
			'items_list'            => esc_html__( 'Items list', igp_wp ),
			'items_list_navigation' => esc_html__( 'Items list navigation', igp_wp ),
			'filter_items_list'     => esc_html__( 'Filter items list', igp_wp ),
        ));
        $this->args = apply_filters( 'igp_wp_cpt_args', array(
			'label'                 => esc_html__( 'Instagram Gallery Portfolio', igp_wp ),
			'description'           => esc_html__( 'Instagram Gallery Portfolio Post Type Description.', igp_wp ),
			'supports'              => array( 'title' ),
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-portfolio',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'rewrite'               => false,
			'show_in_rest'          => true,
        ) );
        
        $this->metaboxes = apply_filters( 'igp_wp_cpt_metaboxes', array(
			// 'portfolio-wp-builder' => array(
			// 	'title' => esc_html__( 'Gallery Images', igp_wp ),
			// 	'callback' => 'output_portfolio_builder',
			// 	'context' => 'normal',
			// ),
			'portfolio-wp-settings' => array(
				'title' => esc_html__( 'Settings', igp_wp ),
				'callback' => 'output_gallery_settings',
				'context' => 'normal',
			),
			 'portfolio-wp-shortcode' => array(
				'title' => esc_html__( 'Shortcode', igp_wp ),
			 	'callback' => 'output_gallery_shortcode',
			 	'context' => 'side',
			 	'priority' => 'default',
			 ),
        ) );
  
		$this->cpt_name = apply_filters( 'igp_wp_cpt_name', 'igp_wp' );

        add_action( 'init', array( $this, 'register_cpt' ) );

        /* Fire our meta box setup function on the post editor screen. */
		add_action( 'load-post.php', array( $this, 'meta_boxes_setup' ) );
        add_action( 'load-post-new.php', array( $this, 'meta_boxes_setup' ) );
        
        
		// Post Table Columns
		add_filter( "manage_{$this->cpt_name}_posts_columns", array( $this, 'add_columns' ) );
		add_action( "manage_{$this->cpt_name}_posts_custom_column" , array( $this, 'output_column' ), 10, 2 );

		/* Load Fields Helper */
		require_once IGP_WP_ADMIN . 'class-portfolio-wp-cpt-fields-helper.php';

		/* Load Builder */
		require_once IGP_WP_ADMIN . 'class-portfolio-wp-field-builder.php';
		$this->builder = Portfolio_WP_Field_Builder::get_instance();

		/* Initiate Image Resizer */
		$this->resizer = new Portfolio_WP_Image();

	}
    
	public function register_cpt() {

		$args = $this->args;
		$args['labels'] = $this->labels;
		register_post_type( $this->cpt_name, $args );

    }
    public function meta_boxes_setup() {
		/* Add meta boxes on the 'add_meta_boxes' hook. */
  		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
  		/* Save post meta on the 'save_post' hook. */
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 10, 2 );
    }
    
    
	public function add_meta_boxes() {

		global $post;

		foreach ( $this->metaboxes as $metabox_id => $metabox ) {
            
            if ( 'portfolio-wp-shortcode' == $metabox_id && 'auto-draft' == $post->post_status ) {
				break;
			}
            
			add_meta_box(
                $metabox_id,      // Unique ID
			    $metabox['title'],    // Title
			    array( $this, $metabox['callback'] ),   // Callback function
			    'igp_wp',         // Admin page (or post type)
			    $metabox['context'],         // Context
			    'high'         // Priority
			);
		}

    }
    
    public function output_portfolio_builder() {
 		 $this->builder->render( 'gallery' );
	}

	public function output_gallery_settings() {
        $this->builder->render( 'settings' );
        //echo "Hello";
        // die;
	}
	public function output_gallery_shortcode( $post ) {
		$this->builder->render( 'shortcode', $post );
	}

    
	public function save_meta_boxes( $post_id, $post ) {

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) || 'igp_wp' != $post_type->name ) {
			return $post_id;
		}

		// We need to resize our images
		$images = get_post_meta( $post_id, 'portfolio-images', true );
		if ( $images && is_array( $images ) ) {
			if ( isset( $_POST['portfolio-wp-settings']['img_size'] ) && apply_filters( 'igp_wp_resize_images', true, $_POST['portfolio-wp-settings'] ) ) {

				$gallery_type = isset( $_POST['portfolio-wp-settings']['type'] ) ? sanitize_text_field($_POST['portfolio-wp-settings']['type']) : 'creative-gallery';
				$img_size = absint( $_POST['portfolio-wp-settings']['img_size'] );
				
				foreach ( $images as $image ) {
					$grid_sizes = array(
						'width' => isset( $image['width'] ) ? absint( $image['width'] ) : 1,
						'height' => isset( $image['height'] ) ? absint( $image['height'] ) : 1,
					);
					$sizes = $this->resizer->get_image_size( $image['id'], $img_size, $gallery_type, $grid_sizes );
					if ( ! is_wp_error( $sizes ) ) {
						$this->resizer->resize_image( $sizes['url'], $sizes['width'], $sizes['height'] );
					}

				}

			}
		}

		if ( isset( $_POST['portfolio-wp-settings'] ) ) {

			
			$fields_with_tabs = Portfolio_WP_CPT_Fields_Helper::get_fields( 'all' );

			// Here we will save all our settings
			$igp_wp_settings = array();

			// We will save only our settings.
			foreach ( $fields_with_tabs as $tab => $fields ) {

			    // We will iterate throught all fields of current tab
				foreach ( $fields as $field_id => $field ) {

					if ( isset( $_POST['portfolio-wp-settings'][ $field_id ] ) ) {

						// Values for selects
						$lightbox_values = apply_filters( 'igp_wp_lightbox_values', array( 'no-link', 'direct', 'lightbox2', 'attachment-page' ) );
						$effect_values   = apply_filters( 'igp_wp_effect_values', array( 'none', 'pufrobo' ) );


						switch ( $field_id ) {
							case 'description':
								$igp_wp_settings[ $field_id ] = wp_filter_post_kses( $_POST['portfolio-wp-settings'][ $field_id ] );
								break;
							case 'img_size':
							case 'margin':
							case 'randomFactor':
							case 'captionFontSize':
							case 'titleFontSize':
							case 'loadedScale':
							case 'borderSize':
							case 'borderRadius':
							case 'shadowSize':
								$igp_wp_settings[ $field_id ] = absint( $_POST['portfolio-wp-settings'][ $field_id ] );
								break;
							case 'lightbox' :
								if ( in_array( $_POST['portfolio-wp-settings'][ $field_id ], $lightbox_values ) ) {
									$igp_wp_settings[ $field_id ] = sanitize_text_field( $_POST['portfolio-wp-settings'][ $field_id ] );
								}else{
									$igp_wp_settings[ $field_id ] = 'lightbox2';
								}
								break;
							
							case 'shuffle' :
								if ( isset( $_POST['portfolio-wp-settings'][ $field_id ] ) ) {
									$igp_wp_settings[ $field_id ] = '1';
								}else{
									$igp_wp_settings[ $field_id ] = '0';
								}
								break;
							case 'captionColor':
							case 'borderColor':
							case 'shadowColor':
								$igp_wp_settings[ $field_id ] = sanitize_hex_color( $_POST['portfolio-wp-settings'][ $field_id ] );
								break;
							case 'Effect' :
								if ( in_array( $_POST['portfolio-wp-settings'][ $field_id ], $effect_values ) ) {
									$igp_wp_settings[ $field_id ] = $_POST['portfolio-wp-settings'][ $field_id ];
								}else{
									$igp_wp_settings[ $field_id ] = 'pufrobo';
								}
								break;
							default:
								if( is_array( $_POST['portfolio-wp-settings'][ $field_id ] ) ){
									$sanitized = array_map( 'sanitize_text_field', $_POST['portfolio-wp-settings'][ $field_id ] );
									$igp_wp_settings[ $field_id ] = apply_filters( 'igp_wp_settings_field_sanitization', $sanitized, $_POST['portfolio-wp-settings'][ $field_id ] ,$field_id, $field );
								}else{
									$igp_wp_settings[ $field_id ] = apply_filters( 'igp_wp_settings_field_sanitization', sanitize_text_field( $_POST['portfolio-wp-settings'][ $field_id ] ), $_POST['portfolio-wp-settings'][ $field_id ] ,$field_id, $field );
								}

								break;
						}

					}else{
						if ( 'toggle' == $field['type'] ) {
							$igp_wp_settings[ $field_id ] = '0';
						}else{
							$igp_wp_settings[ $field_id ] = '';
						}
					}

				}

			}

			// Save the value of helpergrid
			if ( isset( $_POST['portfolio-wp-settings']['helpergrid'] ) ) {
				$igp_wp_settings['helpergrid'] = 1;
			}else{
				$igp_wp_settings['helpergrid'] = 0;
			}

			// print_r($_POST['portfolio-wp-settings']);
            // die;

			
			// Add settings to gallery meta
			update_post_meta( $post_id, 'portfolio-wp-settings', $igp_wp_settings );

		}

	}

    

    public function add_columns( $columns ){

		$date = $columns['date'];
		unset( $columns['date'] );
		$columns['shortcode'] = esc_html__( 'Shortcode', igp_wp );
		$columns['date'] = $date;
		return $columns;

	}

	public function output_column( $column, $post_id ){

		if ( 'shortcode' == $column ) {
			$shortcode = '[igp-wp id="' . $post_id . '"]';
			echo '<input type="text" value="' . esc_attr( $shortcode ) . '"  onclick="select()" readonly style="width:32%;">';
            /*echo '<a href="#" class="copy-portfolio-wp-shortcode button button-primary" style="margin-left:15px;">'.esc_html__('Copy shortcode',igp_wp).'</a><span style="margin-left:15px;"></span>';*/
		}

	}

}

