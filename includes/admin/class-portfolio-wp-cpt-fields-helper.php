<?php

/**
 *
 */
class Portfolio_WP_CPT_Fields_Helper {

	public static function get_tabs() {

		$general_description = '<p>' . esc_html__( 'Select Either Creative or Custom grid and Select lightbox and links style', igp_wp ) . '</p>';
		$caption_description = '<p>' . esc_html__( 'The settings shown below adjust how the image title/description will appear on the front-end gallery layout', igp_wp ) . '</p>';
		$style_description = '<p>' . esc_html__( 'Style the Gallery Images.', igp_wp ) . '</p>';
		$customizations_description = '<p>' . esc_html__( 'Add custom CSS to gallery Layout for advanced modifications', igp_wp ) . '</p>';
		
		return apply_filters( 'igp_wp_gallery_tabs', array(
			'general' => array(
				'label'       => esc_html__( 'General', igp_wp ),
				'title'       => esc_html__( 'General Settings', igp_wp ),
				'description' => $general_description,
				"icon"        => "dashicons dashicons-admin-generic",
				'priority'    => 10,
			),
			
			'captions' => array(
				'label'       => esc_html__( 'Captions', igp_wp ),
				'title'       => esc_html__( 'Caption Settings', igp_wp ),
				'description' => $caption_description,
				"icon"        => "dashicons dashicons-menu-alt3",
				'priority'    => 20,
			),
			
			'style' => array(
				'label'       => esc_html__( 'Style', igp_wp ),
				'title'       => esc_html__( 'Style Settings', igp_wp ),
				'description' => $style_description,
				"icon"        => "dashicons dashicons-admin-customizer",
				'priority'    => 70,
			),
			
			'customizations' => array(
				'label'       => esc_html__( 'Custom CSS', igp_wp ),
				'title'       => esc_html__( 'Custom CSS', igp_wp ),
				'description' => $customizations_description,
				"icon"        => "dashicons dashicons-admin-tools",
				'priority'    => 90,
			),
            
		) );

	}

	

	public static function get_fields( $tab ) {

		$fields = apply_filters( 'igp_wp_gallery_fields', 
			array(
				
				'general' => array(
					"access_mode"   => array(
						"name"		  => esc_html__('Select Access Mode', igp_wp ),
						"type"   	  => "select",
						"description" => esc_html__('Select the type of Access Mode which you want to use', igp_wp),
						"default" 	  => 'hashtag',
						"values"      => array(
							'hashtag'       	=> esc_html__( 'Hash Tag', igp_wp ),
							'username'		  	=> esc_html__( 'Username', igp_wp ),	
						),
						"priority" 	  => 5,	
					),
					'access_value'    => array (
						"name"        => esc_html__( 'Access Value', igp_wp ),
						"type"        => "text",
						"description" => esc_html__( 'Enter Access Value Of Your Profile.', igp_wp ),
						'default'     => 'landscape_nightscape',
						'priority' => 10,
					),
					'type'           => array (
						"name"        => esc_html__( 'Gallery Type', igp_wp ),
						"type"        => "select",
						"description" => esc_html__( 'Select the type of gallery which you want to use.', igp_wp ),
						'default'     => 'custom-grid',
						"values"      => array(
							'creative-gallery' => esc_html__( 'Creative Gallery', igp_wp ),
							'custom-grid'      => esc_html__( 'Custom Grid', igp_wp ),
						),
						'priority' => 10,
					),
					'img_count'    => array (
						"name"        => esc_html__( 'Image Count', igp_wp ),
						"type"        => "text",
						"description" => esc_html__( 'Enter Image Count Of Your Profile.', igp_wp ),
						'default'     => '9',
						'priority' => 11,
					),
					"layout" => array(
						"name"		  => esc_html__('Select Layout', igp_wp ),
						"type"   	  => "select",
						"description" => esc_html__('Select the type of Portfolio Layout which you want to use', igp_wp),
						"default" 	  => 1,
						"values"      => array(
							'1'		  => esc_html__( 'Basic Layout', igp_wp ),
							'2'       => esc_html__( 'Inner Layout 1', igp_wp ),
							'3'		  => esc_html__( 'Inner Layout 2', igp_wp ),	
							'4' 	  => esc_html__( 'Inner layout 3', igp_wp ),
							'5'		  => esc_html__( 'Outer Layout 1', igp_wp ),	
							'6'		  => esc_html__( 'Outer Layout 2', igp_wp ),	
						),
						"priority" 	  => 20,		
					),
					"item_margin"         => array(
						"name"        => esc_html__( 'Margin', igp_wp ),
						"type"        => "ui-slider",
						"description" => esc_html__( 'Set the spacing between gallery images', igp_wp ),
						"min"         => 0,
						"max"         => 100,
						"step"        => 1,
						"default"     => 8,
						'priority'    => 30,
					),
					"select_column"   => array(
						"name"		  => esc_html__('Select Column Layout', igp_wp ),
						"type"   	  => "select",
						"description" => esc_html__('Select the type of Column Layout which you want to use', igp_wp),
						"default" 	  => 4,
						"values"      => array(
							'6' 	  => esc_html__( '2 Column Layout', igp_wp ),
							'4'       => esc_html__( '3 Column Layout', igp_wp ),
							'3'		  => esc_html__( '4 Column Layout', igp_wp ),	
						),
						"priority" 	  => 25,	
					),
					"lightbox"       => array(
						"name"        => esc_html__( 'Lightbox &amp; Links', igp_wp ),
						"type"        => "select",
						"description" => esc_html__( 'Select the preferred lightbox and links style', igp_wp ),
						'default'     => 'lightbox2',
						"values"      => array(
								"no-link"         => esc_html__( 'No link', igp_wp ),
								"direct"          => esc_html__( 'Direct link to image', igp_wp ),
								'lightbox2' => esc_html__( 'Lightbox', igp_wp ),
							),
						'priority' => 110,
					),
					"link_target"       => array(
						"name"        => esc_html__( 'Link Target', igp_wp ),
						"type"        => "select",
						"description" => esc_html__( 'Select the preferred Target For Links', igp_wp ),
						'default'     => '',
						"values"      => array(
								""         => esc_html__( 'Same Window', igp_wp ),
								"_blank"   => esc_html__( 'Open in New Window ', igp_wp ),
							),
						'priority' => 120,
					),
				),
				'captions' => array(
					"captionColor"     => array(
						"name"        => esc_html__( 'Caption Color', igp_wp ),
						"type"        => "color",
						"description" => esc_html__( 'Set the caption color', igp_wp ),
						"default"     => "#ffffff",
						'priority'    => 10,
					),
					
					"hide_description"        => array(
						"name"        => esc_html__( 'Hide Caption', igp_wp ),
						"type"        => "toggle",
						"default"     => 0,
						"description" => esc_html__( 'Hide image caption from gallery layout', igp_wp ),
						'priority'    => 50,
					),	
					"captionFontSize"  => array(
						"name"        => esc_html__( 'Caption Font Size', igp_wp ),
						"type"        => "ui-slider",
						"min"         => 0,
						"max"         => 50,
						"default"     => 14,
						"description" => esc_html__( 'Set the caption font size in pixels', igp_wp ),
						'priority'    => 70,
					),
					
				),
			
			'image-loaded-effects' => array(
				"loadedScale"  => array(
					"name"        => esc_html__( 'Scale', igp_wp ),
					"description" => esc_html__( 'Choose a value below 100% for a zoom-in effect. Choose a value over 100% for a zoom-out effect', igp_wp ),
					"type"        => "ui-slider",
					"min"         => 0,
					"max"         => 200,
					"default"     => 100,
					'priority' => 10,
				),
			),
			'hover-effect' => array(
				"effect" => array(
					"name"        => esc_html__( 'Hover effect', igp_wp ),
					"description" => esc_html__( 'Select your preferred hover effect', igp_wp ),
					"type"        => "hover-effect",
					'default'     => 'pufrobo',
					'priority'    => 10,
				),
			),
			'style' => array(
				"borderSize"   => array(
					"name"        => esc_html__( 'Border Size', igp_wp ),
					"type"        => "ui-slider",
					"description" => esc_html__( 'Set the border size of images in gallery layout', igp_wp ),
					"min"         => 0,
					"max"         => 10,
					"default"     => 0,
					'priority'    => 10,
				),
				"borderRadius" => array(
					"name"        => esc_html__( 'Border Radius', igp_wp ),
					"type"        => "ui-slider",
					"description" => esc_html__( 'Set the border radius of the image in gallery layout', igp_wp ),
					"min"         => 0,
					"max"         => 100,
					"default"     => 0,
					'priority'    => 20,
				),
				"borderColor"  => array(
					"name"        => esc_html__( 'Border Color', igp_wp ),
					"type"        => "color",
					"description" => esc_html__( 'Set the Border color of image in gallery layout', igp_wp ),
					"default"     => "#ffffff",
					'priority'    => 30,
				),
				"shadowSize"   => array(
					"name"        => esc_html__( 'Shadow Size', igp_wp ),
					"type"        => "ui-slider",
					"description" => esc_html__( 'Set the image shadows in gallery layout', igp_wp ),
					"min"         => 0,
					"max"         => 20,
					"default"     => 0,
					'priority'    => 40,
				),
				"shadowColor"  => array(
					"name"        => esc_html__( 'Shadow Color', igp_wp ),
					"type"        => "color",
					"description" => esc_html__( 'Set the color of image shadow in gallery layout', igp_wp ),
					"default"     => "#ffffff",
					'priority'    => 50,
				),
			),
			
			'customizations' => array(
				"extra_classes"  => array(
					"name"        => esc_html__( 'Extra Clases', igp_wp ),
					"type"        => "text",
					"description" => '<strong>' . esc_html__( 'Just Enter Css Class You Want Add in Shotcode.', igp_wp ) . '</strong>',
					'priority' => 10,
				),
				"style"  => array(
					"name"        => esc_html__( 'Custom CSS', igp_wp ),
					"type"        => "custom_code",
					"syntax"      => 'css',
					"description" => '<strong>' . esc_html__( 'Just write the code without using the &lt;style&gt;&lt;/style&gt; tags', igp_wp ) . '</strong>',
					'priority' => 20,
				),
			),
		) );

		

		if ( 'all' == $tab ) {
			return $fields;
		}

		if ( isset( $fields[ $tab ] ) ) {
			return $fields[ $tab ];
		} else {
			return array();
		}

	}

	public static function get_defaults() {
		return apply_filters( 'igp_wp_lite_default_settings', array(
            'type'                      => 'custom-grid',
            'width'                     => '100%',
            'height'                    => '800',
            'img_size'                  => 300,
            'margin'                    => '8',
            'lightbox'                  => 'lightbox2',
            'titleColor'                => '#ffffff',
            'captionColor'              => '#ffffff',
            'wp_field_caption'          => 'none',
            'wp_field_title'            => 'none',
            'hide_title'                => 0,
            'hide_description'          => 0,
            'captionFontSize'           => '14',
            'titleFontSize'             => '18',
            'loadedScale'               => '100',
            'borderColor'               => '#ffffff',
            'borderRadius'              => '0',
            'borderSize'                => '0',
            'shadowColor'               => '#ffffff',
            'shadowSize'                => 0,
            'style'                     => '',
            'gutter'                    => 8,
            'helpergrid'                => 0,
        ) );
	}

}
