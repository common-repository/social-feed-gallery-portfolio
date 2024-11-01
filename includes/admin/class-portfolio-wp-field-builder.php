<?php

/**
 * 
 */
class Portfolio_WP_Field_Builder {

	function __construct() {

		/* Add templates for our plugin */
		add_action( 'admin_footer', array( $this, 'print_igp_wp_templates' ) );

	}

	/**
	 * Get an instance of the field builder
	 */
	public static function get_instance() {
		static $inst;
		if ( ! $inst ) {
			$inst = new Portfolio_WP_Field_Builder();
		}
		return $inst;
	}

	public function get_id(){
		global $id, $post;

        // Get the current post ID. If ajax, grab it from the $_POST variable.
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX && array_key_exists( 'post_id', $_POST ) ) {
            $post_id = absint( $_POST['post_id'] );
        } else {
            $post_id = isset( $post->ID ) ? $post->ID : (int) $id;
        }

        return $post_id;
	}

	/**
     * Helper method for retrieving settings values.
     *
     * @since 1.0.1
     *
     * @global int $id        The current post ID.
     * @global object $post   The current post object.
     * @param string $key     The setting key to retrieve.
     * @param string $default A default value to use.
     * @return string         Key value on success, empty string on failure.
     */
    public function get_setting( $key, $default = false ) {

        // Get config
        $settings = get_post_meta( $this->get_id(), 'portfolio-wp-settings', true );

        // Check config key exists
        if ( isset( $settings[ $key ] ) ) {
            return $settings[ $key ];
        } else {
            return $default ? $default : '';
        }

    }

	public function render( $metabox, $post = false ) {

		switch ( $metabox ) {
			case 'gallery':
				$this->_render_gallery_metabox();
				break;
			case 'settings':
				$this->_render_settings_metabox();
				break;
			case 'shortcode':
				$this->_render_shortcode_metabox( $post );
				break;
			default:
				do_action( "igp_wp_metabox_fields_{$metabox}" );
				break;
		}

	}

	/* Create HMTL for gallery metabox */
	private function _render_gallery_metabox() {

		$images = get_post_meta( $this->get_id(), 'portfolio-images', true );
		$helper_guidelines = $this->get_setting( 'helpergrid' );

		$max_upload_size = wp_max_upload_size();
	    if ( ! $max_upload_size ) {
	        $max_upload_size = 0;
	    }

		echo '<div class="container-fluid portfolio-wp-uploader-container">';
		echo '<div class="row portfolio-wp-upload-actions">';
		echo '<div class="col-lg-7 upload-info-container">';
		echo '<div class="upload-info">';
		echo sprintf( __( '<b><h2>Drag and drop</b> files here (max %s per file), or <b>drag images around to change their order</h2></b>', igp_wp ), esc_html( size_format( $max_upload_size ) ) );
		echo '</div>';
		echo '<div class="upload-progress">';
		echo '<p class="portfolio-wp-upload-numbers">' . esc_html__( 'Uploading image', igp_wp ) . ' <span class="portfolio-wp-current"></span> ' . esc_html__( 'of', igp_wp ) . ' <span class="portfolio-wp-total"></span>';
		echo '<div class="portfolio-wp-progress-bar"><div class="portfolio-wp-progress-bar-inner"></div></div>';
		echo '</div>';
		echo '</div>';
		echo '<div class="col-lg-5">';
		echo '<a href="#" id="portfolio-wp-uploader-browser"  class="btn btn-md btn-secondary btn-block">' . esc_html__( 'Upload Image Files', igp_wp ) . '</a><a href="#" id="portfolio-wp-gallery"  class="btn btn-md btn-primary btn-block">' . esc_html__( 'Select Image from Library', igp_wp ) . '</a>';
		echo '</div>';
		echo '</div>';
		echo '<div id="portfolio-wp-uploader-container" class="row portfolio-wp-uploader-inline">';
			echo '<div class="portfolio-wp-error-container"></div>';
			echo '<div class="col-sm-12 portfolio-wp-uploader-inline-content">';
				echo '<h2 class="portfolio-wp-upload-message"><span class="dashicons dashicons-upload"></span>' . esc_html__( 'Drag & Drop files here!', igp_wp ) . '</h2>';
				echo '<div id="portfolio-wp-grid" style="display:none"></div>';
			echo '</div>';
			echo '<div id="portfolio-wp-dropzone-container"><div class="portfolio-wp-uploader-window-content"><h1>' . esc_html__( 'Drop files to upload', igp_wp ) . '</h1></div></div>';
		echo '</div>';

		echo '</div>';
	}

	/* Create HMTL for settings metabox */
	private function _render_settings_metabox() {
		$tabs = Portfolio_WP_CPT_Fields_Helper::get_tabs();

		// Sort tabs based on priority.
		uasort( $tabs, array( 'Portfolio_WP_Helper', 'sort_data_by_priority' ) );

		$tabs_html = '';
		$tabs_content_html = '';
		$first = true;

		// Generate HTML for each tab.
		foreach ( $tabs as $tab_id => $tab ) {
			$tab['id'] = $tab_id;
			$tabs_html .= $this->_render_tab( $tab, $first );

			$fields = Portfolio_WP_CPT_Fields_Helper::get_fields( $tab_id );
			// Sort fields based on priority.
			uasort( $fields, array( 'Portfolio_WP_Helper', 'sort_data_by_priority' ) );

			$current_tab_content = '<div id="portfolio-wp-' . esc_attr( $tab['id'] ) . '" class="' . ( $first ? 'active-tab' : '' ) . '">';

			// Check if our tab have title & description
			if ( isset( $tab['title'] ) || isset( $tab['description'] ) ) {
				$current_tab_content .= '<div class="tab-content-header">';
				$current_tab_content .= '<div class="tab-content-header-title">';
				if ( isset( $tab['title'] ) && '' != $tab['title'] ) {
					$current_tab_content .= '<h2>' . esc_html( $tab['title'] ) . '</h2>';
				}
				if ( isset( $tab['description'] ) && '' != $tab['description'] ) {
					$current_tab_content .= '<div class="tab-header-tooltip-container portfolio-wp-tooltip"><span><i class="fa fa-lightbulb"></i></span>';
					$current_tab_content .= '<div class="tab-header-description portfolio-wp-tooltip-content">' . wp_kses_post( $tab['description'] ) . '</div>';
					$current_tab_content .= '</div>';
				}
				$current_tab_content .= '</div>';

				$current_tab_content .= '</div>';
			}

			// Generate all fields for current tab
			$current_tab_content .= '<div class="form-table-wrapper">';
			$current_tab_content .= '<table class="form-table"><tbody>';
			foreach ( $fields as $field_id => $field ) {
				$field['id'] = $field_id;
				$current_tab_content .= $this->_render_row( $field );
			}
			$current_tab_content .= '</tbody></table>';
			// Filter to add extra content to a specific tab
			$current_tab_content .= apply_filters( 'igp_wp_' . $tab_id . '_tab_content', '' );
			$current_tab_content .= '</div>';
			$current_tab_content .= '</div>';
			$tabs_content_html .= $current_tab_content;

			if ( $first ) {
				$first = false;
			}

		}

		$html = '<div class="portfolio-wp-settings-container"><div class="portfolio-wp-tabs">%s</div><div class="portfolio-wp-tabs-content">%s</div>';
		printf( $html, $tabs_html, $tabs_content_html );
	}

	/* Create HMTL for shortcode metabox */
	private function _render_shortcode_metabox( $post ) {
		$shortcode = '[igp-wp id="' . $post->ID . '"]';
		echo '<input type="text" style="width:100%;" value="' . esc_attr( $shortcode ) . '"  onclick="select()" readonly>';
		// Add Copy Shortcode button
        echo '<a href="#" class="copy-portfolio-wp-shortcode button button-primary" style="margin-top:10px;">'.esc_html__('Copy Shortcode',igp_wp).'</a><span style="margin-left:15px;"></span>';
	}

	/* Create HMTL for a tab */
	private function _render_tab( $tab, $first = false ) {
		$icon = '';
		$badge = '';

		if ( isset( $tab['icon'] ) ) {
			$icon = '<i class="' . esc_attr( $tab['icon'] ) . '"></i>';
		}

		if ( isset( $tab['badge'] ) ) {
			$badge = '<sup>' . esc_html( $tab['badge'] ) . '</sup>';
		}
		return '<div class="portfolio-wp-tab' . ( $first ? ' active-tab' : '' ) . ' portfolio-wp-' . esc_attr( $tab['id'] ) . '" data-tab="portfolio-wp-' . esc_attr( $tab['id'] ) . '">' . $icon . wp_kses_post( $tab['label'] ) . $badge . '</div>';
	}

	/* Create HMTL for a row */
	private function _render_row( $field ) {
		$format = '<tr data-container="' . esc_attr( $field['id'] ) . '"><th scope="row"><label>%s</label>%s</th><td>%s</td></tr>';

		if ( 'textarea' == $field['type'] || 'custom_code' == $field['type'] ) {
			$format = '<tr data-container="' . esc_attr( $field['id'] ) . '"><td colspan="2"><label class="th-label">%s</label>%s<div>%s</div></td></tr>';
		}

		$format = apply_filters( "igp_wp_field_type_{$field['type']}_format", $format, $field );

		$default = '';

		// Check if our field have a default value
		if ( isset( $field['default'] ) ) {
			$default = $field['default'];
		}

		// Generate tooltip
		$tooltip = '';
		if ( isset( $field['description'] ) && '' != $field['description'] ) {
			$tooltip .= '<div class="portfolio-wp-tooltip"><span><i class="fa fa-lightbulb"></i></span>';
			$tooltip .= '<div class="portfolio-wp-tooltip-content">' . wp_kses_post( $field['description'] ) . '</div>';
			$tooltip .= '</div>';
		}

		// Get the current value of the field
		$value = $this->get_setting( $field['id'], $default );
		return sprintf( $format, wp_kses_post( $field['name'] ), $tooltip, $this->_render_field( $field, $value ) );
	}

	/* Create HMTL for a field */
	private function _render_field( $field, $value = '' ) {
		$html = '';

		switch ( $field['type'] ) {
			case 'text':
				$html = '<input type="text" class="col-sm-4 regular-text" name="portfolio-wp-settings[' . esc_attr( $field['id'] ) . ']" data-setting="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $value ) . '">';
				break;
			case 'select' :
				$html = '<select name="portfolio-wp-settings[' . esc_attr( $field['id'] ) . ']" data-setting="' . esc_attr( $field['id'] ) . '" style="max-width:33%;" class="col-sm-4 regular-text">';
				foreach ( $field['values'] as $key => $option ) {
					if ( is_array( $option ) ) {
						$html .= '<optgroup label="' . esc_attr( $key ) . '">';
						foreach ( $option as $key_subvalue => $subvalue ) {
							$html .= '<option value="' . esc_attr( $key_subvalue ) . '" ' . selected( $key_subvalue, $value, false ) . '>' . esc_html( $subvalue ) . '</option>';
						}
						$html .= '</optgroup>';
					}else{
						$html .= '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $value, false ) . '>' . esc_html( $option ) . '</option>';
					}
				}
				if ( isset( $field['disabled'] ) && is_array( $field['disabled'] ) ) {
					$html .= '<optgroup label="' . esc_attr( $field['disabled']['title'] ) . '">';
					foreach ( $field['disabled']['values'] as $key => $disabled ) {
						$html .= '<option value="' . esc_attr( $key ) . '" disabled >' . esc_html( $disabled ) . '</option>';
					}
					$html .= '</optgroup>';
				}
				$html .= '</select>';
				break;
			case 'ui-slider':
				$min  = isset( $field['min'] ) ? $field['min'] : 0;
				$max  = isset( $field['max'] ) ? $field['max'] : 100;
				$step = isset( $field['step'] ) ? $field['step'] : 1;
				if ( '' === $value ) {
					if ( isset( $field['default'] ) ) {
						$value = $field['default'];
					}else{
						$value = $min;
					}
				}
				$attributes = 'data-min="' . esc_attr( $min ) . '" data-max="' . esc_attr( $max ) . '" data-step="' . esc_attr( $step ) . '"';
				$html .= '<div class="col-sm-4 slider-container portfolio-wp-ui-slider-container">';
					$html .= '<input readonly="readonly" data-setting="' . esc_attr( $field['id'] ) . '"  name="portfolio-wp-settings[' . esc_attr( $field['id'] ) . ']" type="text" class="col-sm-4 rl-slider portfolio-wp-ui-slider-input" id="input_' . esc_attr( $field['id'] ) . '" value="' . $value . '" ' . $attributes . '/>';
					$html .= '<div id="slider_' . esc_attr( $field['id'] ) . '" class="ss-slider portfolio-wp-ui-slider"></div>';
				$html .= '</div>';
				break;
			case 'color' :
				$html .= '<div class="portfolio-wp-colorpickers">';
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" class="col-sm-4 portfolio-wp-color" data-setting="' . esc_attr( $field['id'] ) . '" name="portfolio-wp-settings[' . esc_attr( $field['id'] ) . ']" value="' . esc_attr( $value ) . '">';
				$html .= '</div>';
				break;
			case "toggle":
				$html .= '<div class="portfolio-wp-toggle">';
					$html .= '<input class="portfolio-wp-toggle__input" type="checkbox" data-setting="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" name="portfolio-wp-settings[' . esc_attr( $field['id'] ) . ']" value="1" ' . checked( 1, $value, false ) . '>';
					$html .= '<div class="portfolio-wp-toggle__items">';
						$html .= '<span class="portfolio-wp-toggle__track"></span>';
						$html .= '<span class="portfolio-wp-toggle__thumb"></span>';
						$html .= '<svg class="portfolio-wp-toggle__off" width="6" height="6" aria-hidden="true" role="img" focusable="false" viewBox="0 0 6 6"><path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path></svg>';
						$html .= '<svg class="portfolio-wp-toggle__on" width="2" height="6" aria-hidden="true" role="img" focusable="false" viewBox="0 0 2 6"><path d="M0 0h2v6H0z"></path></svg>';
					$html .= '</div>';
				$html .= '</div>';
				break;
			case "custom_code":
				$html = '<div class="portfolio-wp-code-editor" data-syntax="' . esc_attr( $field['syntax'] ) . '">';
				$html .= '<textarea data-setting="' . esc_attr( $field['id'] ) . '" name="portfolio-wp-settings[' . esc_attr( $field['id'] ) . ']" id="portfolio-wp-' . esc_attr( $field['id'] ) . '" class="large-text code"  rows="10" cols="50">' . wp_kses_post($value) . '</textarea>';
				$html .= '</div>';
				break;
			
			default:
				/* Filter for render custom field types */
				$html = apply_filters( "igp_wp_render_{$field['type']}_field_type", $html, $field, $value );
				break;
		}

		return $html;

	}

	public function print_igp_wp_templates() { 
		include 'portfolio-wp-js-templates.php';
	}

}
?>
